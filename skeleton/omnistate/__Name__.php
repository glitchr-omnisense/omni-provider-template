<?php

namespace __Namespace__;

use Omnistate\Exception\UnavailableException;
use Omnistate\Model\Company;
use Omnistate\Model\CompanyStatus;
use Omnistate\Registry\CompanyRegistryInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * __Title__, a register of companies:
 *
 *   new __Name__($httpClient, apiKey: '...');
 *
 * The template's register answers omnistate's CompanyRegistryInterface over a
 * JSON API - GET companies/{identifier}, GET companies?q=&limit= - with the
 * family's rule on errors: not known is null (a lookup) or [] (a search), not
 * answering is an UnavailableException. To make it the service's, replace
 * BASE_URI, the identifier's form in supports() (Omnistate\Identifier\ has the
 * SIREN, SIRET and VAT rules), the paths and toCompany()'s fields; implement
 * the other contracts the service answers (VatRegistryInterface,
 * ProfessionalRegistryInterface...: omnistate's docs/providers.md).
 */
final class __Name__ implements CompanyRegistryInterface
{
    public const BASE_URI = 'https://registry.example.org/api/';

    /** Seconds to wait after an HTTP 429 that does not say how long. */
    private const RETRY_AFTER = 60;

    public function __construct(
        private readonly HttpClientInterface $http,
        #[\SensitiveParameter] private readonly ?string $apiKey = null,
        private readonly string $baseUri = self::BASE_URI,
    ) {
    }

    public function name(): string
    {
        return '__name__';
    }

    /** The register's identifiers: nine digits. */
    public function supports(string $identifier): bool
    {
        return 1 === preg_match('/^\d{9}$/', $identifier);
    }

    public function company(string $identifier): ?Company
    {
        $answer = $this->ask('companies/'.rawurlencode($identifier));

        return null === $answer ? null : $this->toCompany($answer);
    }

    public function search(string $query, int $limit = 10): array
    {
        $answer = $this->ask('companies', ['q' => $query, 'limit' => $limit]) ?? [];
        $companies = [];
        foreach (\is_array($answer['results'] ?? null) ? $answer['results'] : [] as $result) {
            if (\is_array($result)) {
                $companies[] = $this->toCompany($result);
            }
        }

        return \array_slice($companies, 0, $limit);
    }

    /**
     * The answer's JSON, null on HTTP 404 or 410 (not known).
     *
     * @param array<string, scalar> $query
     *
     * @return array<mixed>|null
     */
    private function ask(string $path, array $query = []): ?array
    {
        try {
            $response = $this->http->request('GET', rtrim($this->baseUri, '/').'/'.$path, [
                'query' => $query,
                'headers' => array_filter(['Accept' => 'application/json', 'Authorization' => null !== $this->apiKey ? 'Bearer '.$this->apiKey : null]),
                'timeout' => 15,
            ]);
            $status = $response->getStatusCode();
            if (404 === $status || 410 === $status) {
                return null;
            }
            if (429 === $status) {
                $retry = $response->getHeaders(false)['retry-after'][0] ?? '';

                throw new UnavailableException('__Title__: too many calls.', is_numeric($retry) && (int) $retry > 0 ? (int) $retry : self::RETRY_AFTER);
            }
            $body = $response->getContent(false);
        } catch (ExceptionInterface $e) {
            throw new UnavailableException('__Title__ did not answer: '.$e->getMessage(), null, $e);
        }
        if ($status >= 400) {
            throw new UnavailableException(\sprintf('__Title__ answered HTTP %d.', $status));
        }
        $data = json_decode($body, true);
        if (!\is_array($data)) {
            throw new UnavailableException(\sprintf('__Title__ answered what is not JSON: %s', substr(trim($body), 0, 200)));
        }

        return $data;
    }

    /**
     * A company as the register answers it: {"id", "name", "status" ("active", "closed"), "country"}.
     *
     * @param array<mixed> $r
     */
    private function toCompany(array $r): Company
    {
        $identifier = $r['id'] ?? null;
        $name = $r['name'] ?? null;
        if (!\is_scalar($identifier) || '' === (string) $identifier || !\is_string($name) || '' === $name) {
            throw new UnavailableException('__Title__ answered a company without its identifier or its name.');
        }
        $country = $r['country'] ?? null;

        return new Company(
            identifier: (string) $identifier,
            name: $name,
            status: 'closed' === ($r['status'] ?? null) ? CompanyStatus::CLOSED : CompanyStatus::ACTIVE,
            country: \is_string($country) && 1 === preg_match('/^[A-Za-z]{2}$/', $country) ? strtoupper($country) : 'FR',
            source: $this->name(),
            raw: $r,
        );
    }
}
