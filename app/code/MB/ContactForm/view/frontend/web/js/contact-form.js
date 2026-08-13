(function () {
    'use strict';

    var patterns = {
        letters: /^(?=.*[\p{Script=Latin}\p{Script=Cyrillic}])[\p{Script=Latin}\p{Script=Cyrillic}\u0300-\u036f\p{Zs}]+$/u,
        alphanumeric: /^(?=.*[\p{Script=Latin}\p{Script=Cyrillic}0-9])[\p{Script=Latin}\p{Script=Cyrillic}\u0300-\u036f0-9\p{Zs}]+$/u,
        numbers: /^[0-9]+$/,
        email: /^[A-Za-z0-9_+\-]+(?:\.[A-Za-z0-9_+\-]+)*@[A-Za-z0-9](?:[A-Za-z0-9\-]{0,61}[A-Za-z0-9])?(?:\.[A-Za-z0-9](?:[A-Za-z0-9\-]{0,61}[A-Za-z0-9])?)+$/,
        safe_text: /^[\p{Script=Latin}\p{Script=Cyrillic}\u0300-\u036f0-9\p{P}\p{Zs}\r\n\t]+$/u
    };

    function getErrorMessage(form, validation) {
        var key = 'error' + validation.split('_').map(function (part) {
            return part.charAt(0).toUpperCase() + part.slice(1);
        }).join('');

        return form.dataset[key] || '';
    }

    function validateControl(form, control) {
        var validation = control.dataset.validation;
        var value = control.value.trim();
        var valid;

        if (typeof value.normalize === 'function') {
            value = value.normalize('NFC');
        }
        valid = !validation || validation === 'none' || value === '' || patterns[validation].test(value);

        if (valid && validation === 'email' && value !== '') {
            valid = control.validity.typeMismatch === false;
        }

        control.setCustomValidity(valid ? '' : getErrorMessage(form, validation));
        return valid;
    }

    function validateCaptcha(form) {
        var container = form.querySelector('[data-contact-captcha-provider]');
        var error;
        var provider;
        var token;

        if (!container) {
            return true;
        }

        provider = container.dataset.contactCaptchaProvider;
        token = provider === 'google'
            ? form.querySelector('[name="g-recaptcha-response"]')
            : form.querySelector('[name="cf-turnstile-response"]');
        error = container.querySelector('.mb-contact__captcha-error');

        if (!token || !token.value) {
            error.textContent = form.dataset.errorCaptcha;
            error.hidden = false;
            return false;
        }

        error.hidden = true;
        return true;
    }

    function initializeForm(form) {
        var controls = form.querySelectorAll('[data-validation]');

        controls.forEach(function (control) {
            control.addEventListener('input', function () {
                validateControl(form, control);
            });
        });

        form.addEventListener('submit', function (event) {
            var valid = true;

            controls.forEach(function (control) {
                valid = validateControl(form, control) && valid;
            });

            valid = validateCaptcha(form) && valid;
            if (!valid || !form.checkValidity()) {
                event.preventDefault();
                form.reportValidity();
            }
        });
    }

    function renderCaptchas(provider) {
        var selector = provider === 'google' ? '.g-recaptcha' : '.cf-turnstile';
        var api = provider === 'google' ? window.grecaptcha : window.turnstile;

        if (!api || typeof api.render !== 'function') {
            return;
        }

        document.querySelectorAll(selector).forEach(function (element) {
            if (element.dataset.contactCaptchaRendered) {
                return;
            }
            if (element.querySelector('iframe')
                || element.querySelector('[name="g-recaptcha-response"], [name="cf-turnstile-response"]')
            ) {
                element.dataset.contactCaptchaRendered = 'true';
                return;
            }
            api.render(element, {sitekey: element.dataset.sitekey});
            element.dataset.contactCaptchaRendered = 'true';
        });
    }

    function getCaptchaApi(provider) {
        return provider === 'google' ? window.grecaptcha : window.turnstile;
    }

    function getCaptchaScript(provider) {
        var baseUrl = provider === 'google'
            ? 'https://www.google.com/recaptcha/api.js'
            : 'https://challenges.cloudflare.com/turnstile/v0/api.js';
        var scripts = document.scripts;
        var index;

        for (index = 0; index < scripts.length; index += 1) {
            if (scripts[index].src.indexOf(baseUrl) === 0) {
                return scripts[index];
            }
        }

        return null;
    }

    function loadCaptcha(provider) {
        var id = 'mb-contact-captcha-' + provider;
        var script = document.getElementById(id) || getCaptchaScript(provider);

        if (getCaptchaApi(provider) && typeof getCaptchaApi(provider).render === 'function') {
            renderCaptchas(provider);
            return;
        }

        if (script) {
            script.addEventListener('load', function () {
                renderCaptchas(provider);
            }, {once: true});
            return;
        }

        script = document.createElement('script');
        script.id = id;
        script.async = true;
        script.defer = true;
        script.src = provider === 'google'
            ? 'https://www.google.com/recaptcha/api.js?render=explicit'
            : 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
        script.addEventListener('load', function () {
            renderCaptchas(provider);
        });
        document.head.appendChild(script);
    }

    function initialize() {
        var providers = {};

        document.querySelectorAll('[data-contact-form]').forEach(initializeForm);
        document.querySelectorAll('[data-contact-captcha-provider]').forEach(function (element) {
            providers[element.dataset.contactCaptchaProvider] = true;
        });
        Object.keys(providers).forEach(loadCaptcha);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
}());
