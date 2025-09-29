# Development Status - iTop Portal Personal Tokens Extension

**Last updated**: 2025-09-29 04:17 UTC  
**Status**: Switched to class-based ManageBrick - testing needed

## Current State

### ✅ Working
- Portal navigation shows "Personal Tokens" menu item
- Page loads and displays correct token count in title (e.g., "Personal Tokens (1)")
- Extension deploys correctly to OrbStack itop-dev container
- Backend "My Account" → "Personal Tokens" interface fully functional (use as reference)
- OQL filtering works: `u.contactid = :current_contact_id`
- Permissions properly extended for Portal User profile

### ❌ Not Working (Need Testing)
- Portal table may still show "No item." - needs testing after setup wizard
- CRUD actions (create/edit/delete) not yet tested in portal interface
- ManageBrick data display - switched to class-based approach

### 🔧 Technical Details
- **ManageBrick config**: Uses `ui_version: v3`, proper actions defined
- **NEW**: Switched from complex OQL JOIN to `<class>PersonalToken</class>` approach
- **Scope filtering**: PersonalToken class scope handles `u.contactid = :current_contact_id`
- **Test data**: User "boris" (contactid=16) has 1 PersonalToken (id=3, application="Test Application")
- **Deployment**: Direct copy to `/var/www/itop/web/extensions/itop-portal-personal-tokens/`

## Next Steps to Resume Development

1. **Test class-based ManageBrick approach**
   - Complete setup wizard if still running: http://itop-dev.orb.local/setup/
   - Test portal: http://itop-dev.orb.local/portal/ (login: boris:admin)
   - Check if Personal Tokens now shows actual items instead of "No item."

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

## Recent Changes (Last 2 Commits)

- **Latest**: `datamodel.itop-portal-personal-tokens.xml` - Switched to class-based ManageBrick
- `README.md` - Added current status section  
- `CHANGELOG.md` - Added progress notes and known issues
- `docs/FILE_STATUS.md` - Added resume instructions
- `docs/DEV_STATUS.md` - This file (development checkpoint)

## Repository State

Ready to commit current progress and resume development later.