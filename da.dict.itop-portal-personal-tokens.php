<?php
/**
 * Danish language dictionary for Portal Personal Tokens extension
 *
 * @copyright   Copyright (C) 2025
 * @license     http://opensource.org/licenses/AGPL-3.0
 *
 * NOTE: This file contains English fallback text.
 * Please translate these strings to Danish (Dansk)
 */

Dict::Add('DA DA', 'Danish', 'Dansk', array(
    // Menu items
    'Menu:PersonalTokens' => 'Personal Tokens', // TODO: Translate
    'Menu:PersonalTokens+' => 'Manage your personal API tokens', // TODO: Translate

    // Portal UI
    'Portal:PersonalTokens:Title' => 'Personal API Tokens', // TODO: Translate
    'Portal:PersonalTokens:Description' => 'Create and manage personal tokens for API access', // TODO: Translate
    'Portal:PersonalTokens:NoTokens' => 'You have no active tokens', // TODO: Translate
    'Portal:PersonalTokens:CreateNew' => 'Create New Token', // TODO: Translate

    // Token fields
    'Class:PersonalToken/Attribute:application' => 'Application Name', // TODO: Translate
    'Class:PersonalToken/Attribute:application+' => 'Descriptive name for this token', // TODO: Translate
    'Class:PersonalToken/Attribute:scope' => 'Scope', // TODO: Translate
    'Class:PersonalToken/Attribute:scope+' => 'API permissions for this token', // TODO: Translate
    'Class:PersonalToken/Attribute:expiration_date' => 'Expiration Date', // TODO: Translate
    'Class:PersonalToken/Attribute:expiration_date+' => 'Token will expire on this date', // TODO: Translate
    'Class:PersonalToken/Attribute:last_use_date' => 'Last Used', // TODO: Translate
    'Class:PersonalToken/Attribute:last_use_date+' => 'Date when token was last used', // TODO: Translate
    'Class:PersonalToken/Attribute:use_count' => 'Usage Count', // TODO: Translate
    'Class:PersonalToken/Attribute:use_count+' => 'Number of times token has been used', // TODO: Translate

    // Actions
    'Portal:PersonalTokens:Create' => 'Create Token', // TODO: Translate
    'Portal:PersonalTokens:Regenerate' => 'Regenerate', // TODO: Translate
    'Portal:PersonalTokens:Delete' => 'Delete', // TODO: Translate
    'Portal:PersonalTokens:Copy' => 'Copy to Clipboard', // TODO: Translate

    // Messages
    'Portal:PersonalTokens:Created' => 'Token created successfully', // TODO: Translate
    'Portal:PersonalTokens:Regenerated' => 'Token regenerated successfully', // TODO: Translate
    'Portal:PersonalTokens:Deleted' => 'Token deleted successfully', // TODO: Translate
    'Portal:PersonalTokens:CopyWarning' => 'Make sure to copy your token now. You won\'t be able to see it again!', // TODO: Translate
    'Portal:PersonalTokens:MaxTokensReached' => 'Maximum number of tokens reached (%1$d)', // TODO: Translate
    'Portal:PersonalTokens:ConfirmDelete' => 'Are you sure you want to delete this token?', // TODO: Translate
    'Portal:PersonalTokens:ConfirmRegenerate' => 'Are you sure you want to regenerate this token? Applications using the old token will stop working.', // TODO: Translate

    // Additional translations
    'Portal:PersonalTokens:Loading' => 'Loading tokens...', // TODO: Translate
    'Portal:PersonalTokens:Application' => 'Application Name', // TODO: Translate
    'Portal:PersonalTokens:Application:Placeholder' => 'e.g., Nextcloud, Mobile App, Script', // TODO: Translate
    'Portal:PersonalTokens:Application:Help' => 'Give your token a meaningful name to identify where it\'s used', // TODO: Translate
    'Portal:PersonalTokens:Description:Placeholder' => 'Optional description of what this token is used for', // TODO: Translate
    'Portal:PersonalTokens:ExpiryDays' => 'Expires in', // TODO: Translate
    'Portal:PersonalTokens:Days' => 'days', // TODO: Translate
    'Portal:PersonalTokens:LastUsed' => 'Last Used', // TODO: Translate
    'Portal:PersonalTokens:UseCount' => 'Uses', // TODO: Translate
    'Portal:PersonalTokens:Actions' => 'Actions', // TODO: Translate
    'Portal:PersonalTokens:ExpiryDate' => 'Expires', // TODO: Translate
    'Portal:PersonalTokens:Scope' => 'Scope', // TODO: Translate

    // Token creation success
    'Portal:PersonalTokens:TokenCreated' => 'Token Created Successfully', // TODO: Translate
    'Portal:PersonalTokens:TokenCreated:Success' => 'Your personal token has been created successfully!', // TODO: Translate
    'Portal:PersonalTokens:TokenCreated:Warning' => 'This is the only time you will see the token value. Make sure to copy it now and store it securely.', // TODO: Translate
    'Portal:PersonalTokens:YourToken' => 'Your Personal Token', // TODO: Translate

    // Buttons
    'Portal:Button:Cancel' => 'Cancel', // TODO: Translate
    'Portal:Button:Close' => 'Close', // TODO: Translate

    // Error messages
    'Portal:PersonalTokens:Error:LoadFailed' => 'Failed to load personal tokens', // TODO: Translate
    'Portal:PersonalTokens:Error:CreateFailed' => 'Failed to create personal token', // TODO: Translate
    'Portal:PersonalTokens:Error:DeleteFailed' => 'Failed to delete personal token', // TODO: Translate
    'Portal:PersonalTokens:Error:Creation' => 'Failed to create token', // TODO: Translate
    'Portal:PersonalTokens:Error:NotFound' => 'Token not found', // TODO: Translate
    'Portal:PersonalTokens:Error:Permission' => 'You do not have permission to manage tokens', // TODO: Translate
));
