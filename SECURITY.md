# Security Policy

## Supported Versions

We actively support the following versions with security updates:

| Version | Supported          | iTop Compatibility |
| ------- | ------------------ | ------------------ |
| 1.1.x   | :white_check_mark: | 3.1.0 - 3.2.x      |
| 1.0.x   | :white_check_mark: | 3.1.0 - 3.2.x      |
| < 1.0   | :x:                | N/A                |

## Security Architecture

### Token Storage

- **Hashing**: All tokens are stored hashed in the database using iTop's `AuthentToken` framework
- **Never Plaintext**: Token values are never stored in plain text
- **One-Time Display**: Generated tokens are shown only once to the user (cannot be retrieved later)
- **Secure Generation**: Tokens are generated using iTop's `AuthentTokenService` with cryptographically secure random values

### Authentication & Authorization

#### User Isolation
- **Query-Level Filtering**: All OQL queries include `user_id` filtering to ensure users can only access their own tokens
- **Double Verification**: Security checks verify token ownership before delete/regenerate operations
  - See [PersonalTokensUserProfileExtension.php:298-308](src/Hook/PersonalTokensUserProfileExtension.php#L298) (delete)
  - See [PersonalTokensUserProfileExtension.php:330-340](src/Hook/PersonalTokensUserProfileExtension.php#L330) (regenerate)

#### Permission Model
- **Portal User Extensions**: Uses iTop's permission system to grant Portal Users specific rights to PersonalToken objects
- **AllowWrite/AllowDelete**: Bypasses standard permissions only for user's own tokens
- **No Admin Required**: Portal users can manage tokens without admin privileges

### CSRF Protection

- **Transaction ID Validation**: All state-changing operations require valid transaction IDs
- **Server-Side Verification**: Transaction IDs are validated by iTop's `UserProfileBrickController` before form processing
- **Single-Use**: Transaction IDs are consumed after use to prevent replay attacks
- **Double-Submit Prevention**: Static flags prevent duplicate form submissions
  - See [PersonalTokensUserProfileExtension.php:79-83](src/Hook/PersonalTokensUserProfileExtension.php#L79)

### XSS Prevention (Updated in v1.1.0)

#### Twig Template Escaping
All user-controlled inputs are properly escaped in Twig templates:

1. **Application Names** (HTML context)
   - Escaped with `|e('html')` filter
   - Location: [personal_tokens_tab.html.twig:78](templates/personal_tokens_tab.html.twig#L78)

2. **Application Names** (JavaScript context)
   - Escaped with `|escape('js')` filter
   - Locations: [personal_tokens_tab.html.twig:85, 90](templates/personal_tokens_tab.html.twig#L85)

3. **Token Values** (HTML attribute context)
   - Escaped with `|e('html_attr')` filter for defense-in-depth
   - Location: [personal_tokens_tab.html.twig:32](templates/personal_tokens_tab.html.twig#L32)

4. **Error Messages**
   - Server-generated messages escaped with `|e('html')` filter
   - Locations: [personal_tokens_tab.html.twig:14, 20](templates/personal_tokens_tab.html.twig#L14)

#### Auto-Escaping
- Twig's auto-escaping is enabled by default in iTop Portal
- Manual escaping is added for defense-in-depth and clarity

### SQL Injection Prevention

#### Parameterized Queries
All database queries use parameterized OQL with placeholder bindings:

```php
// Example: User token query
$oSearch = DBObjectSearch::FromOQL(
    'SELECT PersonalToken WHERE user_id = :user_id'
);
$oSet = new DBObjectSet($oSearch, [], ['user_id' => $iUserId]);
```

**All queries:**
- [PersonalTokensUserProfileExtension.php:199-202](src/Hook/PersonalTokensUserProfileExtension.php#L199) - GetUserTokens
- [PersonalTokensUserProfileExtension.php:300-303](src/Hook/PersonalTokensUserProfileExtension.php#L300) - HandleDeleteToken
- [PersonalTokensUserProfileExtension.php:332-335](src/Hook/PersonalTokensUserProfileExtension.php#L332) - HandleRegenerateToken

#### No String Concatenation
- Never concatenate user input into SQL/OQL strings
- All user-provided values passed via parameter arrays

### Input Validation

#### Server-Side Validation
All inputs are validated on the server before processing:

1. **Application Name**
   - Required field validation
   - Location: [PersonalTokensUserProfileExtension.php:254-256](src/Hook/PersonalTokensUserProfileExtension.php#L254)

2. **Token Scope**
   - Limited to predefined values (REST/JSON, REST/JSON,Export)
   - Enforced by dropdown in UI

3. **Expiry Days**
   - Integer validation with casting
   - Limited to predefined values (30, 90, 180, 365)

4. **Token ID**
   - Integer validation for delete/regenerate operations
   - Ownership verification before any action

#### Client-Side Validation
- HTML5 `required` attribute on critical fields
- Form validation prevents empty submissions
- Not relied upon for security (defense-in-depth only)

### Token Scope Limitation

- **Principle of Least Privilege**: Users select minimal required scope
- **Available Scopes**:
  - `REST/JSON` - Basic REST API access
  - `REST/JSON,Export` - REST API + data export capabilities
- **No Admin Scope**: Personal tokens cannot have admin privileges
- **User Context Only**: Tokens operate in the context of the user who created them

### Rate Limiting & Quotas

- **Maximum Tokens**: Users limited to 5 tokens by default (configurable)
- **Configuration**: `portal_personal_tokens.max_tokens_per_user`
- **Valid Range**: 1-20 tokens recommended
- **Enforcement**: [PersonalTokensUserProfileExtension.php:142-148](src/Hook/PersonalTokensUserProfileExtension.php#L142)

### Session Security

- **iTop Session Management**: Relies on iTop's built-in session handling
- **Portal Authentication**: Requires active Portal session to access tab
- **No Direct API**: Extension only accessible through authenticated Portal sessions

### Logging & Monitoring

#### Error Logging
All errors are logged to iTop's error log:
- Token creation failures
- Permission violations
- Database errors
- Extension activation issues

#### Audit Trail
iTop's built-in audit system tracks:
- PersonalToken object creation
- Token regeneration (update operations)
- Token deletion

**Note**: Token usage (API calls) is logged by iTop's core authentication system, not this extension.

## Known Security Considerations

### iTop Core Dependencies

This extension relies on iTop's security model:
- **iTop XSS Vulnerability (CVE-2024-XXXXX)**: Fixed in iTop 3.1.1
  - **Our Status**: v1.1.0 includes additional XSS escaping for defense-in-depth
  - **Recommendation**: Use iTop 3.1.1+ for maximum security

### Token Exposure Risks

- **Display Warning**: Tokens shown once after creation with prominent warning
- **No Retrieval**: Old token values cannot be retrieved (only regenerate)
- **User Responsibility**: Users responsible for storing tokens securely
- **Recommendation**: Users should treat tokens like passwords

### Browser Security

- **Clipboard API**: Uses deprecated `document.execCommand('copy')` for broad compatibility
  - Modern Clipboard API requires HTTPS
  - Falls back gracefully if copy fails
- **HTTPS Recommended**: Always use HTTPS for iTop Portal in production

## Security Best Practices for Users

### For Portal Users

1. **Create Specific Tokens**: One token per application/integration
2. **Use Descriptive Names**: Clearly identify what each token is for
3. **Minimal Scope**: Select the minimum required scope (REST/JSON vs Export)
4. **Regular Rotation**: Regenerate tokens periodically (every 90-180 days)
5. **Delete Unused**: Remove tokens that are no longer needed
6. **Secure Storage**: Store tokens in secure credential managers, not plain text files
7. **Monitor Usage**: Check "Last Used" and "Use Count" for suspicious activity

### For Administrators

1. **Enable HTTPS**: Always use HTTPS for iTop Portal
2. **Configure Token Limit**: Adjust `max_tokens_per_user` based on your organization's needs
3. **Monitor Logs**: Review iTop error logs for suspicious token activity
4. **Keep iTop Updated**: Use iTop 3.1.1+ to benefit from latest security fixes
5. **Token Authentication**: Ensure `allow_rest_services_via_tokens` is explicitly set
6. **Regular Audits**: Periodically review PersonalToken objects in database for anomalies

### Configuration Security

```php
// Recommended production configuration
'allow_rest_services_via_tokens' => true,
'portal_personal_tokens' => array(
    'max_tokens_per_user' => 5,        // Limit per user (1-20 recommended)
    'default_expiry_days' => 90,       // Default expiration (30-365)
),
```

## Reporting Security Vulnerabilities

### How to Report

**DO NOT** create public GitHub issues for security vulnerabilities.

Instead, please report security issues privately:

1. **Email**: Send details to the repository maintainer
   - Include: Vulnerability description, steps to reproduce, potential impact
   - Use PGP encryption if available (key in repository)

2. **GitHub Security Advisory**: Use GitHub's private vulnerability reporting
   - Navigate to: Repository → Security → Advisories → Report a vulnerability

### What to Include

- **Description**: Clear description of the vulnerability
- **Reproduction**: Step-by-step instructions to reproduce
- **Impact**: Potential impact and attack scenarios
- **Version**: Affected version(s) of the extension
- **iTop Version**: iTop version(s) where vulnerability exists
- **Suggested Fix**: Optional, but appreciated

### Response Timeline

- **Acknowledgment**: Within 48 hours
- **Initial Assessment**: Within 7 days
- **Fix Development**: Depends on severity (critical: 1-7 days, high: 7-30 days)
- **Public Disclosure**: After fix is released and users have time to upgrade (typically 30 days)

## Security Update Process

### Severity Levels

- **Critical**: Remote code execution, authentication bypass
- **High**: XSS, SQL injection, privilege escalation
- **Medium**: CSRF, information disclosure
- **Low**: Minor information leaks, non-exploitable issues

### Update Distribution

1. Security fixes released as patch versions (e.g., 1.1.1)
2. GitHub release with security advisory
3. Notification to known users via GitHub
4. Update on iTop Hub (if listed)

## Security Checklist for Developers

If you're contributing code, ensure:

- [ ] All user inputs are validated server-side
- [ ] Twig templates use appropriate escaping (`|e('html')`, `|escape('js')`, `|e('html_attr')`)
- [ ] Database queries use parameterized OQL (no string concatenation)
- [ ] Authorization checks verify user ownership before state-changing operations
- [ ] Transaction IDs are validated for all POST operations
- [ ] Error messages don't leak sensitive information
- [ ] No sensitive data in client-side JavaScript
- [ ] No use of `eval()`, `innerHTML` with user content, or unsafe DOM manipulation
- [ ] Logging doesn't include token values or sensitive user data

## References

### iTop Security Documentation
- [iTop Security Best Practices](https://www.itophub.io/wiki/page?id=latest%3Aadmin%3Asecurity)
- [iTop Portal Security Model](https://www.itophub.io/wiki/page?id=latest%3Acustomization%3Aportal)

### OWASP Resources
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [XSS Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [SQL Injection Prevention](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)

### Extension-Specific Security
- Token authentication framework: iTop's `authent-token` module
- Twig escaping: [Twig Documentation](https://twig.symfony.com/doc/3.x/filters/escape.html)

---

**Last Updated**: 2025-10-05
**Version**: 1.1.0
