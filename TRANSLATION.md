# Translation Guide

## Overview

The iTop Portal Personal Tokens extension supports all iTop languages. This document provides guidance for translators.

## Available Languages

The extension includes dictionary files for all 17 iTop-supported languages:

| Language Code | Language | Native Name |
|--------------|----------|-------------|
| `cs` | Czech | Čeština |
| `da` | Danish | Dansk |
| `de` | German | Deutsch |
| `en` | English | English |
| `en_gb` | British English | British English |
| `es_cr` | Spanish | Español, Castellano |
| `fr` | French | Français |
| `hu` | Hungarian | Magyar |
| `it` | Italian | Italiano |
| `ja` | Japanese | 日本語 |
| `nl` | Dutch | Nederlands |
| `pl` | Polish | Polski |
| `pt_br` | Brazilian | Brazilian |
| `ru` | Russian | Русский |
| `sk` | Slovak | Slovenčina |
| `tr` | Turkish | Türkçe |
| `zh_cn` | Chinese | 简体中文 |

## Current Status

- ✅ **English (en)**: Complete - reference translation
- ❌ **All other languages**: English fallback text with `// TODO: Translate` markers

## How to Translate

### 1. Choose Your Language

Find the dictionary file for your language:
```
<language_code>.dict.itop-portal-personal-tokens.php
```

For example: `de.dict.itop-portal-personal-tokens.php` for German

### 2. Open the File

Each file contains PHP arrays with translation strings:

```php
Dict::Add('DE DE', 'German', 'Deutsch', array(
    'Portal:PersonalTokens:Title' => 'Personal API Tokens', // TODO: Translate
    'Portal:PersonalTokens:Description' => 'Create and manage personal tokens for API access', // TODO: Translate
    ...
));
```

### 3. Translate the Strings

Replace the English text with your language, keeping:
- The array key unchanged (e.g., `'Portal:PersonalTokens:Title'`)
- Special placeholders like `%1$d` for numbers
- Escaped quotes `\'` where needed

**Before**:
```php
'Portal:PersonalTokens:Title' => 'Personal API Tokens', // TODO: Translate
```

**After (German example)**:
```php
'Portal:PersonalTokens:Title' => 'Persönliche API-Tokens',
```

### 4. Handle Special Cases

#### Placeholders
Some strings contain placeholders:
```php
'Portal:PersonalTokens:MaxTokensReached' => 'Maximum number of tokens reached (%1$d)'
```

The `%1$d` will be replaced with a number. Make sure to keep it in your translation:
```php
'Portal:PersonalTokens:MaxTokensReached' => 'Maximale Anzahl von Tokens erreicht (%1$d)'
```

#### Escaped Quotes
Some strings contain apostrophes that need escaping:
```php
'Portal:PersonalTokens:CopyWarning' => 'Make sure to copy your token now. You won\'t be able to see it again!'
```

Use `\'` for apostrophes:
```php
'Portal:PersonalTokens:CopyWarning' => 'Kopieren Sie Ihr Token jetzt. Sie können es später nicht mehr sehen!'
```

### 5. Remove TODO Comments

After translating, remove the `// TODO: Translate` comment:

**Before**:
```php
'Portal:PersonalTokens:Title' => 'Personal API Tokens', // TODO: Translate
```

**After**:
```php
'Portal:PersonalTokens:Title' => 'Persönliche API-Tokens',
```

## Translation Categories

The dictionary is organized into categories:

### Menu Items
Portal navigation and menu entries

### Portal UI
Main interface text and descriptions

### Token Fields
Field labels and help text

### Actions
Button labels (Create, Delete, Regenerate)

### Messages
Success and confirmation messages

### Buttons
Standard button labels (Cancel, Close)

### Error Messages
Error states and warnings

## Testing Your Translation

1. Copy your translated file to the extension directory
2. Clear iTop cache: `rm -rf /path/to/itop/data/cache-*`
3. Change your iTop language in user preferences
4. Navigate to Profile → Personal API Tokens
5. Verify all text appears in your language

## Submitting Translations

### Via Pull Request
1. Fork the repository
2. Create a branch: `git checkout -b translation-<language_code>`
3. Commit your translation: `git commit -m "Add <language> translation"`
4. Push and create a Pull Request

### Via Email
Send your translated file to the maintainers with:
- Language code
- Your name (for credits)
- Any notes about translation choices

## Translation Tips

### Be Concise
Portal UI has limited space. Keep translations brief while maintaining clarity.

### Use Technical Terms Consistently
- "Token" - use the established term in your language (or keep as "Token" if commonly used)
- "API" - typically kept as "API"
- "Scope" - translate or use the technical term based on your language's conventions

### Context
The extension manages API tokens for portal users:
- **Application Name**: What the user will call their token (e.g., "Nextcloud Integration")
- **Scope**: API permissions (REST/JSON, Export)
- **Expiration**: When the token stops working
- **Regenerate**: Create a new token value (old one becomes invalid)

## Need Help?

- Check existing iTop translations for consistency
- Ask in the iTop community forum
- Reference the English version for context
- Look at similar extensions for terminology

## Credits

Contributors who provide translations will be credited in the CHANGELOG.md file.

Thank you for helping make this extension accessible to the global iTop community!
