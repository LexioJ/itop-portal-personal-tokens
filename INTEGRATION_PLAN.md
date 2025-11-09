# Integration Plan: Portal Personal Tokens → authent-token

**Date:** November 9, 2025
**Version:** 2.0
**Status:** Planning (Updated after authent-token analysis)

## 1. Executive Summary

The standalone extension `itop-portal-personal-tokens` should be integrated into Combodo's official `authent-token` module. This was proposed by the iTop team (see [Issue #3](https://github.com/Combodo/combodo-my-account/issues/3)), as they have already implemented similar functionality for the Console side of iTop in this module.

**Goal:** Enable portal users to manage Personal API Tokens directly in the authent-token module, analogous to the existing Console functionality.

## 2. Current State Analysis

### 2.1 Standalone Extension (itop-portal-personal-tokens)

**Repository:** `LexioJ/itop-portal-personal-tokens`

**Architecture:**
- **2 Hook Classes:**
  - `PersonalTokensTabExtension` (implements `iUserProfileTabExtension`)
  - `PersonalTokensUserProfileExtension` (implements `iUserProfileTabContentExtension`)
- **3 Twig Templates:** UI, JavaScript, CSS
- **17 Language Files** (fully translated: DE/EN, fallback text for 15 others)
- **1 Maintenance Script:** `cleanup_expired_tokens.php`

**Functionality:**
- Adds "Personal API Tokens" tab to portal user profile
- Token creation with configurable scopes (REST/JSON, Export)
- Token management (view, regenerate, delete)
- Security features: CSRF protection, XSS prevention, user isolation

**Dependencies:**
```php
'authent-token/2.0.0',
'itop-portal-base/3.0.0'
```

**Version:** 1.1.0 (production-ready)

**Namespace:**
```php
namespace Combodo\iTop\Portal\PersonalTokens\Hook;
```

### 2.2 Target Repository (authent-token)

**Repository:** `Combodo/authent-token` (Version 2.2.1)

**Description:**
- iTop module for token-based authentication
- Provides **Console-side** token management ("My Account")
- Provides `PersonalToken` class and authentication services
- PHP 96.5%, Twig 3.5%

**Current Structure:**
```
authent-token/
├── src/
│   ├── Controller/
│   ├── Exception/
│   ├── Helper/
│   ├── Hook/                    # Contains Console hooks
│   │   ├── MyAccountTabExtension.php
│   │   ├── MyAccountSectionTabContentExtension.php
│   │   ├── TokenLoginExtension.php
│   │   └── LegacyTokenLoginExtension.php
│   ├── Model/
│   │   ├── AbstractPersonalToken.php
│   │   ├── AbstractApplicationToken.php
│   │   ├── PersonalTokenMenu.php
│   │   └── iToken.php
│   └── Service/
│       ├── AuthentTokenService.php
│       ├── PersonalTokenService.php
│       └── MetaModelService.php
├── templates/
│   ├── personaltokens.html.twig      # Console template
│   └── personaltokens.ready.js.twig  # Console JavaScript
├── dictionaries/                      # 17 language files (same as standalone)
│   ├── en.dict.authent-token.php
│   ├── de.dict.authent-token.php
│   └── ... (15 more)
├── tests/
├── module.authent-token.php
├── model.authent-token.php
├── datamodel.authent-token.xml
└── ajax.php
```

**Key Findings from Analysis:**

1. **Console Implementation Exists:**
   - `MyAccountTabExtension` (implements `iMyAccountTabExtension`)
     - Namespace: `Combodo\iTop\AuthentToken\Hook`
     - Methods: `IsTabPresent()`, `GetTabCode()`, `GetTabLabel()`, `GetTabRank()`
     - Uses `PersonalTokenService` for permission checks

   - `MyAccountSectionTabContentExtension` (implements `iMyAccountTabContentExtension`)
     - Namespace: `Combodo\iTop\AuthentToken\Hook`
     - Delegates logic to `PersonalTokenService::ProvideHtmlTokenInfo()`
     - Template: `personaltokens.html.twig`

