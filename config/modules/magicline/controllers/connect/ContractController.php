<?php

namespace modules\magicline\controllers\connect;

use Craft;
use craft\elements\Entry;
use craft\web\Controller;
use esign\craftcmscrud\controllers\CraftEntryController;
use modules\magicline\Magicline;
use modules\magicline\repositories\connect\ContractRepository;
use modules\magicline\services\MagiclineApiService;
use modules\magicline\support\connect\contract\Contract;
use modules\magicline\support\CraftHandle;
use Psr\Http\Message\StreamInterface;

class ContractController extends Controller
{
    private MagiclineApiService $apiService;

    public function __construct()
    {
        $this->apiService = Magicline::getInstance()->api;
        parent::init();
    }

    public function fetchStudioContracts(string $clubId): StreamInterface
    {
        $response = $this->apiService->performConnectHttpRequest(MagiclineApiService::GET, "/connect/v1/rate-bundle?studioId=$clubId");

        return $response->getBody();
    }

    public function syncContracts(): void
    {
        foreach (ClubController::getAllClubs() as $club) {
            if ($club->{CraftHandle::IDENTIFIER_CLUB}) {
                $this->syncContract($club);
            }
        }
    }

    public function syncContract(Entry $club): void
    {
        $clubId = $club->{CraftHandle::IDENTIFIER_CLUB};
        echo "Syncing contracts for club $clubId\n";
        $siteIdsBel = [1, 6, 7];
        $siteIdsLux = [2];

        $response = Contract::fromContractResponse($this->fetchStudioContracts($clubId));

        // if belgium club
        $contractIds = [];
        if ($club->mlClubZoneId === 'Europe/Brussels') {
            foreach ($siteIdsBel as $siteId) {
                foreach($response as $contractData) {
                    $contractData->siteId = $siteId;
                    $entry = CraftEntryController::updateOrCreateEntry(
                        ContractRepository::getEntry($contractData)
                    );
                    $contractIds[] = $entry->id;
                }
            }
        } else {
            foreach ($siteIdsLux as $siteId) {
                foreach($response as $contractData) {
                    $contractData->siteId = $siteId;
                    $entry = CraftEntryController::updateOrCreateEntry(
                        ContractRepository::getEntry($contractData)
                    );
                    $contractIds[] = $entry->id;
                }
            }
        }

        ContractRepository::attachContractToClub($club, $contractIds);

        // Will be managed manually
        // ContractRepository::disableEntries(array_column($response, CraftHandle::IDENTIFIER_CONTRACT));
    }
}
