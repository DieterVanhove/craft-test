<?php

namespace modules\magicline\repositories\open;

use craft\helpers\StringHelper;
use esign\craftcmscrud\controllers\CraftEntryController;
use esign\craftcmscrud\support\CraftAsset;
use esign\craftcmscrud\support\CraftEntry;
use esign\craftcmscrud\support\CraftMatrixBlock;
use modules\magicline\support\CraftHandle;
use modules\magicline\support\Entry;
use craft\elements\Entry as CraftElementEntry;
use craft\helpers\DateTimeHelper;
use Craft;
use esign\craftcmscrud\interfaces\EntryInterface;

class ClassSlotRepository implements EntryInterface
{
    public static function getEntry($classSlot): CraftEntry
    {
        return new CraftEntry(
            CraftHandle::HANDLE_CLASS_SLOT,
            CraftHandle::IDENTIFIER_CLASS_SLOT,
            Entry::fieldsFromClassSlot($classSlot),
            self::getMatrixBlocks($classSlot),
            self::getNestedEntries($classSlot),
            self::getAssets($classSlot),
        );
    }

    public static function getMatrixBlocks($classSlot): ?array
    {
        return null;
    }

    public static function getNestedEntries($classSlot): ?array
    {
        $entries = [
            self::getNestedInstructors($classSlot),
            self::getNestedLocation($classSlot),
        ];

        // Filter out null values from the array
        $entries = array_filter($entries, function ($entry) {
            return $entry !== null;
        });

        return $entries;
    }

    public static function getAssets($classSlot): ?array
    {
        return null;
    }

    public static function getNestedInstructors($classSlot): ?CraftEntry
    {
        if (!isset($classSlot->{CraftHandle::HANDLE_CLASS_INSTRUCTOR})) {
            return null;
        }

        return new CraftEntry(
            CraftHandle::HANDLE_CLASS_INSTRUCTOR,
            CraftHandle::IDENTIFIER_CLASS_INSTRUCTOR,
            Entry::collectionFieldsFromClassInstructor(
                $classSlot->{CraftHandle::HANDLE_CLASS_INSTRUCTOR}
            )
        );
    }

    public static function getNestedLocation($classSlot): ?CraftEntry
    {
        if (!isset($classSlot->{CraftHandle::HANDLE_CLASS_LOCATION})) {
            return null;
        }

        return new CraftEntry(
            CraftHandle::HANDLE_CLASS_LOCATION,
            CraftHandle::IDENTIFIER_CLASS_LOCATION,
            Entry::collectionFieldsFromClassLocation(
                $classSlot->{CraftHandle::HANDLE_CLASS_LOCATION}
            )
        );
    }

    public static function getAllClassSlots($classId, $classSlotEntries)
    {
        $existingClassSlots = CraftElementEntry::find()
            ->status(CraftElementEntry::statuses())
            ->section(CraftHandle::HANDLE_CLASS)
            ->{CraftHandle::IDENTIFIER_CLASS}($classId)
            ->one()
            ->{CraftHandle::HANDLE_CLASS_SLOT}
            ->all();
        $mergedSlots = array_merge(
            array_column($existingClassSlots, 'id'),
            array_column($classSlotEntries, 'id')
        );

        return array_unique($mergedSlots);
    }

    public static function attachClassSlotToClass($classId, $classSlotEntries): void
    {
        // add class to club (related entry)
        // TODO can be cleaner
        CraftEntryController::updateOrCreateEntry(
            new CraftEntry(
                CraftHandle::HANDLE_CLASS,
                CraftHandle::IDENTIFIER_CLASS,
                (object) [
                    CraftHandle::IDENTIFIER_CLASS => $classId,
                    // instead of overwriting the slots merge them with the existing ones
                    CraftHandle::HANDLE_CLASS_SLOT => self::getAllClassSlots($classId, $classSlotEntries)
                ],
            ),
        );
    }

    public static function disableEntries(int $clubId, ?array $excludeIds = []): void
    {
        $entries = CraftElementEntry::find()
            ->section(CraftHandle::HANDLE_CLASS_SLOT)
            ->where([
                'AND',
                ['NOT', ['content.field_mlClassSlotId_rlqfnkne' => $excludeIds]],
                ['content.field_mlClassSlotClubId_zonwbljd' => $clubId],
            ])
            ->all();

        foreach ($entries as $entry) {
            CraftEntryController::disableEntry($entry);
        }
    }

    public static function purgeExpiredEntries(): int
    {
        $currentDateTime = DateTimeHelper::currentUTCDateTime()->format('Y-m-d');

        // TODO field_mlClassSlotEndDateTime_hcivwepa better way to select field
        $entries = CraftElementEntry::find()
            ->section('mlClassSlots')
            ->status(CraftElementEntry::statuses())
            ->where(['<', 'content.field_mlClassSlotEndDateTime_hcivwepa', $currentDateTime])
            ->all();

        $deletedCount = 0;

        foreach ($entries as $entry) {
            if (Craft::$app->elements->deleteElement($entry, true)) {
                $deletedCount++;
            } else {
                throw new \Exception("Couldn't delete entry: " . print_r($entry->getErrors(), true));
            }
        }

        return $deletedCount;
    }
}
