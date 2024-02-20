<?php

namespace modules\magicline\controllers\connect;

use craft\web\Controller;
use esign\craftcmscrud\controllers\CraftEntryController;
use modules\magicline\Magicline;
use modules\magicline\repositories\connect\CampaignRepository;
use modules\magicline\services\MagiclineApiService;
use modules\magicline\support\connect\campaign\Campaign;
use modules\magicline\support\CraftHandle;
use Psr\Http\Message\StreamInterface;
use craft\elements\Entry;

class CampaignController extends Controller
{
    private MagiclineApiService $apiService;

    public function __construct()
    {
        $this->apiService = Magicline::getInstance()->api;
        parent::init();
    }

    public function fetchCampaigns(string $clubId): StreamInterface
    {
        $response = $this->apiService->performConnectHttpRequest(MagiclineApiService::GET, "/connect/v1/campaign?studioId=$clubId");

        return $response->getBody();
    }

    public function syncCampaigns(): void
    {
        foreach (ClubController::getAllClubs() as $club) {
            if ($club->{CraftHandle::IDENTIFIER_CLUB}) {
                $this->syncCampaign($club);
            }
        }
    }

    public function syncCampaign(Entry $club): void
    {
        $clubId = $club->{CraftHandle::IDENTIFIER_CLUB};
        echo "Syncing campaigns for club $clubId\n";

        $response = Campaign::fromClubResponse($this->fetchCampaigns($clubId));

        $campaignIds = [];
        foreach ($response as $campaignData) {
            $campaignEntry = CraftEntryController::updateOrCreateEntry(
                CampaignRepository::getEntry($campaignData)
            );

            $campaignIds[] = $campaignEntry->id;
        }

        CampaignRepository::attachCampaignToClub($club, $campaignIds);

        // Will be managed manually
        // CampaignRepository::disableEntries(array_column($response, self::IDENTIFIER_CAMPAIGN));
    }
}