2. **No Console/Portal Directory Separation:**
   - The src/ directory is organized by **function** (Controller, Hook, Service, Model), **not** by target (Console vs Portal)
   - Templates are stored **flat** in templates/ directory, no subdirectories

3. **PersonalToken Class:**
   - Defined in `datamodel.authent-token.xml`
   - Attributes: `user_id`, `org_id`, `auth_token`, `application`, `scope`, `expiration_date`, `use_count`, `last_use_date`
   - **No portal-specific permissions defined** in datamodel

4. **Services Architecture:**
   - `PersonalTokenService` handles business logic for Console side
   - `AuthentTokenService` handles token generation/validation
   - Standalone extension uses `AuthentTokenService` but implements own form handling

5. **Dictionary Files:**
   - Exactly **17 language files**, same set as standalone extension
   - Current keys use pattern: `MyAccount:SubTitle:PersonalTokens`, `PersonalToken:*`

## 3. Integration Architecture Options

Based on the analysis, there are **two main approaches**:

### Option A: Flat Structure (Match Current authent-token Style)

**Pros:**
- Consistent with current authent-token organization
- Simpler directory structure
- Easier to share code between Console and Portal

**Cons:**
- Mixing Console and Portal hooks in same Hook/ directory
- May require careful naming to distinguish

```
authent-token/
├── src/
│   └── Hook/
│       ├── MyAccountTabExtension.php               # Console
│       ├── MyAccountSectionTabContentExtension.php # Console
│       ├── PortalPersonalTokensTabExtension.php    # NEW: Portal
│       └── PortalPersonalTokensContentExtension.php # NEW: Portal
├── templates/
│   ├── personaltokens.html.twig           # Console
│   ├── personaltokens.ready.js.twig       # Console
│   ├── portal_tokens_tab.html.twig        # NEW: Portal
│   ├── portal_tokens_tab.ready.js.twig    # NEW: Portal
│   └── portal_tokens_tab.css.twig         # NEW: Portal
```

### Option B: Organized by Target (Proposed New Structure)

**Pros:**
- Clear separation between Console and Portal code
- Easier to maintain and understand
- Scalable for future Portal extensions

**Cons:**
- Requires restructuring existing code
- Breaking change for current structure

```
authent-token/
├── src/
│   └── Hook/
│       ├── Console/
│       │   ├── MyAccountTabExtension.php
│       │   └── MyAccountSectionTabContentExtension.php
│       └── Portal/
│           ├── PersonalTokensTabExtension.php      # NEW
│           └── PersonalTokensContentExtension.php   # NEW
├── templates/
│   ├── console/
│   │   ├── personaltokens.html.twig
│   │   └── personaltokens.ready.js.twig
│   └── portal/
│       ├── personal_tokens_tab.html.twig           # NEW
│       ├── personal_tokens_tab.ready.js.twig       # NEW
│       └── personal_tokens_tab.css.twig            # NEW
```

**Recommendation:** Start with **Option A** (flat structure) to minimize changes to existing code. Can be refactored to Option B later if Combodo prefers.

## 4. Technical Implementation Details

### 4.1 Code Migration

#### 4.1.1 PHP Hook Classes

| File | Lines | Changes Required |
|------|-------|------------------|
| PersonalTokensTabExtension.php | 67 | Namespace change |
| PersonalTokensUserProfileExtension.php | 369 | Namespace + template paths + potential service layer extraction |

**Namespace Change:**
```php
// Current (Standalone)
namespace Combodo\iTop\Portal\PersonalTokens\Hook;

// After Integration (Option A)
namespace Combodo\iTop\AuthentToken\Hook;

// OR (Option B)
namespace Combodo\iTop\AuthentToken\Hook\Portal;
```

