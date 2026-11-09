/* Copy block enhancement; text remains selectable without JS. GPL-2.0-or-later. */
(() => {
  'use strict';
  if (window.kwdMinimalCopyBlockReady) return;
  window.kwdMinimalCopyBlockReady = true;
  const timers = new WeakMap();
  function fallbackCopy(text) {
    const field = document.createElement('textarea');
    field.value = text;
    field.setAttribute('readonly', '');
    field.style.cssText = 'position:fixed;top:0;left:-9999px';
    document.body.appendChild(field);
    try {
      field.select();
      if (!document.execCommand('copy')) throw new Error('Copy failed');
    } finally {
      field.remove();
    }
  }
  document.addEventListener('click', async (event) => {
    if (!(event.target instanceof Element)) return;
    const button = event.target.closest('.kwd-copy-block--chat .kwd-copy-button');
    if (!button || button.disabled) return;
    const block = button.closest('.kwd-copy-block--chat');
    const target = document.getElementById(button.getAttribute('data-copy-target'));
    const status = block.querySelector('.kwd-copy-status');
    const label = button.querySelector('.kwd-copy-label');
    if (!target || !block.contains(target) || !status || !label) return;
    if (!button.dataset.originalLabel) button.dataset.originalLabel = label.textContent;
    window.clearTimeout(timers.get(button));
    status.textContent = '';
    button.removeAttribute('data-copied');
    button.disabled = true;
    try {
      try {
        if (!navigator.clipboard || !window.isSecureContext) throw new Error('Clipboard unavailable');
        await navigator.clipboard.writeText(target.textContent);
      } catch (error) {
        fallbackCopy(target.textContent);
      }
      status.textContent = status.dataset.success;
      label.textContent = status.dataset.success;
      button.setAttribute('data-copied', 'true');
    } catch (error) {
      status.textContent = status.dataset.error;
      label.textContent = status.dataset.errorLabel;
    } finally {
      button.disabled = false;
      button.focus({ preventScroll: true });
      timers.set(button, window.setTimeout(() => {
        label.textContent = button.dataset.originalLabel;
        button.removeAttribute('data-copied');
      }, 2000));
    }
  });
})();
