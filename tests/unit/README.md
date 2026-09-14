# MB Contact Form PHPUnit tests

## Srpski

Testovi su napisani prema javnom `marvikonvic/MB_ContactForm` repozitorijumu,
commit `4e0493caac5f7bf9bc7fe5d570f39667d99d407d` (composer verzija 1.2.3).
Raspakujte dodatak u koren tog repozitorijuma, uz postojeci `composer.json`.
Produkcioni kod i Composer zavisnosti modula nisu menjani.

Potrebni su PHP, PHPUnit 9.6 i Composer zavisnosti Magento instalacije.
Iz korena Contact Form repozitorijuma pokrenite (Linux primer; zamenite putanje):

```bash
MAGENTO_AUTOLOAD=/path/to/magento/vendor/autoload.php \
php /path/to/magento/vendor/phpunit/phpunit/phpunit -c phpunit.xml.dist
```

PowerShell:

```powershell
$env:MAGENTO_AUTOLOAD = 'C:\path\to\magento\vendor\autoload.php'
php C:\path\to\magento\vendor\phpunit\phpunit\phpunit -c phpunit.xml.dist
```

Ako su Magento zavisnosti vec instalirane u `vendor/` ovog repozitorijuma,
`MAGENTO_AUTOLOAD` nije potreban. Bootstrap ucitava Composer klase, bez
pokretanja Magento aplikacije, baze ili ObjectManager-a.

Za Codecov Clover izvestaj dodajte `--coverage-clover coverage.xml` uz
aktivan Xdebug coverage ili PCOV. Ovaj dodatak ne podesava GitHub Actions
ili slanje izvestaja na Codecov. Filter pokriva samo tri testirane Model
oblasti, a ne ceo modul.

Potvrdjeno lokalno: PHP 8.2.33, PHPUnit 9.6.36, originalne Magento klase iz
2.4.7-p8 i psr/log 3.0.2: **49 testova, 84 provere, bez gresaka**.
Korisceno je izdvojeno okruzenje sa originalnim Magento izvorima i minimalnim
test zavisnostima, ne kompletna Magento instalacija. Coverage procenat nije
meren (lokalni PHP nema coverage driver). Grana Unicode normalizacije uz
`ext-intl` nije izvrsena jer ta ekstenzija nije dostupna lokalno.

Pokriveno: obavezna polja, granice duzine, latinica/cirilica, email i ostala
pravila, select vrednosti, custom polja i nescalar input; CAPTCHA provider,
nedostajuci token/tajna, HTTP greske, strogi boolean success, neispravan JSON
i timeout; email sender/recipient/Reply-To, template vrednosti, neispravna
konfiguracija i izuzeci pri pripremi/slanju uz bezbednu poruku korisniku.

HTTP klijent i mail transport su simulirani. CAPTCHA test sa
`timeout-or-duplicate` proverava obradu odgovora, ne stvarno sprecavanje
ponovne upotrebe tokena kod providera. Isporuka mejla, frontend, controller,
CSRF, session, cache i stvarni CAPTCHA servis zahtevaju zasebne integracione
ili staging provere.

## English

Extract this additive test bundle into the Contact Form repository root.
The tests target commit `4e0493caac5f7bf9bc7fe5d570f39667d99d407d`, version
1.2.3. Use PHPUnit 9.6 and set `MAGENTO_AUTOLOAD` to an existing Magento
Composer autoloader as shown above. No application bootstrap or database
is used. Production code and module Composer requirements are unchanged.

Verified locally with PHP 8.2.33, PHPUnit 9.6.36, original Magento 2.4.7-p8
classes and psr/log 3.0.2 in an isolated dependency environment:
**49 tests, 84 assertions, all passing**. This was not a full Magento install.
Coverage was not measured; the intl normalization branch was not exercised.

The suite covers field validation, CAPTCHA response/error handling and
email construction/failure handling using HTTP/mail doubles. It does not
prove real delivery, token replay protection or storefront/controller behavior.
Add `--coverage-clover coverage.xml` with Xdebug/PCOV to generate a Codecov
input report. Coverage scope is the three tested Model directories only;
CI configuration and Codecov upload are not included.
