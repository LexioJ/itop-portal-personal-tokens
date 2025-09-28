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
        'category' => 'portal',

        // Setup
        'dependencies' => array(
            'itop-portal-base/3.0.0',
            'authent-token/2.0.0', // Requires the personal token extension
        ),
        'mandatory' => false,
        'visible' => true,
        'installer' => 'PortalPersonalTokensInstaller',

        // Components
        'datamodel' => array(
            'model.itop-portal-personal-tokens.php',
        ),
        'webservice' => array(),
        'data.struct' => array(),
        'data.sample' => array(),

        // Documentation
        'doc.manual_setup' => '',
        'doc.more_information' => '',

        // Default settings
        'settings' => array(
            'allow_portal_users_tokens' => array(
                'type' => 'bool',
                'description' => 'Allow Portal Users to manage their Personal Tokens',
                'default' => true,
                'value' => true,
            ),
            'portal_token_scopes' => array(
                'type' => 'string',
                'description' => 'Comma-separated list of allowed scopes for Portal Users',
                'default' => 'REST/JSON,Export',
                'value' => 'REST/JSON,Export',
            ),
        ),
    )
);

/**
 * Installer class for the module
 */
class PortalPersonalTokensInstaller extends ModuleInstallerAPI
{
    /**
     * Handler called after the creation of the database schema
     */
    public static function AfterDatabaseCreation(Config $oConfiguration, $sPreviousVersion, $sCurrentVersion)
    {
        // Check if config parameter exists, if not add it
        if (!MetaModel::GetConfig()->HasParameter('allow_rest_services_via_tokens'))
        {
            $oConfiguration->Set('allow_rest_services_via_tokens', true);
            $oConfiguration->WriteToFile();
        }
    }
}