<?php

namespace modules\magicline\repositories\open;

use craft\helpers\StringHelper;
use esign\craftcmscrud\controllers\CraftEntryController;
use esign\craftcmscrud\interfaces\EntryInterface;
use esign\craftcmscrud\support\CraftAsset;
use esign\craftcmscrud\support\CraftEntry;
use esign\craftcmscrud\support\CraftMatrixBlock;
use modules\magicline\support\CraftHandle;
use modules\magicline\support\Entry;

class ClassRepository implements EntryInterface
{
    public static function getEntry($class): CraftEntry
    {
        return new CraftEntry(
            CraftHandle::HANDLE_CLASS,
            CraftHandle::IDENTIFIER_CLASS,
            Entry::fieldsFromClass($class),
            self::getMatrixBlocks($class),
            self::getNestedEntries($class),
            self::getAssets($class),
        );
    }

    public static function getMatrixBlocks($class): ?array
    {
        return null;
    }

    public static function getNestedEntries($class): ?array
    {
        return null;
    }

    public static function getAssets($class): ?array
    {
        if (is_null($class->{CraftHandle::HANDLE_CLASS_IMAGE_URL})) {
            return null;
        }

        return [
            new CraftAsset(
                CraftHandle::HANDLE_CLASS_IMAGE,
                $class->{CraftHandle::HANDLE_CLASS_IMAGE_URL},
                StringHelper::beforeFirst(StringHelper::afterLast($class->{CraftHandle::HANDLE_CLASS_IMAGE_URL}, '/'), '?'),
                'classes/images'
            )
        ];
    }

    public static function attachClassToClub($clubId, $classIds): void
    {
        CraftEntryController::updateOrCreateEntry(
            new CraftEntry(
                CraftHandle::HANDLE_CLUB,
                CraftHandle::IDENTIFIER_CLUB,
                (object) [
                    CraftHandle::IDENTIFIER_CLUB => $clubId,
                    CraftHandle::HANDLE_CLUB_CLASSES => $classIds
                ],
            ),
        );
    }

    public static function disableEntries(?array $excludeIds = []): void
    {
        // TODO field_mlClubId_ocebdswj better way to exclude field
        CraftEntryController::disableEntriesExcept(
            CraftHandle::HANDLE_CLASS,
            'field_mlClassId_qqxtuvym',
            $excludeIds
        );
    }
}
