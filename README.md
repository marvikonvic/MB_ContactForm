# MB Contact Form 1.2.3

[![Version](https://img.shields.io/badge/version-1.2.3-0A66C2.svg)](https://github.com/marvikonvic/MB_ContactForm/tree/v1.2.3)
[![Magento](https://img.shields.io/badge/Magento-2.4.7--p3%20tested-EE672F.svg?logo=magento&logoColor=white)](https://github.com/marvikonvic/MB_ContactForm)
[![PHP](https://img.shields.io/badge/PHP-8.1--8.4-777BB4.svg?logo=php&logoColor=white)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-GPL--3.0--or--later-6F42C1.svg)](LICENSE.md)

[Srpski](#srpski) | [English](#english)

## Srpski

## DEMO

[Isprobajte kontakt formu](https://stagento.com/kontakt) — Trenutno samo Frontend.

## Funkcionalnosti

MB Contact Form omogućava prilagođavanje kontakt forme kroz Magento administraciju, bez menjanja koda.

- Full Page Cache kompatibilan CMS widget.
- Responsive i Hyvä kompatibilan storefront bez RequireJS-a, jQuery-ja i Knockout-a.
- Konfiguracija po Default, Website i Store View nivou.
- Vidljivost forme po customer grupama, uključujući `NOT LOGGED IN`.
- Dodavanje polja, podešavanje obaveznih unosa i pravila validacije.
- Izmenjivi nazivi standardnih polja i mogućnost dodavanja `text`, `textarea` i `select` polja.
- Izmenjivi `Sender Email`, `Sender Name` i `Recipient Email`.
- Magento `Email Template` izbor za izgled email obaveštenja.
- Google reCAPTCHA v2 Checkbox i Cloudflare Turnstile sa serverskom proverom tokena.
- Prilagodljiva success poruka, success URL i newsletter URL.
- Engleski izvorni tekstovi i srpski Latinica prevodi preko `sr_Latn_RS.csv`.
- Admin konfiguracija dostupna samo Full Access administratorima.
- Mogućnost prijave na newsletter nakon slanja poruke.
- Instalacija i ažuriranje preko GitHuba i Composera.

## Kompatibilnost

- **PHP:** 8.1.x, 8.2.x, 8.3.x i 8.4.x prema zahtevima modula u `composer.json`. Izabrana PHP verzija mora odgovarati i konkretnoj Magento verziji.
- **Magento:** potvrđeno na Magento 2.4.7-p3. Composer ne ograničava Magento pakete na određenu verziju; druge verzije nisu potvrđene ovim staging testom.
- **Teme:** podrška za Hyvä i Luma; staging provera potvrđena je na Hyvä okruženju navedenom ispod.

### Testirano na staging okruženju

| Komponenta | Verzija |
| --- | --- |
| MB Contact Form | 1.2.3 |
| PHP | 8.3.33 |
| Magento | 2.4.7-p3 |
| Hyvä Theme Module | 1.5.2 |

### Rezultati funkcionalnih i osnovnih bezbednosnih testova

**Planirani funkcionalni testovi i osnovni bezbednosni testovi završeni su i prošli.** Rezultate je korisnik potvrdio ručnim testiranjem na Stagento staging okruženju za funkcionalnosti verzije 1.2.3, uključujući testiranje koda sa `dev-main`. Ovo nije izveštaj automatskog test paketa niti kompletan bezbednosni audit.

- **Obavezna polja:** browser blokira prazne obavezne unose; server odbija prazno Name polje i kada se browser provera zaobiđe.
- **Email validacija:** neispravan email je odbijen u browseru i direktnim slanjem serveru.
- **Pisma:** latinica i ćirilica su prihvaćene, nepodržana pisma odbijena; direktan serverski test sa kineskim znakovima u Name polju je odbijen.
- **Numerička polja:** slova se odbijaju; direktan serverski test sa `abc123` u Phone Number polju je odbijen.
- **Email isporuka i sadržaj:** poruka stiže, izmenjeni Subject i uvodni tekst prikazuju se u emailu, a Reply-To adresira posetioca.
- **Opciona polja:** uklonjeno polje ne prikazuje se ni u formi ni u emailu.
- **Customer grupe:** potvrđeno ograničenje pristupa za nedozvoljenu customer grupu.
- **Nepotpuna CAPTCHA konfiguracija:** forma je skrivena kada je izabran Google reCAPTCHA bez oba ključa, kao i kada Turnstile Site Key nedostaje. Ovo nije potvrda uspešne Google reCAPTCHA validacije.
- **Turnstile Siteverify:** Cloudflare analitika beleži serversku validaciju pri uspešnom slanju.
- **Nedostajući CAPTCHA token:** direktan POST bez Turnstile tokena odbijen je CAPTCHA greškom.
- **Ponovna upotreba tokena:** dva uzastopna zahteva sa istim Turnstile tokenom proizvela su samo jedan email; drugi zahtev odbijen je CAPTCHA greškom.
- **Full Page Cache:** slanje forme sa potvrđenim cache `HIT` u odvojenoj Edge InPrivate sesiji uspelo je bez session/form key greške, a email je stigao.
- **Privatnost success stranice:** otvaranje success URL-a u novoj InPrivate sesiji prikazuje prazno newsletter email polje, bez emaila prethodne sesije.

## Podešavanja

Konfiguracija se nalazi na:

```text
Stores > Configuration > MB Contact Form > Contact Form
```

Tu se podešavaju:

1. status modula i dozvoljene customer grupe;
2. naslov forme i nazivi polja;
3. email pošiljaoca, primaoca i Magento Email Template;
4. dodatna polja i njihova validacija;
5. Google reCAPTCHA ili Cloudflare Turnstile ključevi;

   **Napomena:** pre unosa ključeva izaberite odgovarajući **Store View** u biraču scope-a. Proverite CAPTCHA provajdera i oba ključa za prodavnicu na kojoj je forma prikazana; obratite pažnju na nasleđivanje vrednosti. Sačuvajte konfiguraciju, očistite cache i proverite slanje forme.

6. success poruka, success URL i newsletter podešavanja.

### Naslov i uvodni tekst emaila

U **General Settings** podesite **Email Subject** i **Email Introduction** za odgovarajući Store View. Subject podržava `%store_name`; prazan Subject koristi podrazumevani naslov. Uvod je običan tekst sa prelomima redova; prazna vrednost ga skriva. Ugrađeni email šablon automatski koristi oba polja.

Za već kopiran šablon u **Marketing > Communications > Email Templates**, postavite Template Subject na `{{var email_subject|raw}}`, a postojeći uvodni pasus zamenite sledećim:

```html
{{depend email_intro}}
<p>{{var email_intro|escape|nl2br}}</p>
{{/depend}}
```

Dostupno od verzije `1.2.3`.

## CMS Widget

Widget se može dodati kroz Page Builder / Insert Widget ili direktno u sadržaj CMS stranice ili bloka:

```text
{{widget type="MB\ContactForm\Block\Widget\Form"}}
```

## Validacija

Pravila ispod su početna pravila; za opciona standardna polja mogu se promeniti u tabeli.

- Poruka: latinična i ćirilična slova, ASCII brojevi, razmaci, novi redovi i interpunkcija.
- Ime i prezime: latinična i ćirilična slova i razmaci.
- Email: stroga browser i serverska email validacija.
- Kompanija: latinična i ćirilična slova, ASCII brojevi i razmaci.
- Telefon: samo ASCII brojevi.
- Kinesko, tajlandsko, hebrejsko, arapsko, grčko i druga nepodržana pisma se odbijaju u poljima ograničenim na slova.

Ista pravila se primenjuju u browseru i ponovo na serveru.

## Cache i bezbednost

- Widget ostaje cacheable i poštuje Magento customer-group HTTP context.
- Success stranica je namerno `cacheable="false"` jer jednokratno čita poslati email iz sesije.
- POST endpoint proverava form key, status modula, customer grupu, sva polja i CAPTCHA token.
- Korisnički podaci se escape-uju u PHTML i email template-u.
- CSP pravila dozvoljavaju CAPTCHA hostove, dok se newsletter `form-action` origin dodaje dinamički samo na success stranici.
- Success stranica koristi `NOINDEX,FOLLOW`.

## Snimci ekrana

Prikazi sa staging okruženja.

### Kontakt forma na Hyvä temi

![Kontakt forma na Hyvä temi](docs/screenshots/contact-form.png)

### Mobilni prikaz — naslov i polja forme

<img src="docs/screenshots/contact-form-mobile-top.png" alt="Mobilni prikaz kontakt forme — naslov i polja" width="390">

### Mobilni prikaz — dodatna polja, Turnstile i Submit

<img src="docs/screenshots/contact-form-mobile-submit.png" alt="Mobilni prikaz dodatnih polja, Turnstile zaštite i Submit dugmeta" width="390">

### Popunjena forma sa dodatnim poljima i Turnstile zaštitom

![Popunjena forma sa dodatnim poljima i Turnstile zaštitom](docs/screenshots/contact-form-filled.png)

### Validacija imena i prezimena

![Validacija imena i prezimena](docs/screenshots/name-validation.png)

### Validacija email adrese

![Validacija email adrese](docs/screenshots/email-validation.png)

### Potvrda slanja i newsletter prijava

![Potvrda slanja i newsletter prijava](docs/screenshots/success-newsletter.png)

### Email obaveštenje sa porukom i dodatnim poljima

![Email obaveštenje sa porukom i dodatnim poljima](docs/screenshots/email-notification.png)

### Admin podešavanja, dodatna polja i CAPTCHA

![Admin podešavanja, dodatna polja i CAPTCHA](docs/screenshots/admin-settings.png)

### Zaštita od spama

![Zaštita od spama](docs/screenshots/spam-protection.png)

### CMS widget kod

![CMS widget kod](docs/screenshots/widget-code.png)

### Field Labels

![Field Labels](docs/screenshots/field-labels.png)

### Email zaglavlja i Mailpit HTML compatibility provera

Screenshot prikazuje `From`, `To` i `Reply-To` zaglavlja koja dobijaju email klijenti, kao i Mailpit `HTML Check` rezultat kompatibilnosti ugrađenog email šablona.

![Email zaglavlja i Mailpit HTML compatibility provera](docs/screenshots/mobile-form.png)

## Instalacija

### Composer (GitHub)

Za javni repozitorijum koristite HTTPS URL ispod; GitHub autentifikacija nije potrebna za čitanje javnog koda (Composer može tražiti token ako se dostigne GitHub API limit). Ovaj postupak bez autentifikacije važi nakon promene vidljivosti repozitorijuma na Public.
Iz Magento root foldera pokrenuti:

```bash
composer config repositories.mb-contact-form vcs https://github.com/marvikonvic/MB_ContactForm.git
composer require mb/module-contact-form:1.2.3 --prefer-dist
```

Composer automatski registruje modul iz paketa u `vendor` direktorijumu.
Ne instalirati istovremeno i kopiju u `app/code`. Nakon instalacije pokrenuti
Magento komande za aktivaciju navedene ispod.

### ZIP

Raspakovati modul tako da se nalazi u:

```text
app/code/MB/ContactForm
```

Zatim iz Magento root foldera pokrenuti:

```bash
bin/magento module:enable MB_ContactForm
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
```

U Production modu generisati static content za aktivne locale i teme:

```bash
bin/magento setup:static-content:deploy -f en_US sr_Latn_RS
```

## Ažuriranje

Pre ažuriranja napravite backup baze, koda i konfiguracije i proverite novu verziju na stagingu. Komande pokrećite iz Magento root direktorijuma kao vlasnik Magento fajlova.

### Instalacija preko Composera

Za prelazak sa ranije verzije (uključujući 1.0.x) na objavljeni tag 1.2.3:

```bash
bin/magento maintenance:enable
composer config repositories.mb-contact-form vcs https://github.com/marvikonvic/MB_ContactForm.git
composer require mb/module-contact-form:1.2.3 --prefer-dist --with-dependencies
bin/magento setup:upgrade
bin/magento setup:di:compile
```

U Production modu zatim pokrenite `bin/magento setup:static-content:deploy -f en_US sr_Latn_RS`, prilagođeno aktivnim locale-ima. Nakon uspešnog izvršavanja:

```bash
bin/magento cache:clean
bin/magento maintenance:disable
```

Ako neki korak ne uspe, prekinite postupak i rešite grešku ili vratite backup pre isključivanja maintenance režima. Za naredna izdanja zamenite `1.2.3` željenim objavljenim tagom. Tačno zaključana Composer verzija ne prelazi na novo izdanje običnim `composer update`.

### Ručna instalacija u app/code

Preuzmite [ZIP taga v1.2.3](https://github.com/marvikonvic/MB_ContactForm/archive/refs/tags/v1.2.3.zip). Sačuvajte postojeći `app/code/MB/ContactForm` van Magento stabla i proverite lokalne izmene. U maintenance režimu zamenite ceo direktorijum kopijom `app/code/MB/ContactForm` iz arhive, uz ispravno vlasništvo fajlova. Pokrenite iste Magento upgrade, compile, static-content (Production) i cache komande iznad. Ne kombinujte ručnu i Composer instalaciju.

Pri prelasku sa 1.0.x proverite nazive/redosled polja i prilagođene email šablone: od 1.2.0 koristi se `name` umesto `firstname`/`lastname`. Proverite CAPTCHA ključeve za odgovarajući Store View, pošaljite test poruku i potvrdite prijem emaila. Detalji promena su u istoriji verzija.

## Dokumentacija

Kompletna tehnička dokumentacija i staging kontrolna lista nalaze se u [README fajlu modula](app/code/MB/ContactForm/README.md).

## Licenca

Modul se distribuira pod [GNU General Public License, verzija 3 ili bilo koja novija verzija](LICENSE.md) (`GPL-3.0-or-later`). Copyright © 2026 MB.

Nosilac autorskih prava dozvoljava korišćenje i ranijih izdanja MB Contact Form modula pod licencom GPL-3.0-or-later.

## Istorija verzija

### Novo u verziji 1.2.3

- Email Subject i Email Introduction u General Settings, podesivi po Store View-u.
- Ugrađeni email šablon koristi novi naslov i uvodni tekst; za kopirane šablone pogledajte uputstvo iznad.
- GPL-3.0-or-later licenca, ažurirani bedževi i uputstva za instalaciju i ažuriranje.
- PHP/XML provere su prošle; korisnik je potvrdio uspešan staging test emaila i svih podešavanja, uključujući Email Subject i Email Introduction.

### Novo u verziji 1.2.2

Vraćena je klasa `action primary` na Submit i Subscribe dugmadima, čime je poništena privremena izmena na `pagebuilder-button-primary`. Zastareli ZIP verzije 1.0.0 više nije deo paketa. Composer verzija je usklađena sa tagom `v1.2.2`.

Staging potvrda i screenshotovi odnose se na verziju 1.2.1; verzija 1.2.2 nije posebno testirana u Magento runtime okruženju. CAPTCHA podešavanja ostaju po Store View-u.

### Novo u verziji 1.2.1

Remove checkbox je zamenjen dugmetom **Remove / Restore**. Klik menja stanje polja; **Save Config** čuva promenu. Email i Message ostaju zaštićeni.

### Novo u verziji 1.2.0

- First Name i Last Name zamenjeni su jednim poljem **Name** (`name`), početno obaveznim, sa validacijom latiničnih i ćiriličnih slova i razmaka. Može se ukloniti i promeniti Required.
- Postojeći redovi za ime/prezime se pri čitanju konfiguracije zamenjuju novim Name redom, na ranijoj od dve pozicije. Novi Name je uključen i obavezan čak i ako je staro polje bilo uklonjeno. Ostala podešavanja ostaju sačuvana. Čuvanje tabele upisuje novi format samo u izabrani scope.
- Email redosled: **Name → Email Address → Company Name → Phone Number → Message → Additional information**. Reply-To naziv koristi Name.
- Ako koristite kopirani email šablon, zamenite stare firstname/lastname promenljive sa `name`, `label_name` i uslovom `show_name`, ili izaberite ugrađeni šablon.
- Verzija 1.2.0 je proverena lokalno; slanje emaila i Admin prikaz ove verzije još treba proveriti na stagingu.

### Istorijski pregled verzije 1.1.0

- **Field Labels → Fields** je tabela: Code, Label, Type, Required, Validation, Options (comma-separated), Sort Order i Action.
- Standardni kodovi su fiksni: `message`, `firstname`, `lastname`, `email`, `company`, `telephone`.
- **Email i Message** ostaju uključeni i obavezni. Required je zaključan na Yes; tip i validacija su zaštićeni. Njihovi nazivi i Sort Order mogu se menjati.
- **Action → Remove** isključuje opciono polje iz forme i emaila. Poništavanje izbora vraća polje bez gubitka podešavanja.
- Sort Order menja redosled standardnih polja na formi. Additional Fields ostaju zasebna tabela i prikazuju se zatim prema svom Sort Order-u.
- Email uvek koristi redosled: First Name, Last Name, Email Address, Company Name, Phone Number, Message, Additional information. Uklonjena polja se izostavljaju.
- **Submit Button Label** ostaje zasebno podesiv po Store View-u; CTA stil ostaje isti.
- Admin prevodi prate jezik administratora: engleski izvorni tekst i srpska latinica (`sr_Latn_RS`). Ručno uneti nazivi standardnih polja čuvaju se po Store View-u.
- Ako nova tabela još nije sačuvana, postojeći nazivi iz verzije 1.0.4 koriste se automatski, uz isti početni raspored i obaveznost. Nema prepisivanja postojećih scope vrednosti.

**Provera:** verzija 1.1.0 je proverena lokalnim testovima pravila i PHP/PHTML/XML proverama. Staging tabela i screenshotovi iznad potvrđuju Magento runtime proveru verzije 1.2.1.

**Email šabloni:** ugrađeni šablon poštuje uklonjena polja. Ako je izabran ranije kopiran šablon iz Marketing → Email Templates, ažurirajte ga novim uslovima `show_firstname`, `show_lastname`, `show_company`, `show_telephone` ili izaberite ugrađeni šablon.

Magento 2 kontakt forma kompatibilna sa Luma i Hyvä temama. Modul pruža cache-friendly CMS widget, Store View konfiguraciju, prilagodljiva polja, Magento Email Templates i izbor između Google reCAPTCHA v2 i Cloudflare Turnstile zaštite.

- **1.1.0:** Tabela standardnih polja, zaštićeni Email/Message, dinamička validacija, fiksan email redosled i dopunjeni Admin prevodi.

- **1.0.4:** Dodaje 24 px razmaka iznad i ispod forme i potvrde, kao i CTA stil Subscribe dugmeta.
- **1.0.3:** Postavlja naslov forme na 20 px i bold (700).
- **1.0.2:** Proširuje Additional Fields tabelu i dodaje CTA stil Submit dugmetu.
- **1.0.1:** Ispravlja identifikator Admin taba za Magento XML validaciju.

---

## English

## Demo

[Try the contact form](https://stagento.com/kontakt) — Currently frontend only.

## Features

MB Contact Form lets you customize the contact form through Magento Admin without changing code.

- Full Page Cache compatible CMS widget.
- Responsive, Hyvä-compatible storefront without RequireJS, jQuery, or Knockout.
- Configuration at Default, Website, and Store View scope.
- Form visibility by customer group, including `NOT LOGGED IN`.
- Additional fields, required inputs, and validation rules.
- Customizable standard field labels and support for `text`, `textarea`, and `select` fields.
- Configurable `Sender Email`, `Sender Name`, and `Recipient Email`.
- Magento `Email Template` selection for notification emails.
- Google reCAPTCHA v2 Checkbox and Cloudflare Turnstile with server-side token verification.
- Custom success message, success URL, and newsletter URL.
- English source text and Serbian Latin translations through `sr_Latn_RS.csv`.
- Admin configuration restricted to Full Access administrators.
- Newsletter subscription option after sending a message.
- Installation and updates through GitHub and Composer.

## Compatibility

- **PHP:** 8.1.x, 8.2.x, 8.3.x, and 8.4.x according to the module's `composer.json` requirements. The selected PHP version must also be supported by the installed Magento version.
- **Magento:** verified on Magento 2.4.7-p3. Composer does not restrict Magento packages to a specific version; other versions have not been confirmed by this staging test.
- **Themes:** Hyvä and Luma support; staging verification was performed on the Hyvä environment below.

### Tested staging environment

| Component | Version |
| --- | --- |
| MB Contact Form | 1.2.3 |
| PHP | 8.3.33 |
| Magento | 2.4.7-p3 |
| Hyvä Theme Module | 1.5.2 |

### Functional and basic security test results

**The planned functional and basic security tests have been completed and passed.** Results were confirmed by the user through manual testing on the Stagento staging environment for version 1.2.3 functionality, including testing code from `dev-main`. This is not an automated test-suite report or a comprehensive security audit.

- **Required fields:** the browser blocks empty required inputs; the server rejects an empty Name even when browser validation is bypassed.
- **Email validation:** invalid email input is rejected both in the browser and through direct server submission.
- **Writing systems:** Latin and Cyrillic are accepted and unsupported scripts rejected; a direct server test with Chinese characters in Name was rejected.
- **Numeric fields:** letters are rejected; a direct server test with `abc123` in Phone Number was rejected.
- **Email delivery and content:** email arrives, the configured subject and introduction appear in the message, and Reply-To addresses the visitor.
- **Optional fields:** a removed field appears in neither the form nor the email.
- **Customer groups:** access restrictions for a disallowed customer group were confirmed.
- **Incomplete CAPTCHA configuration:** the form is hidden when Google reCAPTCHA is selected with both keys empty, and when the Turnstile Site Key is missing. This does not confirm successful Google reCAPTCHA validation.
- **Turnstile Siteverify:** Cloudflare analytics records server-side validation during successful submission.
- **Missing CAPTCHA token:** a direct POST without a Turnstile token was rejected with a CAPTCHA error.
- **Token replay:** two sequential requests using the same Turnstile token produced only one email; the second request was rejected with a CAPTCHA error.
- **Full Page Cache:** submission with a confirmed cache `HIT` in a separate Edge InPrivate session succeeded without a session/form key error, and email arrived.
- **Success-page privacy:** opening the success URL in a new InPrivate session shows an empty newsletter email field without the previous session's email.

## Configuration

Configuration is available under:

```text
Stores > Configuration > MB Contact Form > Contact Form
```

Available settings include:

1. Module status and allowed customer groups.
2. Form title and field labels.
3. Sender, recipient, and Magento Email Template.
4. Additional fields and their validation.
5. Google reCAPTCHA or Cloudflare Turnstile keys.

   **Note:** select the correct **Store View** in the scope selector before entering keys. Verify the CAPTCHA provider and both keys for the storefront displaying the form, including inherited values. Save the configuration, clean the cache, and test form submission.

6. Success message, success URL, and newsletter settings.

### Email subject and introduction

Set **Email Subject** and **Email Introduction** in **General Settings** for the correct Store View. The subject supports `%store_name`; an empty subject uses the default. The introduction is plain text with line breaks; an empty value hides it. The built-in email template uses both settings automatically.

For an existing copied template under **Marketing > Communications > Email Templates**, set Template Subject to `{{var email_subject|raw}}` and replace the existing introduction paragraph with:

```html
{{depend email_intro}}
<p>{{var email_intro|escape|nl2br}}</p>
{{/depend}}
```

Available since version `1.2.3`.

## CMS Widget usage

Add the widget through Page Builder / Insert Widget or directly to a CMS page or block:

```text
{{widget type="MB\ContactForm\Block\Widget\Form"}}
```

## Validation

The rules below are defaults; optional standard fields can use different rules selected in the table.

- Message: Latin and Cyrillic letters, ASCII digits, spaces, line breaks, and punctuation.
- First and last name: Latin and Cyrillic letters and spaces.
- Email: strict browser and server-side email validation.
- Company: Latin and Cyrillic letters, ASCII digits, and spaces.
- Phone: ASCII digits only.
- Chinese, Thai, Hebrew, Arabic, Greek, and other unsupported scripts are rejected in fields restricted to supported letters.

The same rules are applied in the browser and again on the server.

## Cache and security

- The widget remains cacheable and respects Magento's customer-group HTTP context.
- The success page intentionally uses `cacheable="false"` because it reads the submitted email from the session once.
- The POST endpoint checks the form key, module status, customer group, all fields, and CAPTCHA token.
- User data is escaped in PHTML and email templates.
- CSP rules allow CAPTCHA hosts; the newsletter `form-action` origin is added dynamically only on the success page.
- The success page uses `NOINDEX,FOLLOW`.

## Screenshots

Screenshots from the staging environment.

### Contact form on Hyvä

![Contact form on Hyvä](docs/screenshots/contact-form.png)

### Mobile view — title and form fields

<img src="docs/screenshots/contact-form-mobile-top.png" alt="Mobile contact form — title and fields" width="390">

### Mobile view — additional fields, Turnstile, and Submit

<img src="docs/screenshots/contact-form-mobile-submit.png" alt="Mobile additional fields, Turnstile protection, and Submit button" width="390">

### Completed form with additional fields and Turnstile protection

![Completed form with additional fields and Turnstile protection](docs/screenshots/contact-form-filled.png)

### First and last name validation

![First and last name validation](docs/screenshots/name-validation.png)

### Email address validation

![Email address validation](docs/screenshots/email-validation.png)

### Submission confirmation and newsletter subscription

![Submission confirmation and newsletter subscription](docs/screenshots/success-newsletter.png)

### Email notification with the message and additional fields

![Email notification with the message and additional fields](docs/screenshots/email-notification.png)

### Admin settings, additional fields, and CAPTCHA

![Admin settings, additional fields, and CAPTCHA](docs/screenshots/admin-settings.png)

### Spam protection

![Spam protection](docs/screenshots/spam-protection.png)

### CMS widget code

![CMS widget code](docs/screenshots/widget-code.png)

### Field Labels

![Field Labels](docs/screenshots/field-labels.png)

### Email headers and Mailpit HTML compatibility check

The screenshot shows the `From`, `To`, and `Reply-To` headers delivered to email clients, together with the Mailpit `HTML Check` compatibility result for the built-in email template.

![Email headers and Mailpit HTML compatibility check](docs/screenshots/mobile-form.png)

## Installation

### Composer installation (GitHub)

For a public repository, use the HTTPS URL below; GitHub authentication is not required to read public code (Composer may request a token if the GitHub API rate limit is reached). Unauthenticated access applies once the repository visibility is set to Public.
Run from the Magento root directory:

```bash
composer config repositories.mb-contact-form vcs https://github.com/marvikonvic/MB_ContactForm.git
composer require mb/module-contact-form:1.2.3 --prefer-dist
```

Composer automatically registers the module from its package in `vendor`.
Do not also install a copy in `app/code`. After installation, run the Magento activation commands below.

### ZIP installation

Extract the module into:

```text
app/code/MB/ContactForm
```

Then run from the Magento root directory:

```bash
bin/magento module:enable MB_ContactForm
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
```

In Production mode, deploy static content for the active locales and themes:

```bash
bin/magento setup:static-content:deploy -f en_US sr_Latn_RS
```

## Updating

Back up the database, code, and configuration and test the new version on staging first. Run commands from the Magento root as the Magento filesystem owner.

### Composer installation

To upgrade an earlier version (including 1.0.x) to the published 1.2.3 tag:

```bash
bin/magento maintenance:enable
composer config repositories.mb-contact-form vcs https://github.com/marvikonvic/MB_ContactForm.git
composer require mb/module-contact-form:1.2.3 --prefer-dist --with-dependencies
bin/magento setup:upgrade
bin/magento setup:di:compile
```

In Production mode, next run `bin/magento setup:static-content:deploy -f en_US sr_Latn_RS`, adjusted for active locales. After successful completion:

```bash
bin/magento cache:clean
bin/magento maintenance:disable
```

If any step fails, stop and resolve the error or restore the backup before disabling maintenance mode. For future releases, replace `1.2.3` with the desired published tag. A Composer requirement pinned to an exact version will not advance to a new release with a plain `composer update`.

### Manual app/code installation

Download the [v1.2.3 tag ZIP](https://github.com/marvikonvic/MB_ContactForm/archive/refs/tags/v1.2.3.zip). Back up the existing `app/code/MB/ContactForm` outside the Magento tree and review local changes. In maintenance mode, replace the whole directory with `app/code/MB/ContactForm` from the archive, preserving correct filesystem ownership. Run the same Magento upgrade, compile, static-content (Production), and cache commands above. Do not combine manual and Composer installations.

When upgrading from 1.0.x, review field labels/order and custom email templates: since 1.2.0, `name` replaces `firstname`/`lastname`. Check CAPTCHA keys for the correct Store View, submit a test message, and confirm email receipt. See the version history for details.

## Documentation

Full technical documentation and the staging checklist are available in the [module README](app/code/MB/ContactForm/README.md).

## License

The module is distributed under the [GNU General Public License, version 3 or any later version](LICENSE.md) (`GPL-3.0-or-later`). Copyright © 2026 MB.

The copyright holder also offers earlier MB Contact Form releases under GPL-3.0-or-later.

## Version history

### New in version 1.2.3

- Store View-scoped Email Subject and Email Introduction in General Settings.
- The built-in email template uses the new subject and introduction; see the instructions above for copied templates.
- GPL-3.0-or-later licensing, updated badges, and installation/update instructions.
- PHP/XML checks passed; the user confirmed successful staging tests of email delivery and all settings, including Email Subject and Email Introduction.

### New in version 1.2.2

Restored the `action primary` class on Submit and Subscribe buttons, reverting the temporary `pagebuilder-button-primary` change. The obsolete 1.0.0 ZIP is no longer included in the package. The Composer version matches tag `v1.2.2`.

Staging confirmation and screenshots refer to version 1.2.1; version 1.2.2 has not been separately tested in a Magento runtime environment. CAPTCHA settings remain scoped by Store View.

### New in version 1.2.1

The Remove checkbox is replaced by a **Remove / Restore** button. Clicking toggles the field state; **Save Config** saves the change. Email and Message remain protected.

### New in version 1.2.0

- First Name and Last Name are replaced by **Name** (`name`), required by default, allowing Latin/Cyrillic letters and spaces. It can be removed or made optional.
- Legacy name rows are replaced when configuration is read, retaining the earlier sort position. The new Name is enabled and required even if a legacy field was removed. Other settings are preserved. Saving the table persists the new format only in the selected scope.
- Email order: **Name → Email Address → Company Name → Phone Number → Message → Additional information**. Reply-To uses Name.
- For copied email templates, replace firstname/lastname variables with `name`, `label_name`, and the `show_name` condition, or select the built-in template.
- Version 1.2.0 has local verification; email delivery and Admin rendering still require staging verification.

### Historical notes for version 1.1.0

- **Field Labels → Fields** is a table with Code, Label, Type, Required, Validation, Options (comma-separated), Sort Order, and Action.
- Standard codes are fixed: `message`, `firstname`, `lastname`, `email`, `company`, `telephone`.
- **Email and Message** remain enabled and required. Required is locked to Yes; type and validation are protected. Their labels and Sort Order can be changed.
- **Action → Remove** excludes an optional field from the form and email. Clearing the checkbox restores it without losing its settings.
- Sort Order changes the standard field order on the form. Additional Fields remain a separate table and follow in their own Sort Order.
- Email always uses this order: First Name, Last Name, Email Address, Company Name, Phone Number, Message, Additional information. Removed fields are omitted.
- **Submit Button Label** remains separately configurable per Store View; CTA styling is retained.
- Admin translations follow the administrator's interface locale: English source text and Serbian Latin (`sr_Latn_RS`). Manually entered standard field labels are stored per Store View.
- Until the new table is saved, existing 1.0.4 labels are used automatically with the original default order and required flags. Existing scope values are not overwritten.

**Verification:** version 1.1.0 has local contract tests and PHP/PHTML/XML checks. The staging table and screenshots above confirm Magento runtime verification of version 1.2.1.

**Email templates:** the built-in template respects removed fields. If an older copied template is selected under Marketing → Email Templates, update it with the new `show_firstname`, `show_lastname`, `show_company`, and `show_telephone` conditions, or select the built-in template.

Magento 2 contact form compatible with Luma and Hyvä themes. The module provides a cache-friendly CMS widget, Store View configuration, customizable fields, Magento Email Templates, and a choice between Google reCAPTCHA v2 and Cloudflare Turnstile protection.

- **1.1.0:** Standard field table, protected Email/Message, dynamic validation, fixed email order, and expanded Admin translations.

- **1.0.4:** Adds 24 px of spacing above and below the form and confirmation content, plus CTA styling for the Subscribe button.
- **1.0.3:** Sets the form title to 20 px and bold (700).
- **1.0.2:** Widens the Additional Fields table and adds CTA styling to the Submit button.
- **1.0.1:** Fixes the Admin tab identifier for Magento XML validation.
