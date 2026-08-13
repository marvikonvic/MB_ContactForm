define([], function () {
    'use strict';

    return function (config, element) {
        element.addEventListener('click', function () {
            var input = document.getElementById(config.inputId);
            var copyFallback;

            if (!input) {
                return;
            }

            copyFallback = function () {
                input.focus();
                input.select();
                document.execCommand('copy');
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(input.value).catch(copyFallback);
            } else {
                copyFallback();
            }

            element.querySelector('span').textContent = config.copiedText;
        });
    };
});
