<?php
/**
 * Portal Personal Tokens - User Profile Tab Extension
 *
 * Adds a "Personal Tokens" tab header to the user profile page
 *
 * @copyright   Copyright (C) 2025 Your Organization
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace PortalPersonalTokens\Hook;

use Combodo\iTop\Portal\Hook\iUserProfileTabExtension;
use Dict;
use MetaModel;

/**
 * Extension that adds a "Personal Tokens" tab to the user profile page
 */
class PersonalTokensTabExtension implements iUserProfileTabExtension
{
	const TAB_CODE = 'personal-tokens';
	const TAB_RANK = 20.0;

	/**
	 * @inheritDoc
	 */
	public function IsTabPresent(): bool
	{
		// Only show tab if:
		// 1. PersonalToken class exists (authent-token module is installed)
		// 2. REST services via tokens are enabled
		try {
			$bPersonalTokenClassExists = class_exists('PersonalToken');
			$bRestTokensEnabled = MetaModel::GetConfig()->Get('allow_rest_services_via_tokens') === true;

			return $bPersonalTokenClassExists && $bRestTokensEnabled;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function GetTabRank(): float
	{
		return self::TAB_RANK;
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
	public function GetTabLabel(): string
	{
		return Dict::S('Portal:PersonalTokens:Title');
	}
}
