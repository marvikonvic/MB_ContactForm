# MB Contact Form 1.2.1

## Srpski — nadogradnja

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

**Provera:** verzija 1.1.0 je proverena lokalnim testovima pravila i PHP/PHTML/XML proverama. Staging tabela i screenshotovi ispod odnose se na ranije potvrđenu verziju 1.0.4; nova verzija još zahteva Magento runtime proveru.

**Email šabloni:** ugrađeni šablon poštuje uklonjena polja. Ako je izabran ranije kopiran šablon iz Marketing → Email Templates, ažurirajte ga novim uslovima `show_firstname`, `show_lastname`, `show_company`, `show_telephone` ili izaberite ugrađeni šablon.

## English — upgrade

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

**Verification:** version 1.1.0 has local contract tests and PHP/PHTML/XML checks. The staging table and screenshots below describe the previously verified 1.0.4 release; the new version still requires Magento runtime verification.

**Email templates:** the built-in template respects removed fields. If an older copied template is selected under Marketing → Email Templates, update it with the new `show_firstname`, `show_lastname`, `show_company`, and `show_telephone` conditions, or select the built-in template.

---

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


## Licenca

Modul se distribuira pod vlasničkom [MB trajnom licencom za modul](LICENSE.md). Licenca dozvoljava trajno korišćenje na neograničenom broju Magento instalacija i domena koji su u vlasništvu ili pod neposrednom kontrolom istog korisnika licence.

Modul trenutno nije u komercijalnoj prodaji; privatna distribucija zahteva izričito odobrenje davaoca licence. Puni uslovi na engleskom i srpskom nalaze se u licencnom fajlu.
