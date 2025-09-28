<?php
/**
 * Cleanup Expired Tokens
 * 
 * This script removes expired personal tokens from the database.
 * Should be run as a scheduled task (cron job).
 * 
 * Usage: php cleanup_expired_tokens.php [--dry-run]
 */

// Adjust path based on your iTop installation
require_once(__DIR__ . '/../../../approot.inc.php');
require_once(APPROOT . '/application/startup.inc.php');

// Parse command line arguments
$isDryRun = in_array('--dry-run', $argv);

try {
    // Start transaction
    if (!$isDryRun) {
        CMDBSource::Query('START TRANSACTION');
    }
    
    // Find expired tokens
    $sOQL = "SELECT PersonalToken WHERE expiration_date < NOW() AND expiration_date > '1970-01-01'";
    $oSearch = DBObjectSearch::FromOQL($sOQL);
    $oSet = new DBObjectSet($oSearch);
    
    $iCount = 0;
    $aDeleted = array();
    
    echo "Cleanup Expired Personal Tokens\n";
    echo "================================\n";
    echo "Mode: " . ($isDryRun ? "DRY RUN" : "LIVE") . "\n";
    echo "Time: " . date('Y-m-d H:i:s') . "\n\n";
    
    while ($oToken = $oSet->Fetch()) {
        $sApplication = $oToken->Get('application');
        $sUser = 'Unknown';
        
        try {
            $iUserId = $oToken->Get('user_id');
            $oUser = MetaModel::GetObject('User', $iUserId, false);
            if ($oUser) {
                $sUser = $oUser->GetName();
            }
        } catch (Exception $e) {
            // User might be deleted
        }
        
        $sExpired = $oToken->Get('expiration_date');
        
        echo sprintf(
            "Token: %s (User: %s, Expired: %s)\n",
            $sApplication,
            $sUser,
            $sExpired
        );
        
        $aDeleted[] = array(
            'application' => $sApplication,
            'user' => $sUser,
            'expired' => $sExpired
        );
        
        if (!$isDryRun) {
            $oToken->DBDelete();
        }
        
        $iCount++;
    }
    
    if ($iCount > 0) {
        echo "\n";
        echo "Summary:\n";
        echo "--------\n";
        echo "Total expired tokens found: $iCount\n";
        
        if ($isDryRun) {
            echo "No tokens were deleted (dry run mode)\n";
            echo "Run without --dry-run flag to actually delete tokens\n";
        } else {
            echo "All expired tokens have been deleted\n";
            CMDBSource::Query('COMMIT');
        }
    } else {
        echo "No expired tokens found\n";
    }
    
    // Log to file if needed
    $sLogFile = APPROOT . 'log/token_cleanup_' . date('Ymd') . '.log';
    $sLogEntry = sprintf(
        "[%s] %s - Cleaned up %d expired tokens\n",
        date('Y-m-d H:i:s'),
        $isDryRun ? 'DRY RUN' : 'LIVE',
        $iCount
    );
    
    if (!$isDryRun && $iCount > 0) {
        file_put_contents($sLogFile, $sLogEntry, FILE_APPEND);
        
        // Detailed log
        foreach ($aDeleted as $aToken) {
            $sDetail = sprintf(
                "  - %s (User: %s, Expired: %s)\n",
                $aToken['application'],
                $aToken['user'],
                $aToken['expired']
            );
            file_put_contents($sLogFile, $sDetail, FILE_APPEND);
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    
    if (!$isDryRun) {
        CMDBSource::Query('ROLLBACK');
    }
    
    exit(1);
}

echo "\nCleanup completed successfully\n";