**Class Renaming (to avoid confusion):**
```php
// Consider renaming for clarity
PersonalTokensTabExtension → PortalPersonalTokensTabExtension
PersonalTokensUserProfileExtension → PortalPersonalTokensContentExtension
```

#### 4.1.2 Service Layer Consideration

**Current Standalone Approach:**
- All logic in `PersonalTokensUserProfileExtension` (369 lines)
- Direct form handling, token CRUD, rendering

**Console Approach:**
- Delegates to `PersonalTokenService::ProvideHtmlTokenInfo()`
- Cleaner separation of concerns

**Decision Point:**
- **Option 1:** Keep standalone logic as-is (faster integration)
- **Option 2:** Extract to service layer to match Console pattern (cleaner architecture)

**Recommendation:** Start with Option 1, refactor to Option 2 if Combodo requests it.

#### 4.1.3 Templates

Migration strategy:

```php
// Current template paths in PersonalTokensUserProfileExtension
'portal-personal-tokens/personal_tokens_tab.html.twig'
'portal-personal-tokens/personal_tokens_tab.ready.js.twig'
'portal-personal-tokens/personal_tokens_tab.css.twig'

// After integration (Option A - Flat)
'authent-token/portal_tokens_tab.html.twig'
'authent-token/portal_tokens_tab.ready.js.twig'
'authent-token/portal_tokens_tab.css.twig'

// OR (Option B - Organized)
'authent-token/portal/personal_tokens_tab.html.twig'
'authent-token/portal/personal_tokens_tab.ready.js.twig'
'authent-token/portal/personal_tokens_tab.css.twig'
```

**Template Changes:**
- Copy 3 template files to authent-token/templates/
- Update path references in PHP code
- No content changes needed

#### 4.1.4 Dictionary Merge

**Current State:**
- Standalone extension: 17 language files with pattern `Portal:PersonalTokens:*`
- authent-token: 17 language files (exact same set!) with pattern `MyAccount:*`, `PersonalToken:*`

**Merge Strategy:**

```php
// Example: en.dict.authent-token.php

// Existing Console entries
Dict::Add('EN US', 'English', 'English', array(
    'MyAccount:SubTitle:PersonalTokens' => 'Personal Tokens',
    'PersonalToken:application' => 'Application',
    // ... existing entries
));

// ADD: Portal entries
Dict::Add('EN US', 'English', 'English', array(
    'Portal:PersonalTokens:TabTitle' => 'Personal API Tokens',
    'Portal:PersonalTokens:CreateToken' => 'Create new token',
    // ... all portal entries from standalone
));
```

**Task:** Merge all 17 dictionary files, preserving both Console and Portal keys.

### 4.2 Module Configuration

**File:** `module.authent-token.php`

**Add Autoloader Entries:**
```php
'src/Hook/PortalPersonalTokensTabExtension.php',        // Option A
'src/Hook/PortalPersonalTokensContentExtension.php',    // Option A

// OR (Option B)
'src/Hook/Portal/PersonalTokensTabExtension.php',
'src/Hook/Portal/PersonalTokensContentExtension.php',
```

**Add Configuration Parameters:**
```php
'settings' => array(
    'portal_personal_tokens' => array(
        'max_tokens_per_user' => 5,      // Range: 1-20
        'default_expiry_days' => 90,     // Range: 30-365
    ),
),
```

**Question:** Should these be portal-specific or shared with Console settings? Combodo's input needed.

### 4.3 Datamodel Extensions

**Current standalone datamodel** (`datamodel.itop-portal-personal-tokens.xml`):

```xml
<permission id="portal-power-user">
    <actions>
        <action permission="read">PersonalToken</action>
        <action permission="write">PersonalToken</action>
    </actions>
</permission>
```

**authent-token datamodel status:**
- PersonalToken class defined ✓
- No portal-specific permissions ✗

**Decision Points:**
1. Should portal permissions be added to `datamodel.authent-token.xml`?
2. Or keep separate datamodel file for portal extensions?
3. How to handle default portal user access?

