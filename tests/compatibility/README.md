# Repeatable PHP and Magento compatibility checks

These checks load original Magento classes without bootstrapping a store, database,
generated DI container, HTTP server, or mail server. They do not prove complete
Magento installation or storefront compatibility.

## Local checks

Use a PHP version appropriate for the selected Magento source release. Install the
test dependencies using that PHP binary; Composer selects PHPUnit 9.6 on PHP 8.1/8.2
or PHPUnit 12 on PHP 8.3+. When changing PHP versions locally, run `composer update`
in the isolated test directory so its ignored lock file matches the selected PHP.

```bash
git clone --depth 1 --branch 2.4.9 https://github.com/magento/magento2.git /tmp/magento249-source
composer install --working-dir=tests/compatibility --no-interaction --no-scripts --no-plugins
MAGENTO_SOURCE=/tmp/magento249-source php tests/compatibility/run.php
```

PowerShell, after checking out the Magento source and installing the dependencies:

```powershell
$env:MAGENTO_SOURCE = 'C:\path\to\magento249-source'
php tests/compatibility/run.php
```

`run.php` selects the PHPUnit XML configuration and runs the unit suite, loads every
production module class, checks the real core CSP/mail APIs, and runs the existing
standard-field contract checks. Only the test directory has its own dependencies;
the module's production Composer manifests do not acquire test dependencies.

## Security-patched 2.4.9 source

Use a **clean, disposable source checkout**, never a deployed store. The preparer
accepts only the original Magento 2.4.9 commit, downloads the pinned official Adobe
archives, verifies SHA-256, and maps Composer package paths onto the source tree.
Each patch is checked before application and reverse-checked immediately afterward.
It downloads CE patches only; it does not configure or remediate a live store.

```bash
python3 tests/compatibility/prepare-security-patches.py /tmp/magento249-source \
  --report /tmp/contactform-security-patches.json
MAGENTO_SOURCE=/tmp/magento249-source php tests/compatibility/run.php
```

The pinned snapshot is dated **2026-11-22**: July, August, September and October 2026
CE isolated patches, plus the separate `VULN-39341_249.patch` hotfix. Later Adobe
security releases require a deliberate update of the preparer and its checksums.

## GitHub Actions

[The workflow](../../.github/workflows/compatibility.yml) runs PHP 8.1–8.6 against
Magento 2.4.4, 2.4.6, 2.4.7-p10, 2.4.8-p5 and security-patched 2.4.9. The PHP 8.6
job is an experimental **module** check. Magento 2.4.9's own Composer manifest
does not allow PHP 8.6, and Adobe's supported 2.4.9 platform targets PHP 8.5.

The workflow uploads patch provenance for the two patched-source jobs. Green jobs
mean these isolated checks passed, not that a complete Magento store was installed,
compiled, patched or tested in a browser.
