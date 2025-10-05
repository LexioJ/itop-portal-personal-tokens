<?php
/**
 * Module: Portal Personal Tokens
 *
 * Enables Portal Users to manage Personal Tokens for REST API access
 *
 * @copyright   Copyright (C) 2025 Your Organization
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

SetupWebPage::AddModule(
    __FILE__,
    'itop-portal-personal-tokens/1.1.0',
    array(
        // Identification
        'label' => 'Portal Personal Tokens',
        'category' => 'authentication',

        // Setup
        'dependencies' => array(
            'authent-token/2.0.0',
            'itop-portal-base/3.0.0',
        ),
        'mandatory' => false,
        'visible' => true,

        // Components
        'datamodel' => array(
            'src/Hook/PersonalTokensTabExtension.php',
            'src/Hook/PersonalTokensUserProfileExtension.php',
        ),
        'webservice' => array(),
        'data.struct' => array(),
        'data.sample' => array(),

        // Documentation
        'doc.manual_setup' => '',
        'doc.more_information' => 'https://github.com/LexioJ/itop-portal-personal-tokens',

        // Default settings
        'settings' => array(
            'portal_personal_tokens' => array(
                'enabled' => true,
                'max_tokens_per_user' => 5,
                'default_expiry_days' => 90,
            ),
        ),
    )
);