**Question for Combodo:** What is the preferred approach for portal permissions?

### 4.4 Maintenance Script

**Standalone extension includes:** `maintenance/cleanup_expired_tokens.php`

**Check:**
- Does authent-token already have a cleanup script?
- If yes: No action needed
- If no: Add to authent-token/maintenance/ directory

**Script Purpose:**
```php
// Deletes PersonalToken objects where expiration_date < now()
// Can be run via cron job
```

## 5. Migration Strategy (5 Phases)

### Phase 1: Preparation ✓ (In Progress)
- [x] Analyze authent-token repository structure
- [x] Identify Console implementation patterns
- [x] Document integration options
- [ ] Create fork of `Combodo/authent-token`
- [ ] Create feature branch: `feature/portal-token-management`

### Phase 2: Code Integration
1. **Directory Setup:**
   - Create necessary directories (based on Option A or B decision)
   - Copy template files

2. **PHP Classes:**
   - Copy hook classes to src/Hook/
   - Update namespaces
   - Update template paths
   - (Optional) Extract service layer

3. **Dictionaries:**
   - Merge all 17 language files
   - Verify no key conflicts
   - Test translations

4. **Configuration:**
   - Update module.authent-token.php (autoloader + settings)
   - Add maintenance script (if needed)
   - Update datamodel (if portal permissions needed)

5. **Verification:**
   - Check all file references
   - Verify namespace consistency
   - Code review against Combodo standards

### Phase 3: Testing
1. **Unit Tests:**
   - Investigate authent-token test framework (if exists)
   - Write tests for Portal hooks (if required)

2. **Manual Tests:**
   - Token creation in Portal
   - Token regeneration
   - Token deletion
   - User isolation (different portal users)
   - Scope selection (REST/JSON vs Export)
   - Expiration date configuration
   - Copy-to-clipboard functionality
   - REST API access with generated tokens

3. **Compatibility Tests:**
   - iTop 3.1.0+
   - PHP 7.4, 8.0, 8.1, 8.2
   - Different portal configurations

4. **Integration Tests:**
   - Console and Portal tokens coexisting
   - Shared PersonalToken database table
   - No conflicts between implementations

### Phase 4: Documentation
1. **README.md Update:**
   - Add Portal functionality section
   - Update configuration examples
   - Add Portal screenshots (if applicable)

2. **CHANGELOG.md:**
   - Document new Portal features
   - Note version number

3. **Migration Guide:**
   - Instructions for users of standalone extension
   - How to uninstall standalone and use integrated version
   - Data migration (if needed - likely seamless as both use same PersonalToken class)

4. **Code Comments:**
   - Ensure all classes are well-documented
   - Add PHPDoc blocks where needed

### Phase 5: Pull Request
1. **Prepare PR:**
   - Clean commit history
   - Squash/rebase if needed
   - Write comprehensive PR description

2. **PR Description Should Include:**
   - Summary of changes
   - Reference to Issue #3
   - Testing performed
   - Screenshots (Portal UI)
   - Migration notes
   - Configuration examples

3. **Submit PR:**
   - Target branch: TBD (likely `develop` or `main`)
   - Request review from Combodo team
   - Address feedback iteratively

## 6. Open Questions for Combodo

### 6.1 Architecture & Structure

**Q1:** Which integration approach do you prefer?
- **Option A:** Flat structure (`src/Hook/PortalPersonalTokensTabExtension.php`)
- **Option B:** Organized structure (`src/Hook/Portal/PersonalTokensTabExtension.php`)

**Q2:** Should existing Console hooks be moved to `src/Hook/Console/` for consistency (Option B)?

**Q3:** Are there coding standards, style guides, or conventions for authent-token that we should follow?

### 6.2 Service Layer

**Q4:** Should Portal implementation delegate to a service layer (like Console does with `PersonalTokenService`)?
- If yes: Extend existing `PersonalTokenService` or create `PortalPersonalTokenService`?

