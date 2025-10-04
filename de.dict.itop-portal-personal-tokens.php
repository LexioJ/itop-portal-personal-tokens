<?php
/**
 * German language dictionary for Portal Personal Tokens extension
 *
 * @copyright   Copyright (C) 2025
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

Dict::Add('DE DE', 'German', 'Deutsch', array(
    // Menu items
    'Menu:PersonalTokens' => 'Persönliche Tokens',
    'Menu:PersonalTokens+' => 'Verwalten Sie Ihre persönlichen API-Tokens',

    // Portal UI
    'Portal:PersonalTokens:Title' => 'Persönliche API-Tokens',
    'Portal:PersonalTokens:Description' => 'Erstellen und verwalten Sie persönliche Tokens für den API-Zugriff',
    'Portal:PersonalTokens:NoTokens' => 'Sie haben keine aktiven Tokens',
    'Portal:PersonalTokens:CreateNew' => 'Neues Token erstellen',

    // Token fields
    'Class:PersonalToken/Attribute:application' => 'Anwendungsname',
    'Class:PersonalToken/Attribute:application+' => 'Beschreibender Name für dieses Token',
    'Class:PersonalToken/Attribute:scope' => 'Bereich',
    'Class:PersonalToken/Attribute:scope+' => 'API-Berechtigungen für dieses Token',
    'Class:PersonalToken/Attribute:expiration_date' => 'Ablaufdatum',
    'Class:PersonalToken/Attribute:expiration_date+' => 'Token läuft an diesem Datum ab',
    'Class:PersonalToken/Attribute:last_use_date' => 'Zuletzt verwendet',
    'Class:PersonalToken/Attribute:last_use_date+' => 'Datum der letzten Token-Verwendung',
    'Class:PersonalToken/Attribute:use_count' => 'Verwendungsanzahl',
    'Class:PersonalToken/Attribute:use_count+' => 'Anzahl der Token-Verwendungen',

    // Actions
    'Portal:PersonalTokens:Create' => 'Token erstellen',
    'Portal:PersonalTokens:Regenerate' => 'Regenerieren',
    'Portal:PersonalTokens:Delete' => 'Löschen',
    'Portal:PersonalTokens:Copy' => 'In Zwischenablage kopieren',

    // Messages
    'Portal:PersonalTokens:Created' => 'Token erfolgreich erstellt',
    'Portal:PersonalTokens:Regenerated' => 'Token erfolgreich regeneriert',
    'Portal:PersonalTokens:Deleted' => 'Token erfolgreich gelöscht',
    'Portal:PersonalTokens:CopyWarning' => 'Kopieren Sie Ihr Token jetzt. Sie können es später nicht mehr sehen!',
    'Portal:PersonalTokens:MaxTokensReached' => 'Maximale Anzahl von Tokens erreicht (%1$d)',
    'Portal:PersonalTokens:ConfirmDelete' => 'Sind Sie sicher, dass Sie dieses Token löschen möchten?',
    'Portal:PersonalTokens:ConfirmRegenerate' => 'Sind Sie sicher, dass Sie dieses Token regenerieren möchten? Anwendungen, die das alte Token verwenden, funktionieren nicht mehr.',

    // Additional translations
    'Portal:PersonalTokens:Loading' => 'Tokens werden geladen...',
    'Portal:PersonalTokens:Application' => 'Anwendungsname',
    'Portal:PersonalTokens:Application:Placeholder' => 'z.B. Nextcloud, Mobile App, Skript',
    'Portal:PersonalTokens:Application:Help' => 'Geben Sie Ihrem Token einen aussagekräftigen Namen zur Identifizierung',
    'Portal:PersonalTokens:Description:Placeholder' => 'Optionale Beschreibung der Token-Verwendung',
    'Portal:PersonalTokens:ExpiryDays' => 'Läuft ab in',
    'Portal:PersonalTokens:Days' => 'Tagen',
    'Portal:PersonalTokens:LastUsed' => 'Zuletzt verwendet',
    'Portal:PersonalTokens:UseCount' => 'Verwendungen',
    'Portal:PersonalTokens:Actions' => 'Aktionen',
    'Portal:PersonalTokens:ExpiryDate' => 'Läuft ab',
    'Portal:PersonalTokens:Scope' => 'Bereich',

    // Token creation success
    'Portal:PersonalTokens:TokenCreated' => 'Token erfolgreich erstellt',
    'Portal:PersonalTokens:TokenCreated:Success' => 'Ihr persönliches Token wurde erfolgreich erstellt!',
    'Portal:PersonalTokens:TokenCreated:Warning' => 'Dies ist das einzige Mal, dass Sie den Token-Wert sehen. Kopieren Sie ihn jetzt und bewahren Sie ihn sicher auf.',
    'Portal:PersonalTokens:YourToken' => 'Ihr persönliches Token',

    // Buttons
    'Portal:Button:Cancel' => 'Abbrechen',
    'Portal:Button:Close' => 'Schließen',

    // Error messages
    'Portal:PersonalTokens:Error:LoadFailed' => 'Fehler beim Laden der persönlichen Tokens',
    'Portal:PersonalTokens:Error:CreateFailed' => 'Fehler beim Erstellen des persönlichen Tokens',
    'Portal:PersonalTokens:Error:DeleteFailed' => 'Fehler beim Löschen des persönlichen Tokens',
    'Portal:PersonalTokens:Error:Creation' => 'Token-Erstellung fehlgeschlagen',
    'Portal:PersonalTokens:Error:NotFound' => 'Token nicht gefunden',
    'Portal:PersonalTokens:Error:Permission' => 'Sie haben keine Berechtigung, Tokens zu verwalten',
));
