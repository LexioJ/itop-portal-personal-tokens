# Integrationsplan: Portal Personal Tokens → authent-token

**Datum:** 9. November 2025
**Version:** 1.0
**Status:** Planung

## 1. Executive Summary

Die eigenständige Extension `itop-portal-personal-tokens` soll in das offizielle Combodo-Modul `authent-token` integriert werden. Dies wurde vom iTop-Team vorgeschlagen (siehe [Issue #3](https://github.com/Combodo/combodo-my-account/issues/3)), da sie bereits eine ähnliche Funktionalität für die Console-Seite von iTop in diesem Modul implementiert haben.

**Ziel:** Portal-Benutzer sollen Personal API Tokens direkt im authent-token Modul verwalten können, analog zur bestehenden Console-Funktionalität.

## 2. Ist-Situation

### 2.1 Aktuelle Standalone Extension

**Repository:** `LexioJ/itop-portal-personal-tokens`

**Architektur:**
- **2 Hook-Klassen:**
  - `PersonalTokensTabExtension` (iUserProfileTabExtension)
  - `PersonalTokensUserProfileExtension` (iUserProfileTabContentExtension)
- **3 Twig Templates:** UI, JavaScript, CSS
- **17 Sprachdateien** (vollständig übersetzt: DE/EN)
- **1 Maintenance Script:** `cleanup_expired_tokens.php`

**Funktionalität:**
- Fügt Tab "Personal API Tokens" zum Portal-Benutzerprofil hinzu
- Token-Erstellung mit konfigurierbaren Scopes (REST/JSON, Export)
- Token-Verwaltung (anzeigen, regenerieren, löschen)
- Sicherheitsfeatures: CSRF-Protection, XSS-Prevention, User-Isolation

**Abhängigkeiten:**
```php
'authent-token/2.0.0',
'itop-portal-base/3.0.0'
```

**Version:** 1.1.0 (produktionsreif)

### 2.2 Ziel-Repository authent-token

**Repository:** `Combodo/authent-token`

**Beschreibung:**
- iTop-Modul für Token-basierte Authentifizierung
- Unterstützt Console-seitige Token-Verwaltung ("My Account")
- Stellt `PersonalToken`-Klasse und `AuthentTokenService` bereit
- PHP 96.5%, Twig 3.5%

**Struktur:**
```
authent-token/
├── src/                # PHP Klassen
├── dictionaries/       # Übersetzungen
├── templates/          # Twig Templates
├── tests/              # Tests
├── module.*.php        # Modul-Definition
├── model.*.php         # Datenmodell
└── datamodel.*.xml     # XML-Schema
```

## 3. Soll-Situation

### 3.1 Integrationsziel

Das authent-token Modul soll um Portal-Funktionalität erweitert werden:

```
authent-token/
├── src/
│   ├── Console/        # Bestehend: Console-seitige Token-Verwaltung
│   └── Portal/         # NEU: Portal-seitige Token-Verwaltung
│       └── Hook/
│           ├── PersonalTokensTabExtension.php
│           └── PersonalTokensUserProfileExtension.php
├── templates/
│   ├── console/        # Bestehende Console-Templates
│   └── portal/         # NEU: Portal-Templates
│       ├── personal_tokens_tab.html.twig
│       ├── personal_tokens_tab.ready.js.twig
│       └── personal_tokens_tab.css.twig
├── dictionaries/       # Erweitert um Portal-Strings
├── maintenance/
│   └── cleanup_expired_tokens.php  # Ggf. bereits vorhanden
└── ...
```

### 3.2 Namespaces

**Aktuell (Standalone):**
```php
namespace Combodo\iTop\Portal\PersonalTokens\Hook;
```

**Nach Integration:**
```php
namespace Combodo\iTop\AuthentToken\Portal\Hook;
```

## 4. Technische Analyse

### 4.1 Code-Migration

#### 4.1.1 PHP-Klassen
| Datei | Zeilen | Änderungen |
|-------|--------|------------|
| PersonalTokensTabExtension.php | 67 | Namespace-Anpassung |
| PersonalTokensUserProfileExtension.php | 369 | Namespace + Use-Statements |

**Namespace-Änderung:**
```php
// Alt
namespace Combodo\iTop\Portal\PersonalTokens\Hook;

// Neu
namespace Combodo\iTop\AuthentToken\Portal\Hook;
```

**Service-Import (bereits korrekt):**
```php
use Combodo\iTop\AuthentToken\Service\AuthentTokenService;
```

#### 4.1.2 Templates
- Verschieben nach `templates/portal/`
- Pfad-Anpassungen im Code (siehe 4.3)

#### 4.1.3 Sprachdateien
- Merge mit bestehenden Dictionary-Dateien in `authent-token`
- Präfixe prüfen und harmonisieren (z.B. `Portal:PersonalTokens:*`)

### 4.2 Modul-Konfiguration

**module.authent-token.php** erweitern:

```php
'portal_personal_tokens' => [
    'max_tokens_per_user' => 5,      // 1-20
    'default_expiry_days' => 90,     // 30-365
],
```

**Autoloader** erweitern:
```php
'src/Portal/Hook/PersonalTokensTabExtension.php',
'src/Portal/Hook/PersonalTokensUserProfileExtension.php',
```

### 4.3 Template-Pfade

**PersonalTokensUserProfileExtension.php** Zeile ~104:
```php
// Alt
'portal-personal-tokens/personal_tokens_tab.html.twig'

// Neu
'authent-token/portal/personal_tokens_tab.html.twig'
```

Betrifft 3 Template-Referenzen in der Datei.

### 4.4 Datamodel-Erweiterung

**datamodel.itop-portal-personal-tokens.xml** prüfen:

Ggf. Merge mit `datamodel.authent-token.xml`, falls Portal-User-Berechtigungen erweitert werden müssen.

```xml
<permission id="portal-power-user">
    <actions>
        <action permission="read">PersonalToken</action>
        <action permission="write">PersonalToken</action>
    </actions>
</permission>
```

### 4.5 Maintenance Scripts

`cleanup_expired_tokens.php` prüfen:
- Ist Script bereits in authent-token vorhanden?
- Falls nicht: Integration in `maintenance/`-Verzeichnis

## 5. Migrations-Strategie

### Phase 1: Vorbereitung
1. Fork von `Combodo/authent-token` erstellen
2. Feature-Branch anlegen: `feature/portal-token-management`
3. Aktuelle authent-token Struktur analysieren

### Phase 2: Code-Integration
1. Verzeichnisstruktur erstellen (`src/Portal/`, `templates/portal/`)
2. PHP-Klassen migrieren + Namespace anpassen
3. Templates migrieren + Pfade anpassen
4. Sprachdateien mergen
5. Modul-Konfiguration erweitern
6. Datamodel prüfen/mergen

### Phase 3: Testing
1. Unit-Tests für Portal-Hooks schreiben (falls authent-token Tests nutzt)
2. Manuelle Tests:
   - Token-Erstellung im Portal
   - Token-Regenerierung
   - Token-Löschung
   - Berechtigungsprüfung (User-Isolation)
   - REST-API Zugriff mit generierten Tokens
3. Kompatibilitätstests mit iTop 3.1.0+

### Phase 4: Dokumentation
1. README.md von authent-token erweitern (Portal-Funktionalität)
2. CHANGELOG.md Update
3. Upgrade-Hinweise (falls User die Standalone-Extension nutzen)

### Phase 5: Pull Request
1. PR bei `Combodo/authent-token` einreichen
2. Code-Review abwarten
3. Feedback einarbeiten

## 6. Offene Fragen

### 6.1 An Combodo/iTop-Team:

1. **Struktur-Präferenzen:**
   - Soll die Trennung `src/Console/` und `src/Portal/` erfolgen, oder bevorzugen sie eine andere Struktur?
   - Gibt es Coding-Standards/Guidelines für das authent-token Projekt?

2. **Bestehende Portal-Funktionalität:**
   - Existiert bereits Portal-bezogener Code in authent-token?
   - Falls ja: Wo und wie ist dieser strukturiert?

3. **Maintenance Script:**
   - Gibt es bereits ein Cleanup-Script für abgelaufene Tokens?
   - Falls ja: Muss dies angepasst werden oder ist es universell?

4. **Testing:**
   - Welches Test-Framework wird verwendet?
   - Gibt es spezifische Test-Anforderungen für PRs?

5. **Versionierung:**
   - In welcher Version von authent-token soll die Integration erscheinen?
   - Gibt es einen Release-Zyklus zu beachten?

6. **Abwärtskompatibilität:**
   - Müssen User, die die Standalone-Extension nutzen, unterstützt werden?
   - Soll ein Migrations-Script bereitgestellt werden?

### 6.2 Technische Klärungen:

7. **Dictionary-Merge:**
   - Wie ist die aktuelle Dictionary-Struktur in authent-token?
   - Gibt es Namenskonventionen für Portal vs. Console Strings?

8. **Datamodel:**
   - Müssen Portal-User-Berechtigungen im authent-token Datamodel erweitert werden?
   - Oder erfolgt die Permission-Extension weiterhin separat?

9. **Configuration:**
   - Sollen die Konfigurations-Parameter (`max_tokens_per_user`, `default_expiry_days`) portal-spezifisch sein oder global mit der Console-Seite geteilt werden?

10. **Dependencies:**
    - Welche Mindestversion von `itop-portal-base` wird von authent-token vorausgesetzt?

## 7. Risiken & Abhängigkeiten

### 7.1 Risiken

| Risiko | Wahrscheinlichkeit | Impact | Mitigation |
|--------|-------------------|--------|------------|
| Strukturkonflikt mit bestehendem Code | Mittel | Hoch | Frühe Analyse des authent-token Repos |
| Breaking Changes für Standalone-User | Hoch | Mittel | Migrations-Guide bereitstellen |
| Verzögerung durch Code-Review | Mittel | Niedrig | Frühzeitig PR einreichen |
| Namespace-Konflikte | Niedrig | Hoch | Sorgfältige Planung + Testing |

### 7.2 Abhängigkeiten

- **Zugriff auf Combodo/authent-token:** Fork vorhanden ✓
- **Kenntnis der authent-token Architektur:** Benötigt tiefere Analyse
- **Combodo Code-Review:** Externe Abhängigkeit
- **iTop 3.1.0+ Testumgebung:** Vorhanden (laut Standalone-Doku)

## 8. Zeitplan (Grob)

| Phase | Dauer (geschätzt) | Abhängigkeiten |
|-------|------------------|----------------|
| **1. Vorbereitung** | 1-2 Tage | Zugriff auf authent-token ✓ |
| **2. Code-Integration** | 3-5 Tage | Klärung offener Fragen (Kap. 6) |
| **3. Testing** | 2-3 Tage | Testumgebung |
| **4. Dokumentation** | 1-2 Tage | - |
| **5. Pull Request** | Variable | Combodo Code-Review |
| **TOTAL** | **~2 Wochen (Netto)** | + Review-Zeit |

## 9. Nächste Schritte

### Sofort:
1. ✅ Integrationsplan erstellen (dieses Dokument)
2. ⬜ Offene Fragen an iTop-Team/Olivier stellen (GitHub Issue #3)
3. ⬜ Fork von `Combodo/authent-token` detailliert analysieren

### Nach Klärung:
4. ⬜ Feature-Branch im authent-token Fork anlegen
5. ⬜ Code-Migration durchführen (Phase 2)
6. ⬜ Tests implementieren und ausführen (Phase 3)
7. ⬜ Pull Request einreichen (Phase 5)

---

## Anhang A: Relevante Links

- **GitHub Issue:** https://github.com/Combodo/combodo-my-account/issues/3
- **Standalone Extension:** https://github.com/LexioJ/itop-portal-personal-tokens
- **Ziel-Repository:** https://github.com/Combodo/authent-token
- **iTop Dokumentation:** https://www.itophub.io/wiki/

## Anhang B: Kontakte

- **Olivier (Combodo):** Ansprechpartner laut GitHub Issue #3
- **Community:** iTop Slack/Forum (falls zusätzliche Fragen)

---

**Dokument-Historie:**
- v1.0 (2025-11-09): Initiale Planung basierend auf Issue #3 und Codebase-Analyse