**Q5:** How much code sharing is desired between Console and Portal implementations?

### 6.3 Configuration

**Q6:** Should configuration parameters be:
- **Shared:** Same `max_tokens_per_user` for both Console and Portal
- **Separate:** Different limits for Console vs Portal users
- **Unified:** Single configuration with role-based differentiation

**Q7:** What should be the default values for Portal configuration?

### 6.4 Permissions & Security

**Q8:** How should Portal user permissions be handled?
- Add to `datamodel.authent-token.xml`?
- Keep separate datamodel extension?
- Different approach?

**Q9:** Which portal profiles should have access by default?
- Current standalone uses: Portal Power User
- Should we align with Console's `personal_tokens_allowed_profiles`?

### 6.5 Templates & UI

**Q10:** Preferred template naming convention?
- `portal_tokens_tab.html.twig` (prefix style)
- `personaltokens.portal.html.twig` (suffix style)
- `portal/personal_tokens_tab.html.twig` (directory style)

**Q11:** Should Portal UI match Console UI styling, or maintain distinct design?

### 6.6 Dictionaries

**Q12:** Preferred dictionary key pattern?
- `Portal:PersonalTokens:*` (current standalone)
- `PersonalToken:Portal:*` (group by class)
- Other preference?

**Q13:** Should Console and Portal share dictionary keys where functionality overlaps (e.g., "Application", "Scope")?

### 6.7 Testing

**Q14:** What testing framework is used in authent-token (if any)?

**Q15:** What are the test coverage expectations for PRs?

**Q16:** Is there a CI/CD pipeline we should be aware of?

### 6.8 Versioning & Release

**Q17:** What version of authent-token should this be released in?
- 2.3.0 (minor version bump)?
- 3.0.0 (major version)?

**Q18:** Is there a release schedule or cycle to align with?

**Q19:** What is the minimum required version of `itop-portal-base`?

### 6.9 Migration & Backwards Compatibility

**Q20:** How should users migrating from standalone extension be supported?
- Migration guide only?
- Automated migration script?
- Compatibility layer?

**Q21:** Should the standalone extension be deprecated?
- Timeline for deprecation?
- Warning messages in standalone version?

**Q22:** Since both use the same `PersonalToken` class, existing tokens should work seamlessly - correct?

### 6.10 Maintenance

**Q23:** Does authent-token already have a cleanup script for expired tokens?

**Q24:** If not, should the maintenance script from standalone be added?

### 6.11 Process

**Q25:** Should we submit a PR directly to `Combodo/authent-token`, or would you prefer to handle integration internally?

**Q26:** What is the preferred branch for feature development?
- `develop`
- `main`
- Feature-specific branch?

**Q27:** Are there specific requirements for:
- PR descriptions?
- Commit message format?
- Documentation?

## 7. Risk Assessment

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| Namespace conflicts | Low | High | Careful analysis + testing |
| Template path issues | Low | Medium | Verify all references + test |
| Dictionary key collisions | Low | Medium | Systematic merge + review |
| Permission conflicts | Medium | High | Test with multiple user profiles |
| Breaking changes for standalone users | High | Medium | Provide migration guide |
| Code review delays | Medium | Low | Submit early, iterate on feedback |
| Style/convention mismatches | Medium | Low | Review Combodo standards first |
| Service layer refactoring needed | Medium | Medium | Plan for iteration if requested |

## 8. Dependencies & Prerequisites

- [x] Access to Combodo/authent-token repository
- [x] Understanding of authent-token architecture
- [ ] Combodo feedback on open questions
- [ ] Fork of Combodo/authent-token
- [ ] iTop 3.1+ test environment
- [ ] PHP 7.4+ test environments

## 9. Timeline Estimate

