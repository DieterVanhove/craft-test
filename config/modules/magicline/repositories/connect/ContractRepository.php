<?php

namespace modules\magicline\repositories\connect;

use craft\helpers\StringHelper;
use esign\craftcmscrud\controllers\CraftEntryController;
use esign\craftcmscrud\interfaces\EntryInterface;
use esign\craftcmscrud\support\CraftAsset;
use esign\craftcmscrud\support\CraftEntry;
use esign\craftcmscrud\support\CraftMatrixBlock;
use modules\magicline\support\CraftHandle;
use modules\magicline\support\Entry;

class ContractRepository implements EntryInterface
{
    public static function getEntry($contract): CraftEntry
    {
        return new CraftEntry(
            CraftHandle::HANDLE_CONTRACT,
            CraftHandle::IDENTIFIER_CONTRACT,
            Entry::fieldsFromContract($contract),
            self::getMatrixBlocks($contract),
            self::getNestedEntries($contract),
            self::getAssets($contract),
        );
    }

    public static function getMatrixBlocks($contract): ?array
    {
        return null;
    }

    public static function getNestedEntries($contract): ?array
    {
        return [
            new CraftEntry(
                CraftHandle::HANDLE_CONTRACT_TERM,
                CraftHandle::IDENTIFIER_CONTRACT_TERM,
                Entry::collectionFieldsFromContractTerms(
                    $contract->{CraftHandle::HANDLE_CONTRACT_TERM}
                ),
                CraftEntryController::parseNestedMatrixBlocks(
                    $contract->{CraftHandle::HANDLE_CONTRACT_TERM},
                    CraftHandle::MATRIX_BLOCKS_CONTRACT_TERM
                ),
            )
        ];
    }

    public static function getAssets($contract): ?array
    {
        if (is_null($contract->{CraftHandle::HANDLE_CONTRACT_IMAGE_URL})) {
            return null;
        }

        return [
            new CraftAsset(
                CraftHandle::HANDLE_CONTRACT_IMAGE,
                $contract->{CraftHandle::HANDLE_CONTRACT_IMAGE_URL},
                StringHelper::beforeFirst(StringHelper::afterLast($contract->{CraftHandle::HANDLE_CONTRACT_IMAGE_URL}, '/'), '?'),
                'contract/images'
            )
        ];
    }

    public static function attachContractToClub($club, $contractIds): void
    {
        CraftEntryController::updateOrCreateEntry(
            new CraftEntry(
                CraftHandle::HANDLE_CLUB,
                CraftHandle::IDENTIFIER_CLUB,
                (object) [
                    CraftHandle::IDENTIFIER_CLUB => $club->{CraftHandle::IDENTIFIER_CLUB},
                    CraftHandle::HANDLE_CLUB_CONTRACTS => $contractIds
                ],
            ),
        );
    }

    public static function disableEntries(?array $excludeIds = []): void
    {
        // TODO field_mlClubId_ocebdswj better way to exclude field
        CraftEntryController::disableEntriesExcept(
            CraftHandle::HANDLE_CONTRACT,
            'field_mlContractId_opkejrsj',
            $excludeIds
        );
    }
}
