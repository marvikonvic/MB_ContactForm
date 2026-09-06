# MB Contact Form 1.0.4

Magento 2 kontakt forma kompatibilna sa Luma i Hyvä temama. Modul pruža cache-friendly CMS widget, Store View konfiguraciju, prilagodljiva polja, Magento Email Templates i izbor između Google reCAPTCHA v2 i Cloudflare Turnstile zaštite.

## DEMO

[Isprobajte kontakt formu](https://stagento.com/kontakt)

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
| MB Contact Form | 1.0.4 |
| PHP | 8.3.33 |
| Magento | 2.4.7-p3 |
| Hyvä Theme Module | 1.5.2 |

## Podešavanje

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
6. success poruka, success URL i newsletter podešavanja.

## CMS Widget

Widget se može dodati kroz Page Builder / Insert Widget ili direktno u sadržaj CMS stranice ili bloka:

```text
{{widget type="MB\ContactForm\Block\Widget\Form"}}
```

## Validacija

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

## Instalacija

### Composer (GitHub)

Za privatni repozitorijum prethodno podesiti Composer GitHub autentifikaciju.
Iz Magento root foldera pokrenuti:

```bash
composer config repositories.mb-contact-form vcs https://github.com/marvikonvic/MB_ContactForm.git
composer require mb/module-contact-form:1.0.4 --prefer-dist
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

## Dokumentacija

Kompletna tehnička dokumentacija i staging kontrolna lista nalaze se u [README fajlu modula](app/code/MB/ContactForm/README.md).

## Istorija verzija

- **1.0.4:** Dodaje 24 px razmaka iznad i ispod forme i potvrde, kao i CTA stil Subscribe dugmeta.
- **1.0.3:** Postavlja naslov forme na 20 px i bold (700).
- **1.0.2:** Proširuje Additional Fields tabelu i dodaje CTA stil Submit dugmetu.
- **1.0.1:** Ispravlja identifikator Admin taba za Magento XML validaciju.
