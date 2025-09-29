# Development Status - iTop Portal Personal Tokens Extension

**Last updated**: 2025-09-29  
**Status**: Portal menu works, CRUD interface not yet functional

## Current State

### ✅ Working
- Portal navigation shows "Personal Tokens" menu item
- Page loads and displays correct token count in title (e.g., "Personal Tokens (1)")
- Extension deploys correctly to OrbStack itop-dev container
- Backend "My Account" → "Personal Tokens" interface fully functional (use as reference)
- OQL filtering works: `u.contactid = :current_contact_id`
- Permissions properly extended for Portal User profile

### ❌ Not Working
- Portal table shows "No item." despite count showing (1) 
- CRUD actions (create/edit/delete) not functional in portal interface
- ManageBrick data display issue

### 🔧 Technical Details
- **ManageBrick config**: Uses `ui_version: v3`, proper actions defined
- **OQL**: `SELECT PersonalToken AS pt JOIN User AS u ON pt.user_id = u.id WHERE u.contactid = :current_contact_id`
- **Test data**: User "boris" (contactid=16) has 1 PersonalToken (id=3, application="Test Application")
- **Deployment**: Direct copy to `/var/www/itop/web/extensions/itop-portal-personal-tokens/`

## Next Steps to Resume Development

1. **Diagnose ManageBrick display issue**
   - Try switching from OQL JOIN to `<class>PersonalToken</class>` in brick
   - Compare field definitions with working itop-tickets ManageBrick
   - Check data_loading, grouping, and template configurations

2. **Enable debug logging**
   - Check ScopeValidatorHelper decisions
   - Verify BrickCollection loading and permissions
   - Look for portal error logs during page render

3. **CRUD Implementation**
   - Once data displays, verify create/edit actions work
   - Test form submission and navigation rules
   - Confirm token creation sets correct user_id

4. **Integration Testing**
   - Verify generated tokens work with REST API
   - Test with Nextcloud integration_itop app
   - Document end-to-end workflow

## Quick Resume Commands

```bash
# Deploy to OrbStack
cd /Users/lexioj/github/itop-portal-tokens-extension
orb -m itop-dev sudo cp -r . /var/www/itop/web/extensions/itop-portal-personal-tokens/
orb -m itop-dev sudo find /var/www/itop/web/data/cache-production -type f -delete

# Fix setup permissions
orb -m itop-dev sudo chmod 664 /var/www/itop/web/conf/production/config-itop.php
orb -m itop-dev sudo chown www-data:www-data /var/www/itop/web/conf/production/config-itop.php
orb -m itop-dev sudo chmod 775 /var/www/itop/web/conf/production/

# Run setup wizard
open http://itop-dev.orb.local/setup/

# Test portal
open http://itop-dev.orb.local/portal/
# Login as boris:admin, check Personal Tokens menu
```

## Files Changed Since Last Commit

- `datamodel.itop-portal-personal-tokens.xml` - Updated OQL, added CRUD actions
- `README.md` - Added current status section  
- `CHANGELOG.md` - Added progress notes and known issues
- `docs/FILE_STATUS.md` - Added resume instructions
- `docs/DEV_STATUS.md` - This file (development checkpoint)

## Repository State

Ready to commit current progress and resume development later.