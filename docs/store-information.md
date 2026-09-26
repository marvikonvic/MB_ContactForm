# Store Information beside the contact form

## Srpski

U **Stores > Configuration > MB Contact Form > Contact Form > General Settings**
uključite **Enable Store Information**. Podrazumevana vrednost je **No**.
Opcija podržava Default, Website i Store View scope i Magento nasleđivanje.

Podaci se preuzimaju iz **Stores > Configuration > General > General > Store Information**
za aktivnu prodavnicu: naziv, adresa (obe linije, poštanski broj, grad, region i država),
telefon, radno vreme i VAT Number. Region i država prikazuju se kao nazivi preko
Magento Store Information servisa. Nema dodatnih polja za unos istih podataka.
Email se ne prikazuje jer nije deo ove Magento sekcije.

- Na ekranima od 900 px: forma levo, informacije desno, odnos raspoloživih širina 65/35.
- Na manjim ekranima: informacije ispod forme; postojeći raspored polja ostaje responzivan.
- Prazne stavke se preskaču. Kada je opcija No ili su svi podaci prazni,
  forma zadržava postojeću strukturu i punu širinu.
- Tekst se HTML-escape-uje; prelomi redova u adresi i radnom vremenu ostaju vidljivi.
- Posle izmene podešavanja osvežite Magento Configuration i Full Page cache kada Admin to zatraži.

Potvrđeno lokalno: 59 PHPUnit testova / 116 provera, 33 postojeće contract provere,
PHP lint svih 30 PHP/PHTML fajlova, parsiranje XML/JSON/CSV i `git diff --check`.
Pregled stvarnog PHTML/CSS sa probnim podacima potvrdio je dve kolone na 1200 px,
slaganje ispod forme na 390 px bez horizontalnog prelivanja i punu širinu bez panela.
Pregled koristi zamene za Magento block/escaper servise; nije živa Magento instalacija.

Na Stagento sajtu korisnik je potvrdio uspešan setup:upgrade, DI kompilaciju, objavljivanje
statičkih fajlova i čišćenje keša pri instalaciji funkcionalnosti. Poslednja PHP ispravka
je preuzeta kroz Composer i keš je očišćen. Prikaz posle ispravke, čuvanje i nasleđivanje
podešavanja, različiti Store View podaci, CAPTCHA i slanje poruke još nisu potvrđeni.

## English

Enable **Stores > Configuration > MB Contact Form > Contact Form > General Settings >
Enable Store Information** (default: **No**). Default, Website and Store View scopes
use normal Magento configuration inheritance.

The panel reads the active store's **General > General > Store Information** values:
name, address, phone, operating hours and VAT number. Native Magento services resolve
country and region names. Empty values are omitted; no duplicate settings are added.
Email is not shown because it is not part of Store Information.

At viewport widths of 900 px and above, the form and panel share available width 65/35.
Below that breakpoint, the panel follows the form. Disabled or entirely empty panels
leave the original form structure and width intact. All values are escaped as text;
address and operating-hours line breaks are preserved.

Local validation: 59 PHPUnit tests / 116 assertions, 33 existing contract checks,
30 PHP/PHTML syntax checks, XML/JSON/CSV parsing, and whitespace validation passed.
The actual template and stylesheet were previewed with sample data and isolated service
doubles at 1200 px and 390 px, plus the disabled state. The user confirmed setup:upgrade,
DI compilation, static deployment and cache cleaning during the Stagento feature install.
The final PHP fix was downloaded through Composer and cache was cleaned. Live rendering
after that fix, Admin save/inheritance, multi-store behavior, CAPTCHA and email delivery
remain unconfirmed.

## Widget store resolution fix

The CMS widget now resolves its current or explicitly assigned store through Magento's
Store Manager. The original call to the widget's magic `getStore()` returned null and
caused a TypeError on the live CMS page. Two regression cases cover current-store and
explicit-store resolution using the real widget methods. Both reproduce the TypeError
before the fix and pass after it. Frontend verification after the fix on Stagento is pending.
