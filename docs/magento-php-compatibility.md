# MB Contact Form 1.2.5 — PHP / Magento compatibility

Development branch: `feature/magento249-php85-86`. Snapshot: **2026-11-22**.
Version 1.2.5 is prepared in both manifests; no release tag has been published.

## Srpski

Oba Composer manifesta dopuštaju PHP **8.1–8.6**. Postojeća PHP 8.1–8.4 podrška
ostaje sačuvana. Ciljna zvanično podržana kombinacija je **Magento 2.4.9 + PHP 8.5**.
Prethodno odloženo Composer ograničenje za PHP 8.5 uklonjeno je na ovoj grani.

PHP 8.6.0 prolazi izdvojene provere modula, ali **nije zvanično podržana Magento
2.4.9 kombinacija**. Sam Magento u svom Composer manifestu isključuje PHP 8.6.
Proširenje uslova ovog modula ne menja uslove Magento paketa i drugih ekstenzija.

### Potvrđene lokalne provere

| Magento izvor | PHP | Rezultat unit testova |
| --- | --- | --- |
| 2.4.4 | 8.1.34 | 67 testova / 198 provera |
| 2.4.6 | 8.2.33 | 67 testova / 198 provera |
| 2.4.8-p5 | 8.4.27 | 67 testova / 333 provere |
| 2.4.9 + navedene CE zakrpe i hotfix | 8.5.13 | 67 testova / 333 provere |
| 2.4.9 + navedene CE zakrpe i hotfix | 8.6.0 | 67 testova / 333 provere; eksperimentalna provera modula |

Na PHP 8.5.13 i 8.6.0 testovi su pokrenuti sa PHPUnit 12.5.45 i uključenim padom
na PHPUnit notices, deprecations i notices. Ponovljivi postupak primenjen je na
čist izvorni checkout; svih pet zakrpa uspešno je primenjeno i odmah provereno
obrnutom proverom primene. U oba okruženja učitava se svih 21 produkcionih klasa,
prolaze CSP/mail API provere i 33 ugovorne provere standardnih polja.

### Sigurnosni obuhvat

Proveren je Magento Open Source 2.4.9 commit
`755e34dd689021c5165db9d35ecff74f7dc51527` sa sledećim zvaničnim CE zakrpama:

1. `249-2026-07-001-CE.patch` — APSB26-73.
2. `249-2026-08-001-CE.patch` — APSB26-92.
3. `VULN-39341_249.patch` — zasebni APSB26-146 / CVE-2026-75650 hotfix.
4. `249-2026-09-001-CE.patch` — APSB26-138.
5. `249-2026-10-001-CE.patch` — APSB26-153.

SHA-256 arhiva i tačni URL-ovi zaključani su u
[skripti za pripremu](../tests/compatibility/prepare-security-patches.py).
Instalacija modula **ne primenjuje ove zakrpe na prodavnicu**. To je zaseban
Magento operativni postupak. Ovaj spisak ne obuhvata buduće zakrpe posle datuma provere.

### Ograničenja

Ovo nisu pune Magento instalacije: nema baze, frontend servera, DI kompilacije,
stvarnog CAPTCHA servisa ili SMTP isporuke. Pre produkcije ostaju staging provere
instalacije/DI, Admin čuvanja, storefront forme, CSRF/FPC toka, CAPTCHA i prijema mejla.
Rezultati se odnose na Open Source CE klase, ne na sve Adobe Commerce/B2B dodatke.

PHPStan nivo 6 sa `bitexpert/phpstan-magento` 0.44.0 pokrenut je nad produkcionim
PHP kodom. Prijavio je 41 nalaz, pretežno postojeće nedostajuće iterable PHPDoc tipove,
uz nalaze o potpisima callback-a i granicama Magento interfejsa. Nije prošao bez
nalaza; taj rezultat se ne predstavlja kao čista statička analiza. Ova grana ne
uvodi promene poslovne logike radi prikrivanja tih nalaza.

## English

The manifests now permit PHP 8.1–8.6, retaining older PHP support. The supported
platform target is Magento 2.4.9 on PHP 8.5. PHP 8.6 passes isolated module checks
only; it is excluded by Magento 2.4.9's own Composer constraint and is not an
officially supported Magento platform combination.

The local matrix above uses original Magento classes. Patched 2.4.9 on PHP 8.5.13
and 8.6.0 passes 67 tests / 333 assertions, all 21 production-class loading checks,
core CSP/mail API checks and 33 standalone standard-field checks. No store runtime,
database, DI compilation, real CAPTCHA or mail delivery is established by those results.

The patch snapshot contains the July–October 2026 CE isolated patches plus the
separate VULN-39341 hotfix. Archives are checksum-pinned and applied to a disposable
source checkout only. Installing this module does not patch a deployed Magento store.
PHPStan level 6 reports 41 existing typing/interface findings; no clean PHPStan run
is claimed. The automated source-level matrix is defined in
[GitHub Actions](../.github/workflows/compatibility.yml).

## Reproduce / Ponoviti provere

See [the isolated compatibility instructions](../tests/compatibility/README.md).

## Primary sources

- [Adobe system requirements](https://experienceleague.adobe.com/en/docs/commerce-operations/installation-guide/system-requirements).
- [Adobe released versions](https://experienceleague.adobe.com/en/docs/commerce-operations/release/versions).
- [Adobe current security bulletins](https://www.adobe.com/trust/security/bulletins-and-advisories.html).
- [October isolated patches and prerequisite guidance](https://experienceleague.adobe.com/en/docs/experience-cloud-kcs/kbarticles/ka-49323).
- [Separate CVE-2026-75650 hotfix](https://experienceleague.adobe.com/en/docs/commerce-knowledge-base/kb/announcements/commerce-apsb26-146).
- [PHP 8.6 release](https://www.php.net/releases/8.6/en.php).
