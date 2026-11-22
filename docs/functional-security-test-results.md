# MB Contact Form — funkcionalni i osnovni bezbednosni testovi

[Srpski](#srpski) | [English](#english)

## Srpski

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

[Povratak na glavni README](../README.md#srpski).

## English

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

[Back to the main README](../README.md#english).
