<?php
/**
 * English language dictionary for Portal Personal Tokens extension
 *
 * @copyright   Copyright (C) 2024
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

Dict::Add('EN US', 'English', 'English', array(
    // Menu items
    'Menu:PersonalTokens' => 'Personal Tokens',
    'Menu:PersonalTokens+' => 'Manage your personal API tokens',
    
    // Portal UI
    'Portal:PersonalTokens:Title' => 'Personal API Tokens',
    'Portal:PersonalTokens:Description' => 'Create and manage personal tokens for API access',
    'Portal:PersonalTokens:NoTokens' => 'You have no active tokens',
    'Portal:PersonalTokens:CreateNew' => 'Create New Token',
    
    // Token fields
    'Class:PersonalToken/Attribute:application' => 'Application Name',
    'Class:PersonalToken/Attribute:application+' => 'Descriptive name for this token',
    'Class:PersonalToken/Attribute:scope' => 'Scope',
    'Class:PersonalToken/Attribute:scope+' => 'API permissions for this token',
    'Class:PersonalToken/Attribute:expiration_date' => 'Expiration Date',
    'Class:PersonalToken/Attribute:expiration_date+' => 'Token will expire on this date',
    'Class:PersonalToken/Attribute:last_use_date' => 'Last Used',
    'Class:PersonalToken/Attribute:last_use_date+' => 'Date when token was last used',
    'Class:PersonalToken/Attribute:use_count' => 'Usage Count',
    'Class:PersonalToken/Attribute:use_count+' => 'Number of times token has been used',
    
    // Actions
    'Portal:PersonalTokens:Create' => 'Create Token',
    'Portal:PersonalTokens:Regenerate' => 'Regenerate',
    'Portal:PersonalTokens:Delete' => 'Delete',
    'Portal:PersonalTokens:Copy' => 'Copy to Clipboard',
    
    // Messages
    'Portal:PersonalTokens:Created' => 'Token created successfully',
    'Portal:PersonalTokens:Regenerated' => 'Token regenerated successfully',
    'Portal:PersonalTokens:Deleted' => 'Token deleted successfully',
    'Portal:PersonalTokens:CopyWarning' => 'Make sure to copy your token now. You won\'t be able to see it again!',
    'Portal:PersonalTokens:MaxTokensReached' => 'Maximum number of tokens reached (%1$d)',
    'Portal:PersonalTokens:ConfirmDelete' => 'Are you sure you want to delete this token?',
    'Portal:PersonalTokens:ConfirmRegenerate' => 'Are you sure you want to regenerate this token? Applications using the old token will stop working.',
    
    // Additional translations needed for the template
    'Portal:PersonalTokens:Loading' => 'Loading tokens...',
    'Portal:PersonalTokens:Application' => 'Application Name',
    'Portal:PersonalTokens:Application:Placeholder' => 'e.g., Nextcloud, Mobile App, Script',
    'Portal:PersonalTokens:Application:Help' => 'Give your token a meaningful name to identify where it\'s used',
    'Portal:PersonalTokens:Description:Placeholder' => 'Optional description of what this token is used for',
    'Portal:PersonalTokens:ExpiryDays' => 'Expires in',
    'Portal:PersonalTokens:Days' => 'days',
    'Portal:PersonalTokens:LastUsed' => 'Last Used',
    'Portal:PersonalTokens:UseCount' => 'Uses',
    'Portal:PersonalTokens:Actions' => 'Actions',
    'Portal:PersonalTokens:ExpiryDate' => 'Expires',
    'Portal:PersonalTokens:Scope' => 'Scope',
    
    // Token creation success
    'Portal:PersonalTokens:TokenCreated' => 'Token Created Successfully',
    'Portal:PersonalTokens:TokenCreated:Success' => 'Your personal token has been created successfully!',
    'Portal:PersonalTokens:TokenCreated:Warning' => 'This is the only time you will see the token value. Make sure to copy it now and store it securely.',
    'Portal:PersonalTokens:YourToken' => 'Your Personal Token',
    
    // Buttons
    'Portal:Button:Cancel' => 'Cancel',
    'Portal:Button:Close' => 'Close',
    
    // Additional error messages
    'Portal:PersonalTokens:Error:LoadFailed' => 'Failed to load personal tokens',
    'Portal:PersonalTokens:Error:CreateFailed' => 'Failed to create personal token',
    'Portal:PersonalTokens:Error:DeleteFailed' => 'Failed to delete personal token',
    
    // Errors
    'Portal:PersonalTokens:Error:Creation' => 'Failed to create token',
    'Portal:PersonalTokens:Error:NotFound' => 'Token not found',
    'Portal:PersonalTokens:Error:Permission' => 'You do not have permission to manage tokens',
));
