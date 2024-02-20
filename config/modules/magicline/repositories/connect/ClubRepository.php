<?php

namespace modules\magicline\repositories\connect;

use esign\craftcmscrud\controllers\CraftEntryController;
use esign\craftcmscrud\interfaces\EntryInterface;
use esign\craftcmscrud\support\CraftEntry;
use esign\craftcmscrud\support\CraftMatrixBlock;
use modules\magicline\support\CraftHandle;
use modules\magicline\support\Entry;

class ClubRepository implements EntryInterface
{
    public static function getEntry($club): CraftEntry
    {
        return new CraftEntry(
            CraftHandle::HANDLE_CLUB,
            CraftHandle::IDENTIFIER_CLUB,
            Entry::fieldsFromClub($club),
            self::getMatrixBlocks($club),
            self::getNestedEntries($club),
            self::getAssets($club),
        );
    }

    public static function getMatrixBlocks($club): ?array
    {
        return [
            new CraftMatrixBlock(
                CraftHandle::HANDLE_OPENING_HOURS,
                CraftHandle::HANDLE_OPENING_HOURS_BLOCK,
                self::sortOpeningHours($club->{CraftHandle::HANDLE_OPENING_HOURS})
            ),
        ];
    }

    public static function getNestedEntries($club): ?array
    {
        return [
            new CraftEntry(
                CraftHandle::HANDLE_CLUB_TAGS,
                CraftHandle::IDENTIFIER_CLUB_TAGS,
                Entry::collectionFieldsFromClubTags($club->{CraftHandle::HANDLE_CLUB_TAGS})
            ),
        ];
    }

    public static function getAssets($club): ?array
    {
        return null;
    }

    public static function disableEntries(?array $excludeIds = []): void
    {
        // TODO field_mlClubId_ocebdswj better way to exclude field
        CraftEntryController::disableEntriesExcept(
            CraftHandle::HANDLE_CLUB,
            'field_mlClubId_ocebdswj',
            $excludeIds
        );
    }

    public static function sortOpeningHours(array $openingHours): array
    {
        $days = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];
        usort($openingHours, function ($a, $b) use ($days) {
            return array_search($a->mlOpeningDayFrom, $days) - array_search($b->mlOpeningDayFrom, $days);
        });

        // combine days with the same hours
        $result = [];
        foreach ($openingHours as $object) {
            $key = $object->mlOpeningTimeFrom . '-' . $object->mlOpeningTimeTo;
            if (!isset($result[$key])) {
                $result[$key] = $object;
            } else {
                $result[$key]->mlOpeningDayTo = $object->mlOpeningDayTo;
            }
        }

        return array_values($result);
    }
}
