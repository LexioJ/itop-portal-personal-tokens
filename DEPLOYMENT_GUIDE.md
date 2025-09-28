# Portal Personal Tokens Extension - Deployment Guide

## Overview
This guide explains how to deploy the Portal Personal Tokens extension to your iTop instance in a way that persists across upgrades.

## Extension Architecture

The extension is packaged as a standard iTop extension that:
- Lives in the `extensions/` directory (survives upgrades)
- Uses iTop's extension framework
- Leverages existing `authent-token` functionality
- Adds Portal UI without modifying core files

## Directory Structure

```
itop-portal-personal-tokens/
├── extension.xml                           # Extension manifest
├── module.itop-portal-personal-tokens.php  # Module definition
├── model.itop-portal-personal-tokens.php   # Business logic
├── datamodel.itop-portal-personal-tokens.xml # Data model & permissions
├── en.dict.itop-portal-personal-tokens.php # English translations
├── portal/
│   ├── views/
│   │   └── personal_tokens/
│   │       ├── list.html.twig             # Token list view
│   │       └── modals.html.twig           # Create/edit modals
│   ├── public/
│   │   ├── js/
│   │   │   └── personal_tokens.js         # JavaScript logic
│   │   └── css/
│   │       └── personal_tokens.css        # Custom styles
│   └── config/
│       └── routes.yml                      # Portal routes configuration
└── docs/
    └── README.md                           # Documentation

```

## Installation Steps

### 1. Prepare the Extension

```bash
# Navigate to your extension directory
cd /Users/lexioj/github/itop-portal-tokens-extension

# Create a clean copy for deployment
cp -r . /tmp/itop-portal-personal-tokens

# Remove development files
cd /tmp/itop-portal-personal-tokens
rm -rf .git .gitignore IMPLEMENTATION_PLAN.md REVISED_IMPLEMENTATION_PLAN.md
```

### 2. Package the Extension

```bash
# Create a ZIP file for easy deployment
cd /tmp
zip -r itop-portal-personal-tokens.zip itop-portal-personal-tokens/
```

### 3. Deploy to iTop

#### Option A: Manual Installation
```bash
# Copy to iTop extensions directory
cp -r /tmp/itop-portal-personal-tokens /path/to/itop/extensions/

# Set proper permissions
chown -R www-data:www-data /path/to/itop/extensions/itop-portal-personal-tokens
chmod -R 755 /path/to/itop/extensions/itop-portal-personal-tokens
```

#### Option B: Via iTop Toolkit
```bash
# Using iTop toolkit
php /path/to/itop/toolkit/deploy_extension.php \
  --source=/tmp/itop-portal-personal-tokens.zip \
  --deploy
```

### 4. Run iTop Setup

1. Navigate to: `https://your-itop.com/setup/`
2. Choose "Upgrade an existing iTop instance"
3. The extension will appear in the list as "Portal Personal Tokens"
4. Check the box to install it
5. Complete the setup wizard

### 5. Post-Installation Configuration

#### Configure in config-itop.php
```php
// Add to conf/production/config-itop.php
'allow_rest_services_via_tokens' => true,  // Enable REST API for personal tokens
'portal_personal_tokens' => array(
    'enabled' => true,
    'allowed_scopes' => 'REST/JSON,Export',
),
```

#### Assign Profiles to Users
```sql
-- Option 1: Grant all Portal Users token management
-- This is handled automatically by the extension

-- Option 2: Create specific users with Portal REST User profile
-- Use iTop admin interface to assign the new "Portal REST User" profile
```

## Upgrade Safety

### Why This Survives iTop Upgrades

1. **Extensions Directory**: Located in `extensions/` which is preserved during upgrades
2. **No Core Modifications**: Uses only extension points and APIs
3. **Delta Files**: Uses `_delta` directives in XML for safe merging
4. **Database Safe**: No direct database modifications

### During iTop Upgrades

