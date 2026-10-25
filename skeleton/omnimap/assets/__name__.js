/**
 * __Title__'s driver for omnimap's script (assets/omnimap.js of omniscient/omnimap):
 * draws the description __Name__Provider::describe() gave.
 *
 *   import { register } from 'omnimap';
 *   import * as driver from '__package__/assets/__name__.js';
 *   register('__name__', driver);
 */
export async function draw(element, description, options = {}) {
  // Replace with the map library's own drawing; the description is plain data.
  element.dataset.omnimapProvider = description.provider;
  element.setAttribute('role', 'img');
  element.setAttribute('aria-label', options.label ?? 'Map');
  return { element, description };
}
