define([], function () {
    'use strict';

    return function (config, element) {
        var input = element.closest('td').querySelector('[data-field-disabled]');

        element.addEventListener('click', function () {
            if (!input || input.disabled || element.disabled) {
                return;
            }
            input.value = input.value === '1' ? '0' : '1';
            element.querySelector('span').textContent = input.value === '1'
                ? element.dataset.restoreLabel : element.dataset.removeLabel;
            element.setAttribute('aria-pressed', input.value === '1' ? 'true' : 'false');
            input.dispatchEvent(new Event('change', {bubbles: true}));
        });
    };
});