1. **Before Upgrade**: 
   - Backup your extensions directory
   - Note your config settings

2. **During Upgrade**:
   - iTop upgrade process preserves extensions/
   - Extension remains inactive during upgrade

3. **After Upgrade**:
   - Run setup to re-enable extension
   - Verify configuration in config-itop.php
   - Test Portal access

## Configuration Management

### Environment-Specific Settings

Create environment-specific configuration files:

```php
// extensions/itop-portal-personal-tokens/config/production.php
return array(
    'allow_portal_tokens' => true,
    'default_token_expiry' => 90, // days
    'max_tokens_per_user' => 5,
);

// extensions/itop-portal-personal-tokens/config/development.php
return array(
    'allow_portal_tokens' => true,
    'default_token_expiry' => 7, // shorter for testing
    'max_tokens_per_user' => 10,
);
```

### Multi-Instance Deployment

For multiple iTop instances:

```bash
#!/bin/bash
# deploy.sh - Deploy to multiple instances

EXTENSION_SOURCE="/Users/lexioj/github/itop-portal-tokens-extension"
INSTANCES=(
    "/var/www/itop-prod"
    "/var/www/itop-staging"
    "/var/www/itop-dev"
)

for INSTANCE in "${INSTANCES[@]}"; do
    echo "Deploying to $INSTANCE..."
    cp -r "$EXTENSION_SOURCE" "$INSTANCE/extensions/"
    chown -R www-data:www-data "$INSTANCE/extensions/itop-portal-personal-tokens"
    echo "Deployed to $INSTANCE"
done
```

## Maintenance

### Monitoring Token Usage

```sql
-- Check active tokens per user
SELECT 
    u.login,
    COUNT(pt.id) as token_count,
    MAX(pt.last_use_date) as last_activity
FROM 
    priv_user u
    LEFT JOIN personal_token pt ON pt.user_id = u.id
WHERE 
    u.profile_list LIKE '%Portal User%'
GROUP BY 
    u.id
ORDER BY 
    token_count DESC;
```

### Cleaning Up Expired Tokens

```php
// Add to cron job or scheduled task
// extensions/itop-portal-personal-tokens/maintenance/cleanup_tokens.php

<?php
require_once('../../../approot.inc.php');
require_once(APPROOT.'/application/startup.inc.php');

// Delete expired tokens
$sOQL = "SELECT PersonalToken WHERE expiration_date < NOW()";
$oSearch = DBObjectSearch::FromOQL($sOQL);
$oSet = new DBObjectSet($oSearch);

while ($oToken = $oSet->Fetch()) {
    $oToken->DBDelete();
    echo "Deleted expired token: " . $oToken->Get('application') . "\n";
}
```

### Backup and Recovery

```bash
#!/bin/bash
# backup_extension.sh

# Backup extension files
tar -czf itop-portal-tokens-backup-$(date +%Y%m%d).tar.gz \
    /path/to/itop/extensions/itop-portal-personal-tokens \
    /path/to/itop/conf/production/config-itop.php

# Backup token data
mysql -u itop_user -p itop_db -e \
    "SELECT * FROM personal_token WHERE user_id IN 
     (SELECT id FROM priv_user WHERE profile_list LIKE '%Portal%')" \
    > portal_tokens_backup_$(date +%Y%m%d).sql
```

## Troubleshooting

### Extension Not Appearing in Portal

1. Check extension is enabled:
```php
// In config-itop.php, check installed modules
'installed_modules' => array(
    // ... other modules
    'itop-portal-personal-tokens' => '1.0.0',
),
```

2. Clear cache:
```bash
rm -rf /path/to/itop/data/cache-production/*
rm -rf /path/to/itop/data/cache/twig/*
```

3. Check permissions:
```bash
# Ensure web server can read extension files
ls -la /path/to/itop/extensions/itop-portal-personal-tokens/
```

### Tokens Not Working with REST API

