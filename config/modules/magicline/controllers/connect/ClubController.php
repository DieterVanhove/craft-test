<?php

namespace modules\magicline\controllers\connect;

use craft\elements\Entry as ElementsEntry;
use craft\web\Controller;
use modules\magicline\support\connect\club\Club;

use Psr\Http\Message\StreamInterface;
use esign\craftcmscrud\controllers\CraftEntryController;
use modules\magicline\Magicline;
use modules\magicline\repositories\connect\ClubRepository;
use modules\magicline\services\MagiclineApiService;
use modules\magicline\support\CraftHandle;

class ClubController extends Controller
{
    private MagiclineApiService $apiService;

    public function __construct()
    {
        $this->apiService = Magicline::getInstance()->api;
        parent::init();
    }

    public static function getAllClubs()
    {
        return ElementsEntry::find()
            ->section(CraftHandle::HANDLE_CLUB)
            ->status(ElementsEntry::statuses())
            ->type(CraftHandle::HANDLE_CLUB)->all();
    }

    public function fetchClubs(): StreamInterface
    {
        $response = $this->apiService->performConnectHttpRequest(MagiclineApiService::GET, '/connect/v2/studio');

        return $response->getBody();
    }

    public function syncClubs(): void
    {
        $response = Club::fromClubResponse($this->fetchClubs());
        foreach ($response as $club) {
            dd(ClubRepository::getEntry($club));
            CraftEntryController::updateOrCreateEntry(
                dd(ClubRepository::getEntry($club))
            );
        }

        // Will be managed manually
        // ClubRepository::disableEntries(array_column($response, CraftHandle::IDENTIFIER_CLUB));
    }
}
