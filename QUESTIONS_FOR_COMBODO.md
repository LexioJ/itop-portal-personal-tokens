# Questions for Combodo Team - Portal Tokens Integration

**Context:** Integration of the standalone extension `itop-portal-personal-tokens` into `Combodo/authent-token` (see [Issue #3](https://github.com/Combodo/combodo-my-account/issues/3))

**Date:** November 9, 2025

---

Hi Olivier and the iTop team,

I've created a detailed integration plan (see `INTEGRATION_PLAN.md`) after analyzing the authent-token repository structure. Before proceeding with the actual code migration, I'd like to clarify several questions to ensure the integration aligns with your preferences and standards.

## 🏗️ 1. Architecture & Code Organization

### Q1: Directory Structure Preference

Based on my analysis, authent-token currently uses a **flat structure** (src/Hook/ contains all hooks regardless of Console/Portal). I've identified two integration approaches:

**Option A: Flat Structure** (matches current style)
```
src/Hook/
├── MyAccountTabExtension.php               # Existing Console
├── MyAccountSectionTabContentExtension.php # Existing Console
├── PortalPersonalTokensTabExtension.php    # NEW Portal
└── PortalPersonalTokensContentExtension.php # NEW Portal
```

**Option B: Organized Structure** (introduces Console/Portal separation)
```
src/Hook/
├── Console/
│   ├── MyAccountTabExtension.php
│   └── MyAccountSectionTabContentExtension.php
└── Portal/
    ├── PersonalTokensTabExtension.php
    └── PersonalTokensContentExtension.php
```

**Which approach do you prefer?**
- Option A keeps minimal changes to existing code
- Option B provides clearer separation but requires refactoring existing hooks

### Q2: Template Organization

Similarly for templates, should they be:
- **Flat:** `templates/portal_tokens_tab.html.twig`
- **Organized:** `templates/portal/personal_tokens_tab.html.twig`

### Q3: Coding Standards

Are there specific coding standards, style guides, or conventions for authent-token that I should follow?
- PSR-12?
- Custom Combodo standards?
- PHPDoc requirements?
- Naming conventions?

---

## 🔧 2. Service Layer & Architecture Patterns

### Q4: Service Layer Implementation

I noticed the Console implementation delegates logic to `PersonalTokenService`:
- `MyAccountSectionTabContentExtension` → calls `PersonalTokenService::ProvideHtmlTokenInfo()`

The current standalone Portal implementation has all logic directly in the hook class (369 lines):
- Form handling, token CRUD, rendering - all in `PersonalTokensUserProfileExtension`

**Should the Portal implementation:**
- **Option A:** Keep current standalone approach (faster integration, no service layer changes)
- **Option B:** Extract logic into service layer to match Console pattern
  - If yes: Extend existing `PersonalTokenService` or create separate `PortalPersonalTokenService`?

### Q5: Code Sharing Between Console and Portal

How much code sharing is desired?
- Shared services for common operations?
- Separate implementations for Console vs Portal?
- Your preference/recommendation?

---

## ⚙️ 3. Configuration & Settings

### Q6: Configuration Parameters Scope

The standalone extension uses these parameters:
```php
'portal_personal_tokens' => [
    'max_tokens_per_user' => 5,      // Range: 1-20
    'default_expiry_days' => 90,     // Range: 30-365
]
```

Should these be:
- **Shared:** Same limits for both Console and Portal users
- **Separate:** Different configuration for Portal (`portal_personal_tokens`) vs Console
- **Unified:** Single configuration with role-based differentiation

### Q7: Default Configuration Values

What should the default values be for Portal token management?
- Maximum tokens per user?
- Default expiration period?
- Should they match Console defaults?

---

## 🔐 4. Permissions & Security

### Q8: Portal User Permissions

The PersonalToken class exists in authent-token's datamodel, but **no portal-specific permissions are defined**.

The standalone extension currently uses:
```xml
<permission id="portal-power-user">
    <actions>
        <action permission="read">PersonalToken</action>
        <action permission="write">PersonalToken</action>
    </actions>
</permission>
```

**How should this be handled?**
- Add portal permissions to `datamodel.authent-token.xml`?
- Keep separate datamodel file for portal extensions?
- Different approach?

### Q9: Default Portal Profiles Access

Which portal profiles should have token management access by default?
- Current standalone: Portal Power User
- Should we align with Console's `personal_tokens_allowed_profiles` parameter?
- Or maintain separate portal profile configuration?

---

## 🌐 5. Internationalization (Dictionaries)

### Q10: Dictionary Key Naming Convention

Both repositories have the **same 17 language files** (great!).

Current patterns:
- Console (authent-token): `MyAccount:SubTitle:PersonalTokens`, `PersonalToken:application`
- Standalone Portal: `Portal:PersonalTokens:TabTitle`, `Portal:PersonalTokens:CreateToken`

**Preferred pattern for Portal keys?**
- `Portal:PersonalTokens:*` (current standalone)
- `PersonalToken:Portal:*` (group by class)
- Other preference?

### Q11: Shared Dictionary Keys

Should Console and Portal share dictionary keys where functionality overlaps?
- Example: "Application", "Scope", "Expiration date", "Token", "Regenerate", "Delete"
- Or keep them separate for flexibility (e.g., different wording for Console vs Portal users)?

---

## 🧪 6. Testing & Quality Assurance

### Q12: Test Framework

I see a `tests/` directory in authent-token. What testing framework is used?
- PHPUnit?
- Custom framework?
- Integration with iTop's test suite?

### Q13: Test Coverage Requirements

What are the expectations for test coverage with this PR?
- Unit tests required?
- Integration tests required?
- Minimum coverage percentage?

### Q14: CI/CD Pipeline

I noticed a `Jenkinsfile` in the repository. Are there specific CI/CD requirements I should be aware of?
- Automated tests that must pass?
- Code quality checks?
- Other gates?

---

## 📦 7. Versioning & Release

### Q15: Target Version

In which version of authent-token should this integration be released?
- **2.3.0** (minor version bump - new feature)?
- **3.0.0** (major version - significant addition)?
- Other version?

### Q16: Release Schedule

Is there a release schedule or cycle I should align with?

### Q17: Dependency Versions

What is the minimum required version of `itop-portal-base` for authent-token?
- Standalone currently requires: `itop-portal-base/3.0.0`
- Should this be maintained or adjusted?

---

## 🔄 8. Migration & Backwards Compatibility

### Q18: Migration Strategy for Existing Users

How should users currently using the standalone `itop-portal-personal-tokens` extension be supported?
- **Migration guide** documenting the transition process
- **Automated migration script** (though likely unnecessary as both use same PersonalToken class)
- **Compatibility layer** for gradual migration

### Q19: Standalone Extension Deprecation

Should the standalone extension be deprecated after integration?
- If yes: Timeline for deprecation announcement?
- Should standalone version display warning message after authent-token integration is released?
- Or keep both available?

### Q20: Token Data Compatibility

Since both the standalone extension and authent-token use the same `PersonalToken` class (the standalone depends on `authent-token/2.0.0`), existing tokens should work seamlessly after migration - correct?
- Any data migration needed?
- Any database schema changes?

---

## 🛠️ 9. Maintenance & Utilities

### Q21: Cleanup Script for Expired Tokens

The standalone extension includes `maintenance/cleanup_expired_tokens.php` which deletes expired PersonalToken objects.

**Does authent-token already have a similar cleanup script?**
- If yes: No action needed
- If no: Should this script be added to authent-token?

---

## 📋 10. Process & Workflow

### Q22: PR Submission Preference

As mentioned in Issue #3, you suggested either:
1. We submit a PR to `Combodo/authent-token`
2. You handle the integration internally

**What is your preference?**
- If PR: We're happy to submit once these questions are clarified
- If internal: We can provide a clean branch with all changes

### Q23: Development Branch

What is the preferred branch for feature development?
- `develop`
- `main`
- Feature-specific branch?

### Q24: PR Requirements

Are there specific requirements for:
- **PR description format/template**?
- **Commit message format** (Conventional Commits, other)?
- **Documentation** that must be included?
- **Changelog entries**?

---

## 📊 Summary of My Analysis

For context, here's what I've found in my analysis of authent-token:

### ✅ What Already Exists:
- Console-side token management (My Account)
- `PersonalToken` class with all necessary attributes
- `PersonalTokenService` and `AuthentTokenService`
- 17 dictionary files (exact same language set as standalone!)
- Templates for Console UI
- Hook architecture (`MyAccountTabExtension`, `MyAccountSectionTabContentExtension`)

### ➕ What Needs to be Added:
- Portal-side hooks (2 PHP classes, ~430 lines total)
- Portal templates (3 Twig files: HTML, JS, CSS)
- Portal dictionary entries (merge into existing 17 files)
- Portal permissions in datamodel (if using integrated approach)
- Configuration parameters for Portal

### ⚙️ What Needs to be Changed:
- Namespaces: `Combodo\iTop\Portal\PersonalTokens\Hook` → `Combodo\iTop\AuthentToken\Hook[|Portal]`
- Template paths in PHP code
- Module autoloader entries

**Estimated net development time:** ~1-2 weeks (after clarifications)

---

## 🎯 Next Steps

Once these questions are clarified, I'm ready to:

1. Fork `Combodo/authent-token` (or use existing fork)
2. Create feature branch
3. Implement the integration following your preferences
4. Write tests (as per your requirements)
5. Update documentation
6. Submit PR (if that's the preferred approach)

---

Thank you for your guidance! I'm looking forward to contributing this functionality to the official authent-token module.

Best regards,
Julian

---

**Related Documents:**
- Detailed Integration Plan: `INTEGRATION_PLAN.md`
- Standalone Extension: https://github.com/LexioJ/itop-portal-personal-tokens
- Target Repository: https://github.com/Combodo/authent-token
- Original Issue: https://github.com/Combodo/combodo-my-account/issues/3
