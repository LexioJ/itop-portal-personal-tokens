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
    
    // Errors
    'Portal:PersonalTokens:Error:Creation' => 'Failed to create token',
    'Portal:PersonalTokens:Error:NotFound' => 'Token not found',
    'Portal:PersonalTokens:Error:Permission' => 'You do not have permission to manage tokens',
));