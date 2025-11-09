# Offene Fragen zur Integration in authent-token

**Context:** Integration der Standalone-Extension `itop-portal-personal-tokens` in `Combodo/authent-token` (siehe [Issue #3](https://github.com/Combodo/combodo-my-account/issues/3))

Hallo Olivier und iTop-Team,

ich habe einen detaillierten Integrationsplan erstellt (siehe `INTEGRATION_PLAN.md`) und möchte gerne einige Fragen klären, bevor ich mit der eigentlichen Code-Migration beginne:

## 1. Architektur & Struktur

**1.1** Wie ist der bestehende Code in `authent-token` strukturiert? Gibt es bereits eine Trennung zwischen Console- und Portal-Funktionalität?

**1.2** Bevorzugt ihr eine bestimmte Verzeichnisstruktur? Mein Vorschlag wäre:
```
authent-token/
├── src/
│   ├── Console/        # Bestehende Console-Funktionalität
│   └── Portal/         # Neue Portal-Funktionalität
│       └── Hook/
└── templates/
    ├── console/
    └── portal/
```

**1.3** Gibt es Coding-Standards, Style-Guidelines oder Konventionen für das authent-token Projekt, die ich beachten sollte?

## 2. Bestehende Funktionalität

**2.1** Existiert bereits Portal-bezogener Code in authent-token? Falls ja: Wo und wie ist dieser strukturiert?

**2.2** Gibt es bereits ein Maintenance-Script für das Cleanup abgelaufener Tokens? Die Standalone-Extension enthält `maintenance/cleanup_expired_tokens.php`.

**2.3** Wie ist die aktuelle Dictionary-Struktur in authent-token organisiert? Gibt es Namenskonventionen für Portal vs. Console Strings?

## 3. Testing & Quality

**3.1** Welches Test-Framework wird verwendet? Ich sehe ein `tests/`-Verzeichnis im Repository.

**3.2** Gibt es spezifische Test-Anforderungen oder Coverage-Erwartungen für Pull Requests?

**3.3** Gibt es eine CI/CD Pipeline, die ich beachten muss?

## 4. Konfiguration & Berechtigungen

**4.1** Sollen die Konfigurations-Parameter portal-spezifisch sein oder global mit der Console-Seite geteilt werden?

Aktuell in der Standalone-Extension:
```php
'portal_personal_tokens' => [
    'max_tokens_per_user' => 5,      // 1-20
    'default_expiry_days' => 90,     // 30-365
]
```

**4.2** Müssen Portal-User-Berechtigungen im authent-token Datamodel erweitert werden, oder erfolgt die Permission-Extension weiterhin separat?

Die Standalone-Extension hat aktuell ein eigenes `datamodel.itop-portal-personal-tokens.xml`.

## 5. Versionierung & Release

**5.1** In welcher Version von authent-token soll die Integration erscheinen?

**5.2** Gibt es einen Release-Zyklus zu beachten?

**5.3** Welche Mindestversion von `itop-portal-base` wird von authent-token vorausgesetzt?

## 6. Abwärtskompatibilität & Migration

**6.1** Müssen Benutzer, die aktuell die Standalone-Extension `itop-portal-personal-tokens` nutzen, unterstützt werden?

**6.2** Falls ja: Soll ein Migrations-Script oder -Guide bereitgestellt werden?

**6.3** Wie soll mit bestehenden Tokens umgegangen werden? (Diese sollten in der gleichen `PersonalToken`-Klasse gespeichert sein, da die Standalone-Extension bereits `authent-token/2.0.0` als Abhängigkeit hat)

## 7. Namespaces

**7.1** Ist der folgende Namespace-Wechsel korrekt?

```php
// Standalone-Extension
namespace Combodo\iTop\Portal\PersonalTokens\Hook;

// Nach Integration
namespace Combodo\iTop\AuthentToken\Portal\Hook;
```

## 8. Prozess

**8.1** Soll ich direkt einen Pull Request gegen `Combodo/authent-token` erstellen, oder möchtet ihr den Code selbst integrieren?

**8.2** Gibt es einen bevorzugten Branch für Feature-Entwicklung? (z.B. `develop`, `main`)

**8.3** Gibt es spezifische Requirements für PR-Beschreibungen oder Commit-Messages?

---

## Zusammenfassung meiner Planung

Mein aktueller Plan umfasst:

1. **Code-Migration:**
   - 2 PHP Hook-Klassen (~430 Zeilen)
   - 3 Twig Templates (UI, JS, CSS)
   - 17 Sprachdateien (DE/EN vollständig übersetzt)
   - 1 Maintenance Script

2. **Änderungen:**
   - Namespace-Anpassungen
   - Template-Pfad-Anpassungen
   - Dictionary-Merge
   - Modul-Konfiguration erweitern

3. **Testing:**
   - Manuelle Tests (Token-Erstellung, -Regenerierung, -Löschung)
   - Unit-Tests (falls erforderlich)
   - REST-API Integration-Tests

4. **Dokumentation:**
   - README.md Update
   - CHANGELOG.md
   - Migrations-Guide (falls benötigt)

**Geschätzter Zeitaufwand:** ~2 Wochen (Netto-Entwicklungszeit)

---

Vielen Dank für eure Unterstützung!

Bei Rückfragen stehe ich gerne zur Verfügung.

Beste Grüße
