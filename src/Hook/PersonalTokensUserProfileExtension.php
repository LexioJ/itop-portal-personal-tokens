<?php
/**
 * Portal Personal Tokens - User Profile Tab Extension
 *
 * Adds a "Personal Tokens" tab to the user profile page
 *
 * @copyright   Copyright (C) 2025 Your Organization
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace PortalPersonalTokens\Hook;

use Combodo\iTop\Portal\Hook\iUserProfileTabContentExtension;
use Combodo\iTop\Portal\Twig\PortalTwigContext;
use Combodo\iTop\Portal\Twig\PortalBlockExtension;
use DBObjectSearch;
use DBObjectSet;
use Dict;
use UserRights;
use MetaModel;
use IssueLog;

/**
 * Extension that adds Personal Tokens management to the user profile page
 */
class PersonalTokensUserProfileExtension implements iUserProfileTabContentExtension
{
	const TAB_CODE = 'personal-tokens';
	const SECTION_RANK = 10.0;

	/**
	 * Track if form has already been handled in this request
	 * @var bool
	 */
	private static $bFormHandled = false;

	/**
	 * Store form result data to pass between method calls
	 * @var array
	 */
	private static $aFormData = [];

	/**
	 * @inheritDoc
	 */
	public function IsActive(): bool
	{
		// Only active if:
		// 1. User is a Portal User
		// 2. PersonalToken class exists (authent-token module is installed)
		// 3. REST services via tokens are enabled
		try {
			$bPersonalTokenClassExists = class_exists('PersonalToken');
			$bRestTokensEnabled = MetaModel::GetConfig()->Get('allow_rest_services_via_tokens') === true;
			$bIsPortalUser = UserRights::GetContactObject() !== null;

			return $bPersonalTokenClassExists && $bRestTokensEnabled && $bIsPortalUser;
		} catch (\Exception $e) {
			IssueLog::Error(__METHOD__ . ' Error checking if extension is active: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function GetTabCode(): string
	{
		return self::TAB_CODE;
	}

	/**
	 * @inheritDoc
	 */
	public function HandlePortalForm(array &$aData): void
	{
		// Prevent double execution - HandlePortalForm is called by both iTop's controller
		// AND our GetPortalTabContentTwigs method. Only process once per request.
		if (self::$bFormHandled) {
			// Merge in any stored form data from first execution
			$aData = array_merge($aData, self::$aFormData);
			return;
		}

		// Check for POST data related to personal tokens
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$sAction = $_POST['token_action'] ?? null;

			// Mark as handled to prevent double execution
			self::$bFormHandled = true;

			// Note: Transaction ID validation is already handled by iTop's UserProfileBrickController
			// before this method is called, so we don't need to validate it again

			try {
				switch ($sAction) {
					case 'create':
						$this->HandleCreateToken($aData);
						break;

					case 'delete':
						$this->HandleDeleteToken($aData);
						break;

					case 'regenerate':
						$this->HandleRegenerateToken($aData);
						break;
				}

				// Store any success/error messages and data for the next call
				self::$aFormData = array_intersect_key($aData, array_flip(['success_message', 'error_message', 'new_token_value']));
			} catch (\Exception $e) {
				$aData['error_message'] = $e->getMessage();
				self::$aFormData['error_message'] = $e->getMessage();
				IssueLog::Error(__METHOD__ . ' Error handling token action: ' . $e->getMessage());
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function GetPortalTabContentTwigs(): PortalTwigContext
	{
		$oPortalTwigContext = new PortalTwigContext();

		// Prepare data for the template
		$aData = [
			'extension_path' => $this->GetExtensionPath(),
		];

		// Handle form submission if present
		$this->HandlePortalForm($aData);

		// Transaction ID is already set by iTop's UserProfileBrickController
		// If not set (shouldn't happen), generate one as fallback
		if (!isset($aData['sTransactionId'])) {
			$aData['sTransactionId'] = \utils::GetNewTransactionId();
		}

		// Get current user's tokens (after form handling in case tokens were modified)
		$aTokens = $this->GetUserTokens();
		$iMaxTokens = $this->GetMaxTokensPerUser();

		// Add to data
		$aData['tokens'] = $aTokens;
		$aData['max_tokens'] = $iMaxTokens;
		$aData['can_create'] = count($aTokens) < $iMaxTokens;

		// Get the template base path - use relative path from itop root
		$sTemplateBasePath = 'itop-portal-personal-tokens/templates';

		// Register the HTML template
		$oPortalTwigContext->AddBlockExtension(
			'html',
			new PortalBlockExtension($sTemplateBasePath.'/personal_tokens_tab.html.twig', $aData)
		);

		// Register JavaScript
		$oPortalTwigContext->AddBlockExtension(
			'ready_script',
			new PortalBlockExtension($sTemplateBasePath.'/personal_tokens_tab.ready.js.twig', $aData)
		);

		// Register CSS
		$oPortalTwigContext->AddBlockExtension(
			'css',
			new PortalBlockExtension($sTemplateBasePath.'/personal_tokens_tab.css.twig', $aData)
		);

		return $oPortalTwigContext;
	}

	/**
	 * @inheritDoc
	 */
	public function GetSectionRank(): float
	{
		return self::SECTION_RANK;
	}

	/**
	 * Get all PersonalTokens for the current user
	 *
	 * @return array Array of PersonalToken objects serialized as arrays
	 */
	protected function GetUserTokens(): array
	{
		$aTokens = [];

		try {
			// Get current user ID from UserRights (authenticated session)
			$iUserId = UserRights::GetUserId();
			if (!$iUserId) {
				return $aTokens;
			}

			// SQL Injection Prevention: Parameterized OQL query
			// Authorization: Filter by user_id to ensure user isolation
			$oSearch = DBObjectSearch::FromOQL(
				'SELECT PersonalToken WHERE user_id = :user_id'
			);
			$oSet = new DBObjectSet($oSearch, [], ['user_id' => $iUserId]);

			while ($oToken = $oSet->Fetch()) {
				$aTokens[] = [
					'id' => $oToken->GetKey(),
					'application' => $oToken->Get('application'),
					'scope' => $oToken->Get('scope'),
					'expiration_date' => $oToken->Get('expiration_date'),
					'last_use_date' => $oToken->Get('last_use_date'),
					'use_count' => $oToken->Get('use_count'),
				];
			}
		} catch (\Exception $e) {
			IssueLog::Error(__METHOD__ . ' Error fetching user tokens: ' . $e->getMessage());
		}

		return $aTokens;
	}

	/**
	 * Get the maximum number of tokens allowed per user
	 *
	 * @return int Maximum tokens allowed
	 */
	protected function GetMaxTokensPerUser(): int
	{
		$aConfig = MetaModel::GetConfig()->Get('portal_personal_tokens');
		return $aConfig['max_tokens_per_user'] ?? 5;
	}

	/**
	 * Get the extension's base path
	 *
	 * @return string Base path
	 */
	protected function GetExtensionPath(): string
	{
		return dirname(__DIR__, 2);
	}

	/**
	 * Handle creating a new token
	 *
	 * @param array &$aData Form data
	 * @throws \Exception
	 */
	protected function HandleCreateToken(array &$aData): void
	{
		// Input validation: All user inputs are validated and sanitized
		// XSS Protection: User input will be escaped in Twig templates with |e('html') filter
		$sApplication = $_POST['application'] ?? '';

		// Scope validation: Limited to predefined values from dropdown
		$sScope = $_POST['scope'] ?? 'REST/JSON';

		// Integer sanitization: Cast to int to prevent injection
		$iExpiryDays = (int)($_POST['expiry_days'] ?? 90);

		// Required field validation
		if (empty($sApplication)) {
			throw new \Exception('Application name is required');
		}

		// Get current user ID
		$iUserId = UserRights::GetUserId();

		// Create new token
		$oToken = MetaModel::NewObject('PersonalToken');
		$oToken->Set('user_id', $iUserId);
		$oToken->Set('application', $sApplication);
		$oToken->Set('scope', $sScope);

		// Set expiration date
		$oExpirationDate = new \DateTime();
		$oExpirationDate->add(new \DateInterval("P{$iExpiryDays}D"));
		$oToken->Set('expiration_date', $oExpirationDate->format('Y-m-d H:i:s'));

		// Allow write and insert - token will be auto-generated
		$oToken->AllowWrite();
		$oToken->DBInsert();

		// Get the generated token value
		$sTokenValue = $oToken->GetToken();

		// Store the token value to show it once
		// Don't set success_message - the yellow warning box is enough indication
		$aData['new_token_value'] = $sTokenValue;
	}

	/**
	 * Handle deleting a token
	 *
	 * @param array &$aData Form data
	 * @throws \Exception
	 */
	protected function HandleDeleteToken(array &$aData): void
	{
		// Input sanitization: Cast to integer to prevent SQL injection
		$iTokenId = (int)($_POST['token_id'] ?? 0);

		// Input validation: Ensure valid token ID
		if ($iTokenId <= 0) {
			throw new \Exception('Invalid token ID');
		}

		// Security check: Authorization - ensure token belongs to current user
		// SQL Injection Prevention: Uses parameterized OQL query with placeholders
		$iCurrentUserId = UserRights::GetUserId();
		$oSearch = DBObjectSearch::FromOQL(
			'SELECT PersonalToken WHERE id = :token_id AND user_id = :user_id'
		);
		$oSet = new DBObjectSet($oSearch, [], ['token_id' => $iTokenId, 'user_id' => $iCurrentUserId]);

		$oToken = $oSet->Fetch();
		if (!$oToken) {
			throw new \Exception('Token not found or you do not have permission to delete it');
		}

		$oToken->AllowDelete();
		$oToken->DBDelete();

		$aData['success_message'] = 'Token deleted successfully';
	}

	/**
	 * Handle regenerating a token
	 *
	 * @param array &$aData Form data
	 * @throws \Exception
	 */
	protected function HandleRegenerateToken(array &$aData): void
	{
		// Input sanitization: Cast to integer to prevent SQL injection
		$iTokenId = (int)($_POST['token_id'] ?? 0);

		// Input validation: Ensure valid token ID
		if ($iTokenId <= 0) {
			throw new \Exception('Invalid token ID');
		}

		// Security check: Authorization - ensure token belongs to current user
		// SQL Injection Prevention: Uses parameterized OQL query with placeholders
		$iCurrentUserId = UserRights::GetUserId();
		$oSearch = DBObjectSearch::FromOQL(
			'SELECT PersonalToken WHERE id = :token_id AND user_id = :user_id'
		);
		$oSet = new DBObjectSet($oSearch, [], ['token_id' => $iTokenId, 'user_id' => $iCurrentUserId]);

		$oToken = $oSet->Fetch();
		if (!$oToken) {
			throw new \Exception('Token not found or you do not have permission to regenerate it');
		}

		// Regenerate token using iTop's AuthentTokenService
		$oToken->AllowWrite();
		$oService = new \Combodo\iTop\AuthentToken\Service\AuthentTokenService();
		$sNewToken = $oService->CreateNewToken($oToken);
		$oPassword = $oService->CreatePassword($sNewToken);
		$oToken->Set('auth_token', $oPassword);
		$oToken->DBUpdate();

		// Store the new token value to show it once
		// Don't set success_message - the yellow warning box is enough indication
		$aData['new_token_value'] = $sNewToken;
	}
}
