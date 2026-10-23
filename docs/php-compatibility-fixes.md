# MB_ContactForm: ispravke P2/P3

Polazna verzija: 1.2.4, commit 7052dddef9ac81d0133a56fef7095c5eb9fa6ab1.

## Izmene

- CAPTCHA token mora biti string pre trim() i pre citanja tajne ili HTTP zahteva.
- Dodato osam regresionih slucajeva: array, object, integer i boolean za
  Google reCAPTCHA i Cloudflare Turnstile. Svako PHP upozorenje u toj proveri
  izaziva pad testa.
- DataProvider atributi i glavna XML konfiguracija za PHPUnit 12.
- Posebna phpunit-9.xml.dist konfiguracija; legacy provider komentari ostaju
  zbog postojeceg PHPUnit 9.6. PHPUnit 12 koristi atribute.
- Mock/stub objekti prilagodjeni PHPUnit 12 uz ocuvane provere store scope-a.
- Uklonjeni nepotrebni ReflectionProperty::setAccessible pozivi i potiskivanje
  XML gresaka u testu. Libxml prethodno stanje vraca se kroz finally.
- PHP kod formatiran prema PSR-12; prosireni jednolinijski blokovi i metode,
  prelomljene duge naredbe i PHTML atributi, dodata LF pravila za PHP/PHTML.
- Oba composer.json fajla, verzija modula i PHP ogranicenja ostaju nepromenjeni.
  P1 / podrska za PHP 8.5 ostaje odlozena.

## Potvrdjeno

- PHP 8.4.27 + PHPUnit 12.5.40 + originalne Magento 2.4.8 klase:
  67 testova, 333 provere; bez PHPUnit obavestenja, deprecation prijava ili gresaka.
- PHP 8.2.33 + PHPUnit 9.6.36 + originalne Magento 2.4.7-p8 klase:
  67 testova, 198 provera, bez gresaka.
- 33 postojece ugovorne provere prolaze.
- Svih osam novih test slucajeva pada sa originalnim CAPTCHA validatorom:
  cetiri greske i cetiri neuspele provere; sa ispravkom svih osam prolazi.
- Provera sintakse: svih 32 PHP/PHTML fajla prolaze na PHP 8.2.33.
- PHP CS Fixer @PSR12 dry-run: bez predlozenih izmena za sve PHP fajlove modula i testova.
- git diff --check: bez gresaka.
- Oba Composer fajla proverena prema polaznom commitu: nema izmena.

## Ogranicenja

Testovi su izvrseni sa originalnim Magento klasama u izdvojenom okruzenju,
ne na kompletnoj Magento instalaciji. Mail i HTTP klijent su simulirani.
Stvarna isporuka emaila, CAPTCHA servis, browser, CSRF/FPC i setup:di:compile
nisu provereni. PHPStan nije izvrsen i coverage procenat nije meren.

Puna Magento Coding Standard provera produkcionih PHP fajlova ne prijavljuje
greske, ali prijavljuje upozorenja: pretezno nedostajuce PHPDoc komentare,
tri postojeca konstruktora koja samo pozivaju parent, postojeci parse_url()
i neprelomljene potpune prevodive string literale. Ti nalazi nisu tretirani
kao dokaz runtime nekompatibilnosti. PHTML provera tim alatom na Windowsu
nailazi na internu regex gresku; PHTML sintaksa i postojece template provere
jesu prosle.
