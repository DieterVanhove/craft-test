<?php

namespace modules\magicline\repositories\connect;

use esign\craftcmscrud\controllers\CraftEntryController;
use esign\craftcmscrud\interfaces\EntryInterface;
use esign\craftcmscrud\support\CraftEntry;
use modules\magicline\support\connect\campaign\Campaign;
use modules\magicline\support\CraftHandle;

class CampaignRepository implements EntryInterface
{
    public static function getEntry($campaign): CraftEntry
    {
        return new CraftEntry(
            CraftHandle::HANDLE_CAMPAIGN,
            CraftHandle::IDENTIFIER_CAMPAIGN,
            Campaign::fieldsFromCampaign($campaign),
            self::getMatrixBlocks($campaign),
            self::getNestedEntries($campaign),
            self::getAssets($campaign),
        );
    }

    public static function getMatrixBlocks($campaign): ?array
    {
        return null;
    }

    public static function getNestedEntries($campaign): ?array
    {
        return null;
    }

    public static function getAssets($campaign): ?array
    {
        return null;
    }

    public static function attachCampaignToClub($club, $campaignIds): void
    {
        CraftEntryController::updateOrCreateEntry(
            new CraftEntry(
                CraftHandle::HANDLE_CLUB,
                CraftHandle::IDENTIFIER_CLUB,
                (object) [
                    CraftHandle::IDENTIFIER_CLUB => $club->{CraftHandle::IDENTIFIER_CLUB},
                    CraftHandle::HANDLE_CLUB_CAMPAIGN => $campaignIds

                ],
            ),
        );
    }

    public static function disableEntries(?array $excludeIds = []): void
    {
        // TODO field_mlCampaignId_mvrbzuwi better way to exclude field
        CraftEntryController::disableEntriesExcept(
            CraftHandle::HANDLE_CAMPAIGN,
            'field_mlCampaignId_mvrbzuwi',
            $excludeIds
        );
    }
}
