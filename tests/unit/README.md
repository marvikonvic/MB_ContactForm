# MB Contact Form unit tests

## Srpski

Testovi koriste originalne Magento klase iz postojece instalacije, bez
pokretanja aplikacije ili pristupa bazi. Postavite `MAGENTO_AUTOLOAD` na
`vendor/autoload.php` te instalacije.

### PHPUnit 12

Potreban je PHP 8.3 ili noviji, uskladjen sa Magento verzijom instalacije.
Glavna konfiguracija `phpunit.xml.dist` koristi PHPUnit 12 XML semu i
`source/include` filter. Data provider metode imaju `DataProvider` atribute.

```powershell
$env:MAGENTO_AUTOLOAD = 'C:\path\to\magento\vendor\autoload.php'
php C:\path\to\phpunit-12.phar -c phpunit.xml.dist
```

### Postojeci PHPUnit 9.6

Za starije Magento instalacije koristite `phpunit-9.xml.dist`.
`@dataProvider` komentari namerno su zadrzani uz atribute: PHPUnit 9.6
koristi komentare, a PHPUnit 12 atribute. Nije potreban kompatibilni adapter.

```powershell
$env:MAGENTO_AUTOLOAD = 'C:\path\to\magento\vendor\autoload.php'
php C:\path\to\magento\vendor\phpunit\phpunit\phpunit -c phpunit-9.xml.dist
```

Na Linuxu postavite istu promenljivu okruzenja i koristite odgovarajuce putanje.
Ako repozitorijum vec ima Magento zavisnosti u svom `vendor/` direktorijumu,
`MAGENTO_AUTOLOAD` nije potreban.

### Obuhvat i potvrdjene provere

- PHP 8.4.27, PHPUnit 12.5.40, originalne Magento 2.4.8 klase: 67 testova,
  333 provera; bez upozorenja, obavestenja i deprecation prijava.
- PHP 8.2.33, PHPUnit 9.6.36, originalne Magento 2.4.7-p8 klase: 67 testova,
  198 provere, bez gresaka.
- `php tests/standard-fields.php`: 33 ugovorne provere.

Broj provera zavisi od PHPUnit verzije i nacina brojanja mock ocekivanja.
Koriscena su izdvojena okruzenja sa originalnim Magento izvorima, ne kompletne
Magento instalacije. Novije okruzenje ukljucuje `ext-intl`.

Regresioni CAPTCHA testovi obuhvataju niz, objekat, integer i boolean za oba
provajdera. PHP upozorenja pretvaraju se u izuzetke u tim testovima; neispravan
token mora biti odbijen pre citanja tajne i HTTP zahteva. Provereni su i
validni string tokeni, greske provajdera, validacija polja, email transport,
store scope i escapovanje Store Information prikaza.

HTTP klijent i mail transport su simulirani. Testovi ne potvrdjuju stvarnu
isporuku emaila, stvarni CAPTCHA servis, browser, HTTP/CSRF tok, FPC ili DI
kompilaciju. Coverage procenat nije meren. Za Clover izvestaj dodajte
`--coverage-clover coverage.xml` uz Xdebug/PCOV. Filter ostaje ogranicen na
Model/Form, Model/Captcha i Model/Email.

## English

Use `phpunit.xml.dist` with PHPUnit 12 and PHP 8.3+; use
`phpunit-9.xml.dist` with PHPUnit 9.6. Set `MAGENTO_AUTOLOAD` to the Magento
installation's Composer autoloader. Provider attributes are used by PHPUnit
12; legacy provider comments are retained for PHPUnit 9.6.

Verified in isolated environments with original Magento classes:
67 tests / 333 assertions on PHP 8.4.27, PHPUnit 12.5.40, Magento 2.4.8;
67 tests / 198 assertions on PHP 8.2.33, PHPUnit 9.6.36, Magento 2.4.7-p8.
The standalone standard-fields script passes 33 checks. These results do
not establish full application, storefront, CAPTCHA or email integration.
