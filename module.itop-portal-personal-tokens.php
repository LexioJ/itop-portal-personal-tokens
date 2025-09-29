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
    'itop-portal-personal-tokens/1.0.0',
    array(
        // Identification
        'label' => 'Portal Personal Tokens',
        'category' => 'authentication',

        // Setup
        'dependencies' => array(
            'authent-token/2.0.0', // Requires the personal token extension
        ),
        'mandatory' => false,
        'visible' => true,

        // Components
        'datamodel' => array(
            // Using standard BrowseBrick - no custom model needed
        ),
        'webservice' => array(),
        'data.struct' => array(),
        'data.sample' => array(),

        // Documentation
        'doc.manual_setup' => '',
        'doc.more_information' => '',

        // Default settings
        'settings' => array(),
    )
);
