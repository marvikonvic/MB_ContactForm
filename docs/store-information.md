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

Potvrđeno lokalno: 57 PHPUnit testova / 110 provera, 33 postojeće contract provere,
PHP lint svih 29 PHP/PHTML fajlova, parsiranje XML/JSON/CSV i `git diff --check`.
Pregled stvarnog PHTML/CSS sa probnim podacima potvrdio je dve kolone na 1200 px,
slaganje ispod forme na 390 px bez horizontalnog prelivanja i punu širinu bez panela.
Pregled koristi zamene za Magento block/escaper servise; nije živa Magento instalacija.

Pre puštanja proveriti: čuvanje i nasleđivanje podešavanja u Adminu, instalaciju i DI
kompilaciju, prikaz na aktivnoj temi (uključujući CAPTCHA), različite Store View podatke,
keširanje i slanje kontakt poruke. Ove runtime provere još nisu izvršene.

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

Local validation: 57 PHPUnit tests / 110 assertions, 33 existing contract checks,
29 PHP/PHTML syntax checks, XML/JSON/CSV parsing, and whitespace validation passed.
The actual template and stylesheet were previewed with sample data and isolated service
doubles at 1200 px and 390 px, plus the disabled state. No live Magento installation,
Admin save, DI compilation, theme/CAPTCHA integration, cache or email-delivery test was run.