| Phase | Duration (Estimated) | Dependencies |
|-------|---------------------|--------------|
| **1. Preparation** | 1 day | Combodo answers (Q1-Q3, Q25-Q27) |
| **2. Code Integration** | 3-5 days | Architecture decisions (Q1-Q5) |
| **3. Testing** | 2-3 days | Test environment, Q14-Q16 |
| **4. Documentation** | 1-2 days | - |
| **5. Pull Request** | Variable | Combodo code review |
| **TOTAL (Net)** | **~1-2 weeks** | + Review time |

## 10. Next Steps

### Immediate Actions:
1. ✅ Create detailed integration plan (this document)
2. ✅ Analyze authent-token repository structure
3. ⬜ Submit questions to Combodo team (GitHub Issue #3)
4. ⬜ Await feedback on architecture preferences

### After Clarification:
5. ⬜ Fork Combodo/authent-token repository
6. ⬜ Create feature branch
7. ⬜ Execute Phase 2: Code Integration
8. ⬜ Execute Phase 3: Testing
9. ⬜ Execute Phase 4: Documentation
10. ⬜ Submit Pull Request (Phase 5)

---

## Appendix A: Key Differences Console vs Portal

| Aspect | Console (My Account) | Portal (User Profile) | Shared |
|--------|---------------------|----------------------|--------|
| **Interface** | `iMyAccountTabExtension` | `iUserProfileTabExtension` | - |
| **Content Interface** | `iMyAccountSectionTabContentExtension` | `iUserProfileTabContentExtension` | - |
| **Namespace** | `Combodo\iTop\AuthentToken\Hook` | `Combodo\iTop\AuthentToken\Hook` (proposed) | - |
| **Service** | `PersonalTokenService` | Direct implementation (currently) | - |
| **Templates** | `personaltokens.html.twig` | `portal_tokens_tab.*.twig` (3 files) | - |
| **Data Model** | PersonalToken class | PersonalToken class | ✓ Same |
| **Token Service** | `AuthentTokenService` | `AuthentTokenService` | ✓ Same |
| **Dictionary Keys** | `MyAccount:*` | `Portal:PersonalTokens:*` | Some overlap |

## Appendix B: File Mapping

| Standalone Extension | authent-token (Proposed) | Type |
|---------------------|--------------------------|------|
| `src/Hook/PersonalTokensTabExtension.php` | `src/Hook/PortalPersonalTokensTabExtension.php` | PHP Hook |
| `src/Hook/PersonalTokensUserProfileExtension.php` | `src/Hook/PortalPersonalTokensContentExtension.php` | PHP Hook |
| `templates/personal_tokens_tab.html.twig` | `templates/portal_tokens_tab.html.twig` | Twig |
| `templates/personal_tokens_tab.ready.js.twig` | `templates/portal_tokens_tab.ready.js.twig` | Twig |
| `templates/personal_tokens_tab.css.twig` | `templates/portal_tokens_tab.css.twig` | Twig |
| `en.dict.itop-portal-personal-tokens.php` | Merge into `dictionaries/en.dict.authent-token.php` | Dictionary |
| `de.dict.itop-portal-personal-tokens.php` | Merge into `dictionaries/de.dict.authent-token.php` | Dictionary |
| ... (15 more language files) | ... (15 more merges) | Dictionary |
| `maintenance/cleanup_expired_tokens.php` | `maintenance/cleanup_expired_tokens.php` (if not exists) | Maintenance |
| `datamodel.itop-portal-personal-tokens.xml` | Merge/integrate into `datamodel.authent-token.xml` | Datamodel |

## Appendix C: Relevant Links

- **GitHub Issue:** https://github.com/Combodo/combodo-my-account/issues/3
- **Standalone Extension:** https://github.com/LexioJ/itop-portal-personal-tokens
- **Target Repository:** https://github.com/Combodo/authent-token
- **iTop Documentation:** https://www.itophub.io/wiki/

---

**Document History:**
- v1.0 (2025-11-09): Initial planning based on Issue #3 and codebase review
- v2.0 (2025-11-09): Updated with detailed authent-token analysis, architectural options, and comprehensive questions
