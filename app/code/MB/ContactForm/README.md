# MB Contact Form 1.0.0

Standalone Magento 2 contact form widget compatible with Luma and Hyvä themes.

## Funkcionalnosti

- Full Page Cache kompatibilan CMS widget.
- Store View konfiguracija svih vrednosti, naziva polja, poruka, email adresa i CAPTCHA ključeva.
- Vidljivost po customer grupama; prazna selekcija znači sve grupe, uključujući `NOT LOGGED IN`.
- Obavezna polja: poruka, ime, prezime i email.
- Opciona polja: kompanija i telefon.
- Admin može dodati `text`, `textarea` i `select` polja, odrediti obaveznost, redosled i validaciju.
- Izmenjivi `Sender Email`, `Sender Name`, `Recipient Email` i Magento `Email Template`; email posetioca se postavlja kao `Reply-To`.
- Google reCAPTCHA v2 Checkbox ili Cloudflare Turnstile sa obaveznom serverskom proverom tokena.
- Posebna `/contact-success/` stranica sa izmenjivom porukom i unapred popunjenim newsletter emailom.
- Engleski izvorni tekstovi i `sr_Latn_RS` prevodi.
- Responsive, semantički HTML, pristupačne labele i SEO meta podešavanja success stranice.
- Hyvä kompatibilnost: storefront ne koristi RequireJS, jQuery, Knockout niti Magento UI JavaScript.
- Admin konfiguraciji mogu pristupiti samo administratori čija rola ima `Resource Access = All / Full Access`.

## Instalacija

Kopirati sadržaj modula u:

```text
app/code/MB/ContactForm
```

Zatim u Magento root folderu pokrenuti:

```bash
bin/magento module:enable MB_ContactForm
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
```

U Production modu ponovo generisati static content za aktivne locale/theme kombinacije, na primer:

```bash
bin/magento setup:static-content:deploy -f en_US sr_Latn_RS
```

## Podešavanje

Otvoriti `Stores > Configuration > MB Contact Form > Contact Form` i podesiti:

1. status i dozvoljene customer grupe;
2. naslov i nazive polja;
3. email pošiljaoca, primaoca i Magento Email Template;
4. dodatna polja;
5. Google ili Cloudflare ključeve;
6. success/newsletter tekstove i URL-ove po Store View-u.

Sekcija koristi Magento ACL resurs `Magento_Backend::all`. Ne prikazuje se administratorima sa prilagođenim (`Custom`) dozvolama i ne može im se naknadno dodeliti kao pojedinačna dozvola.

Za Google opciju potreban je reCAPTCHA v2 Checkbox par ključeva. Secret ključevi se čuvaju Magento encrypted backend modelom i nikada se ne prikazuju na storefrontu.

Početni template je `MB Contact Form Notification`. Za drugačiji izgled otvoriti `Marketing > Communications > Email Templates`, učitati ili kopirati početni template, izmeniti ga standardnim Magento editorom i izabrati ga u konfiguraciji modula. Izbor se može razlikovati po Store View-u.

## CMS Widget

Kod se može kopirati iz Admin konfiguracije i nalepiti u CMS stranicu ili block:

```text
{{widget type="MB\ContactForm\Block\Widget\Form"}}
```

Isti widget se može dodati i kroz Page Builder / Insert Widget dijalog kao `MB Contact Form`.

## Validacija polja

- Poruka: latinična i ćirilična slova, brojevi `0-9`, razmaci, prelomi redova i interpunkcija; najviše 5000 znakova.
- Ime/prezime: latinična i ćirilična slova i razmaci.
- Email: standardna email validacija; pored slova, brojeva i `@` dozvoljeni su `.`, `_`, `+` i `-`, jer su potrebni za validne adrese.
- Kompanija: latinična i ćirilična slova, brojevi `0-9` i razmaci.
- Telefon: ASCII brojevi `0-9` bez `+`, crtica i razmaka.

Ista pravila se primenjuju u vanilla JavaScript UX validaciji i obavezno ponovo na serveru.
Kinesko, tajlandsko, hebrejsko, arapsko i ostala pisma nisu dozvoljena u poljima koja prihvataju slova.

## Cache i bezbednost

- Widget ostaje cacheable i koristi Magento customer-group HTTP context, koji je deo FPC varijante.
- Success stranica je jedina namerno `cacheable="false"` stranica zato što jednokratno čita email iz korisničke sesije.
- POST endpoint zahteva validan form key i proverava enabled status, customer grupu, sva polja i CAPTCHA token.
- Svi ispisi su escaped; email template escape-uje korisničke vrednosti.
- CSP whitelist sadrži Google reCAPTCHA i Cloudflare Turnstile hostove, dok se tačno poreklo podešenog newsletter URL-a dinamički dodaje u `form-action` samo na success stranici.
- Success stranica ima `NOINDEX,FOLLOW`; SEO naslov i opis se podešavaju po Store View-u. SEO CMS stranice sa widgetom podešava se u standardnom CMS Page interfejsu.

## Provera na staging instalaciji

Obavezno proveriti oba aktivna storefront locale-a i Hyvä temu:

1. gost i svaka dozvoljena/nedozvoljena customer grupa;
2. FPC hit nakon prvog učitavanja CMS stranice;
3. ispravan i neispravan form key;
4. svako pravilo validacije i maksimalnu dužinu;
5. Google i Turnstile success/failure/expired token;
6. email From/To/Reply-To i SPF/DMARC isporuku;
7. success redirect, jednokratno popunjen email i newsletter POST;
8. mobilni prikaz i browser konzolu bez CSP grešaka.
