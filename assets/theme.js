/* Progressive enhancement: ordinary links and native details work without JS. */
(() => {
  'use strict';
  const root = document.documentElement;
  root.dataset.kwdInputMethod = 'keyboard';
  document.addEventListener('pointerdown', () => { root.dataset.kwdInputMethod = 'pointer'; }, true);
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Tab') root.dataset.kwdInputMethod = 'keyboard';
  }, true);
  const menu = document.querySelector('.site-menu');
  if (menu) {
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && menu.open) {
        menu.open = false;
        menu.querySelector('summary').focus();
      }
    });
    document.addEventListener('click', (event) => {
      if (menu.open && !menu.contains(event.target)) menu.open = false;
    });
  }
})();
