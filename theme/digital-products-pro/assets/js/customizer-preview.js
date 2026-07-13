/* global wp */

function bindTextSetting(settingId, selector) {
  wp.customize(settingId, (setting) => {
    setting.bind((value) => {
      const element = document.querySelector(selector);

      if (element) {
        element.textContent = value;
      }
    });
  });
}

bindTextSetting('dpp_badge', '[data-customize-hero-badge]');
bindTextSetting('dpp_hero_title', '[data-customize-hero-title]');
bindTextSetting('dpp_hero_text', '[data-customize-hero-text]');
bindTextSetting('dpp_primary_button', '[data-customize-primary-button]');
bindTextSetting('dpp_secondary_button', '[data-customize-secondary-button]');