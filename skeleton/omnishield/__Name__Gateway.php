<?php

namespace __Namespace__;

use Omnishield\ChallengeInterface;
use Omnishield\Exception\InvalidKeyException;
use Omnishield\Exception\ProviderException;
use Omnishield\Http\Answer;
use Omnishield\Model\Attempt;
use Omnishield\Model\Capabilities;
use Omnishield\Model\Verdict;
use Omnishield\Model\Widget;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * __Title__: the provider's widget on the page, its token checked by the
 * provider's verification API - POST secret, response, remoteip; the answer's
 * success, hostname, challenge_ts and error-codes. Replace the addresses, the
 * fields, REASONS (the provider's codes in the family's words) and the
 * Capabilities with the service's: scores and actions where it signs them
 * (omnishield's docs/gateways.md).
 */
final class __Name__Gateway implements ChallengeInterface
{
    public const BASE_URI = 'https://challenges.example.org/api/';
    public const SCRIPT = 'https://challenges.example.org/widget.js';
    public const FIELD = '__name__-response';

    /** The provider's error codes, in the family's words; any other is INVALID. */
    private const REASONS = [
        'missing-input-response' => Verdict::MISSING,
        'invalid-input-response' => Verdict::INVALID,
        'timeout-or-duplicate' => Verdict::SPENT,
    ];

    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly string $siteKey,
        #[\SensitiveParameter] private readonly string $secret,
        private readonly string $script = self::SCRIPT,
        private readonly string $field = self::FIELD,
        private readonly string $baseUri = self::BASE_URI,
    ) {
    }

    public function getName(): string
    {
        return '__name__';
    }

    public function getTitle(): string
    {
        return '__Title__';
    }

    public function capabilities(): Capabilities
    {
        return new Capabilities(challenge: true, thirdParty: true, cookies: false);
    }

    public function widget(?string $action = null): Widget
    {
        $scheme = parse_url($this->script, \PHP_URL_SCHEME);
        $host = parse_url($this->script, \PHP_URL_HOST);

        return new Widget(
            field: $this->field,
            script: $this->script,
            scriptAttributes: ['async' => true, 'defer' => true],
            tag: 'div',
            attributes: ['class' => '__name__-widget', 'data-sitekey' => $this->siteKey],
            siteKey: $this->siteKey,
            thirdParty: true,
            cookies: false,
            origins: \is_string($scheme) && \is_string($host) ? [$scheme.'://'.$host] : [],
        );
    }

    public function verify(Attempt $attempt): Verdict
    {
        if ($attempt->isEmpty()) {
            return Verdict::fail(Verdict::MISSING);
        }

        $answer = Answer::send($this->http, '__name__', 'POST', rtrim($this->baseUri, '/').'/siteverify', [
            'body' => array_filter(['secret' => $this->secret, 'response' => $attempt->token, 'remoteip' => $attempt->ip]),
        ]);
        if (401 === $answer->status || 403 === $answer->status) {
            throw new InvalidKeyException('__name__', \sprintf('HTTP %d: the secret was refused.', $answer->status), (string) $answer->status);
        }
        if (400 <= $answer->status) {
            throw new ProviderException('__name__', \sprintf('HTTP %d: %s', $answer->status, substr(trim($answer->body), 0, 200)), (string) $answer->status);
        }
        $data = $answer->json();
        $codes = array_values(array_map(static fn (mixed $code) => \is_scalar($code) ? (string) $code : '', \is_array($data['error-codes'] ?? null) ? $data['error-codes'] : []));
        if (array_intersect($codes, ['missing-input-secret', 'invalid-input-secret'])) {
            throw new InvalidKeyException('__name__', 'The secret was refused: '.implode(', ', $codes).'.', $codes[0] ?? null);
        }

        $hostname = \is_string($data['hostname'] ?? null) && '' !== $data['hostname'] ? $data['hostname'] : null;
        $at = $this->date($data['challenge_ts'] ?? null);
        if (true !== ($data['success'] ?? null)) {
            $reasons = array_values(array_unique(array_map(static fn (string $code) => self::REASONS[$code] ?? Verdict::INVALID, $codes ?: ['invalid-input-response'])));

            return new Verdict(false, hostname: $hostname, at: $at, reasons: $reasons, codes: $codes);
        }
        if (null !== $attempt->hostname && strtolower($attempt->hostname) !== strtolower((string) $hostname)) {
            return new Verdict(false, hostname: $hostname, at: $at, reasons: [Verdict::HOSTNAME]);
        }

        return new Verdict(true, hostname: $hostname, at: $at);
    }

    private function date(mixed $value): ?\DateTimeImmutable
    {
        try {
            return \is_string($value) && '' !== $value ? new \DateTimeImmutable($value) : null;
        } catch (\Exception) {
            return null;
        }
    }
}
