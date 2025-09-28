<?php
/**
 * Model file for Portal Personal Tokens extension
 * 
 * @copyright   Copyright (C) 2025 Your Organization
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

/**
 * Extension to add Personal Token management to Portal
 */
class PortalPersonalTokensExtension implements iPortalUIExtension
{
    /**
     * Return the list of portal routes handled by this extension
     */
    public function GetRoutes()
    {
        return array(
            array(
                'path' => '/user/tokens',
                'controller' => 'PortalPersonalTokensController::ViewTokens',
                'method' => 'GET',
                'name' => 'p_user_tokens_list',
            ),
            array(
                'path' => '/user/tokens/create',
                'controller' => 'PortalPersonalTokensController::CreateToken',
                'method' => 'POST',
                'name' => 'p_user_tokens_create',
            ),
            array(
                'path' => '/user/tokens/{id}/regenerate',
                'controller' => 'PortalPersonalTokensController::RegenerateToken',
                'method' => 'POST',
                'name' => 'p_user_tokens_regenerate',
            ),
            array(
                'path' => '/user/tokens/{id}/delete',
                'controller' => 'PortalPersonalTokensController::DeleteToken',
                'method' => 'POST',
                'name' => 'p_user_tokens_delete',
            ),
        );
    }
    
    /**
     * Add JavaScript files to the portal
     */
    public function GetJSFiles()
    {
        return array(
            'itop-portal-personal-tokens/js/personal_tokens.js',
        );
    }
    
    /**
     * Add CSS files to the portal
     */
    public function GetCSSFiles()
    {
        return array(
            'itop-portal-personal-tokens/css/personal_tokens.css',
        );
    }
}

/**
 * Controller for Personal Token management in Portal
 */
class PortalPersonalTokensController
{
    /**
     * Display the list of user's personal tokens
     */
    public static function ViewTokens($oRequest)
    {
        $iUserId = UserRights::GetUserId();
        
        // Security check - ensure user can manage PersonalTokens
        if (!UserRights::IsActionAllowed('PersonalToken', UR_ACTION_MODIFY))
        {
            throw new SecurityException('Not allowed to manage personal tokens');
        }
        
        // Get user's tokens - PersonalToken class should already exist from authent-token extension
        $oSearch = DBObjectSearch::FromOQL("SELECT PersonalToken WHERE user_id = :user_id");
        $oSet = new DBObjectSet($oSearch, array(), array('user_id' => $iUserId));
        
        $aTokens = array();
        while ($oToken = $oSet->Fetch())
        {
            $aTokens[] = array(
                'id' => $oToken->GetKey(),
                'application' => $oToken->Get('application'),
                'scope' => $oToken->Get('scope'),
                'expiration_date' => $oToken->Get('expiration_date'),
                'use_count' => $oToken->Get('use_count'),
                'last_use_date' => $oToken->Get('last_use_date'),
            );
        }
        
        // Return view with tokens
        $oPage = new PortalWebPage('Personal Tokens');
        $oPage->SetTokens($aTokens);
        $oPage->SetUserId($iUserId);
        
        return $oPage;
    }
    
    /**
     * Create a new personal token
     */
    public static function CreateToken($oRequest)
    {
        $iUserId = UserRights::GetUserId();
        
        // Security check
        if (!UserRights::IsActionAllowed('PersonalToken', UR_ACTION_MODIFY))
        {
            return json_encode(array('success' => false, 'message' => 'Not authorized'));
        }
        
        try
        {
            $sApplication = utils::ReadParam('application', '', false, 'raw_data');
            $sScope = utils::ReadParam('scope', 'REST/JSON', false, 'raw_data');
            
            // Create token using existing PersonalToken class from authent-token
            $oToken = MetaModel::NewObject('PersonalToken');
            $oToken->Set('user_id', $iUserId);
            $oToken->Set('application', $sApplication);
            $oToken->Set('scope', $sScope);
            
            // The PersonalToken class should have a method to generate the token
            // This depends on the authent-token implementation
            $sGeneratedToken = $oToken->GenerateToken();
            $oToken->DBInsert();
            
            return json_encode(array(
                'success' => true,
                'token' => $sGeneratedToken,
                'message' => 'Token created successfully. Copy it now - it won\'t be shown again!',
            ));
        }
        catch (Exception $e)
        {
            return json_encode(array(
                'success' => false,
                'message' => 'Error creating token: ' . $e->getMessage(),
            ));
        }
    }
    
    /**
     * Regenerate an existing token
     */
    public static function RegenerateToken($oRequest)
    {
        $iUserId = UserRights::GetUserId();
        $iTokenId = (int)$oRequest->GetParam('id');
        
        // Security check
        if (!UserRights::IsActionAllowed('PersonalToken', UR_ACTION_MODIFY))
        {
            return json_encode(array('success' => false, 'message' => 'Not authorized'));
        }
        
        try
        {
            $oToken = MetaModel::GetObject('PersonalToken', $iTokenId, false);
            
            if (!$oToken || $oToken->Get('user_id') != $iUserId)
            {
                return json_encode(array('success' => false, 'message' => 'Token not found or not authorized'));
            }
            
            // Regenerate token - method should exist in PersonalToken class
            $sNewToken = $oToken->RegenerateToken();
            $oToken->DBUpdate();
            
            return json_encode(array(
                'success' => true,
                'token' => $sNewToken,
                'message' => 'Token regenerated. Copy the new token now!',
            ));
        }
        catch (Exception $e)
        {
            return json_encode(array(
                'success' => false,
                'message' => 'Error regenerating token: ' . $e->getMessage(),
            ));
        }
    }
    
    /**
     * Delete a token
     */
    public static function DeleteToken($oRequest)
    {
        $iUserId = UserRights::GetUserId();
        $iTokenId = (int)$oRequest->GetParam('id');
        
        // Security check
        if (!UserRights::IsActionAllowed('PersonalToken', UR_ACTION_DELETE))
        {
            return json_encode(array('success' => false, 'message' => 'Not authorized'));
        }
        
        try
        {
            $oToken = MetaModel::GetObject('PersonalToken', $iTokenId, false);
            
            if (!$oToken || $oToken->Get('user_id') != $iUserId)
            {
                return json_encode(array('success' => false, 'message' => 'Token not found or not authorized'));
            }
            
            $oToken->DBDelete();
            
            return json_encode(array('success' => true, 'message' => 'Token deleted successfully'));
        }
        catch (Exception $e)
        {
            return json_encode(array(
                'success' => false,
                'message' => 'Error deleting token: ' . $e->getMessage(),
            ));
        }
    }
}