1. Verify configuration:
```php
// Check in config-itop.php
'allow_rest_services_via_tokens' => true,
```

2. Test token directly:
```bash
curl -X POST https://your-itop.com/webservices/rest.php?version=1.3 \
  -d "auth_token=YOUR_TOKEN" \
  -d 'json_data={"operation":"core/check_credentials"}'
```

3. Check user profile:
```sql
-- Verify user has correct profile
SELECT login, profile_list 
FROM priv_user 
WHERE id = YOUR_USER_ID;
```

### Portal Shows Permission Denied

1. Check profile permissions in datamodel XML
2. Run setup again to apply permission changes
3. Verify PersonalToken group is assigned to Portal User profile

## Security Considerations

### Production Deployment

1. **HTTPS Only**: Ensure Portal is only accessible via HTTPS
2. **Token Expiration**: Set reasonable expiration periods
3. **Audit Logging**: Monitor token creation and usage
4. **Rate Limiting**: Implement rate limits for token operations
5. **IP Restrictions**: Consider IP whitelisting for API access

### Hardening

```php
// Add to model.itop-portal-personal-tokens.php
class TokenSecurityManager {
    const MAX_TOKENS_PER_USER = 5;
    const MIN_TOKEN_LENGTH = 32;
    const TOKEN_EXPIRY_DAYS = 90;
    
    public static function ValidateTokenCreation($iUserId) {
        // Check existing token count
        $oSearch = DBObjectSearch::FromOQL(
            "SELECT PersonalToken WHERE user_id = :user_id AND status = 'active'"
        );
        $oSet = new DBObjectSet($oSearch, array(), array('user_id' => $iUserId));
        
        if ($oSet->Count() >= self::MAX_TOKENS_PER_USER) {
            throw new Exception('Maximum token limit reached');
        }
        
        return true;
    }
}
```

## Version Management

### Updating the Extension

1. Increment version in `extension.xml` and `module.itop-portal-personal-tokens.php`
2. Document changes in CHANGELOG.md
3. Test in staging environment
4. Deploy using same process as initial installation

### Rollback Procedure

```bash
#!/bin/bash
# rollback.sh

BACKUP_DIR="/path/to/backups"
ITOP_DIR="/path/to/itop"

# Restore previous version
rm -rf $ITOP_DIR/extensions/itop-portal-personal-tokens
tar -xzf $BACKUP_DIR/itop-portal-tokens-backup-YYYYMMDD.tar.gz -C /

# Run setup to remove from active modules
# Then manually uncheck the extension in setup wizard
```

## Support and Maintenance

### Health Check Script

```php
// healthcheck.php - Run periodically to verify extension health
<?php
require_once('../approot.inc.php');

$checks = array();

// Check 1: Extension is loaded
$checks['extension_loaded'] = 
    in_array('itop-portal-personal-tokens', DBObject::GetLoadedModules());

// Check 2: Portal Users have permission
$checks['permissions_ok'] = 
    UserRights::IsActionAllowed('PersonalToken', UR_ACTION_MODIFY, null, 'Portal User');

// Check 3: REST services enabled
$checks['rest_enabled'] = 
    MetaModel::GetConfig()->Get('allow_rest_services_via_tokens') === true;

// Check 4: Token table accessible
try {
    $oSearch = DBObjectSearch::FromOQL("SELECT PersonalToken");
    $checks['table_accessible'] = true;
} catch (Exception $e) {
    $checks['table_accessible'] = false;
}

// Output results
foreach ($checks as $check => $result) {
    echo sprintf("%s: %s\n", $check, $result ? 'PASS' : 'FAIL');
}
```

## Conclusion

This extension is designed to be:
- **Upgrade-safe**: Survives iTop upgrades intact
- **Maintainable**: Clear structure and documentation
- **Secure**: Leverages existing iTop security
- **Simple**: Minimal code, maximum compatibility

By following this deployment guide, you ensure that Portal Users can manage their personal tokens reliably across iTop upgrades.