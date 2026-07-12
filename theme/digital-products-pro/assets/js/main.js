import { initializeWorkflowShowcase } from './workflow.js';
document.addEventListener('DOMContentLoaded', () => {
  initializeWorkflowShowcase();
  const menuButton = document.querySelector('[data-menu-toggle]');
  const navigation = document.querySelector('[data-primary-nav]');
  const themeButton = document.querySelector('[data-theme-toggle]');
  const storedMode = localStorage.getItem('dpp-theme-mode');

  if (window.DPPTheme && DPPTheme.darkModeDefault && !storedMode) {
    document.body.classList.add('dark-mode');
  }

  if (storedMode === 'dark') {
    document.body.classList.add('dark-mode');
  }

  if (menuButton && navigation) {
    menuButton.addEventListener('click', () => {
      const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
      menuButton.setAttribute('aria-expanded', String(!isOpen));
      navigation.classList.toggle('is-open', !isOpen);
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        menuButton.setAttribute('aria-expanded', 'false');
        navigation.classList.remove('is-open');
      }
    });
  }

  if (themeButton) {
    themeButton.addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
      localStorage.setItem(
        'dpp-theme-mode',
        document.body.classList.contains('dark-mode') ? 'dark' : 'light'
      );
    });
  }
});
