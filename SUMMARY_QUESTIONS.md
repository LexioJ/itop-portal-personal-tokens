# Summary Questions for Combodo Team

**Context:** I've analyzed the authent-token repository and created a detailed integration plan. Here are the key questions that need clarification before proceeding with the integration of the standalone `itop-portal-personal-tokens` extension.

---

## 🏗️ Architecture & Structure

**Q1: Directory Structure Preference**

authent-token currently uses a flat structure (`src/Hook/` contains all hooks). Should I:
- **Option A:** Keep flat structure → Add `PortalPersonalTokensTabExtension.php` directly to `src/Hook/`
- **Option B:** Introduce organization → Create `src/Hook/Console/` and `src/Hook/Portal/` subdirectories

Same question for templates: flat (`templates/portal_tokens_tab.html.twig`) or organized (`templates/portal/personal_tokens_tab.html.twig`)?

**Q2: Coding Standards**

Are there specific coding standards or style guides I should follow? (PSR-12, custom Combodo standards, PHPDoc requirements?)

---

## 🔧 Service Layer

**Q3: Service Layer Implementation**

Console implementation delegates to `PersonalTokenService`. The standalone Portal implementation has all logic directly in the hook class (369 lines).

Should Portal also use a service layer approach?
- **Option A:** Keep current standalone approach (faster integration)
- **Option B:** Extract to service layer like Console (extend `PersonalTokenService` or create `PortalPersonalTokenService`?)

---

## ⚙️ Configuration

**Q4: Configuration Scope**

Should the configuration parameters (`max_tokens_per_user`, `default_expiry_days`) be:
- **Shared:** Same limits for Console and Portal users
- **Separate:** Different config for Portal vs Console
- **Unified:** Single config with role-based differentiation

---

## 🔐 Permissions

**Q5: Portal User Permissions**

The PersonalToken class exists, but **no portal-specific permissions** are defined in the datamodel.

How should this be handled?
- Add portal permissions to `datamodel.authent-token.xml`?
- Keep separate datamodel file?
- Different approach?

Which portal profiles should have access by default? (Standalone uses "Portal Power User")

---

## 🌐 Dictionaries

**Q6: Dictionary Key Pattern**

Preferred pattern for Portal dictionary keys?
- `Portal:PersonalTokens:*` (current standalone)
- `PersonalToken:Portal:*` (group by class)
- Share keys with Console where functionality overlaps (e.g., "Application", "Scope")?

---

## 🧪 Testing

**Q7: Testing Requirements**

- What testing framework is used? (PHPUnit?)
- What are the test coverage expectations?
- Any CI/CD requirements I should know about? (Jenkinsfile)

---

## 📦 Release & Migration

**Q8: Target Version & Migration**

- Which version should this be released in? (2.3.0 minor bump or 3.0.0 major?)
- How to support existing standalone extension users? (Migration guide only, or automated script?)
- Should standalone be deprecated after integration?
- Token data compatibility: Since both use the same PersonalToken class, existing tokens should work seamlessly - correct?

---

## 📋 Process

**Q9: PR Submission**

As mentioned in Issue #3, you suggested either:
1. We submit a PR to `Combodo/authent-token`
2. You handle integration internally

**What is your preference?** I'm ready to submit a PR once these questions are clarified.

**Q10: Development Branch**

Which branch should I target? (`develop`, `main`, or feature-specific?)

---

## 📊 Summary of Integration Scope

**What needs to be added:**
- 2 Portal hook classes (~430 lines)
- 3 Twig templates (HTML, JS, CSS)
- Portal dictionary entries (merge into existing 17 language files)
- Portal permissions in datamodel
- Configuration parameters

**What needs to be changed:**
- Namespaces: `Combodo\iTop\Portal\PersonalTokens\Hook` → `Combodo\iTop\AuthentToken\Hook[|Portal]`
- Template paths in PHP code
- Module autoloader entries

**Estimated development time:** 1-2 weeks after clarifications

---

## 🎯 My Current Status

- ✅ Detailed integration plan created (`INTEGRATION_PLAN.md`)
- ✅ authent-token repository analyzed
- ✅ Fork created: https://github.com/LexioJ/authent-token
- ⏳ Awaiting your guidance on these questions

---

**Full details:** See `INTEGRATION_PLAN.md` and `QUESTIONS_FOR_COMBODO.md` in https://github.com/LexioJ/itop-portal-personal-tokens/tree/claude/plan-authent-token-integration-011CUy6CiQh2s3y988PiutJw

Thank you for your guidance!
