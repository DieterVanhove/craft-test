<?php

namespace modules\magicline\controllers\open;

use esign\craftcmscrud\controllers\CraftEntryController;
use craft\elements\Entry as ElementsEntry;
use craft\web\Controller;
use modules\magicline\controllers\connect\ClubController;
use modules\magicline\Magicline;
use modules\magicline\repositories\open\ClassRepository;
use modules\magicline\repositories\open\ClassSlotRepository;
use modules\magicline\support\open\class\FitnessClass;
use modules\magicline\support\CraftHandle;
use Psr\Http\Message\StreamInterface;
use modules\magicline\services\MagiclineApiService;

class ClassController extends Controller
{
    private MagiclineApiService $apiService;

    public function __construct()
    {
        $this->apiService = Magicline::getInstance()->api;
        parent::init();
    }

    public function fetchStudioClasses(string $apiKey): StreamInterface
    {
        $response = $this->apiService->performOpenHttpRequest(MagiclineApiService::GET, '/v1/classes', $apiKey);

        return $response->getBody();
    }

    public function syncClasses(): void
    {
        foreach (ClubController::getAllClubs() as $club) {
            if (empty($club->{CraftHandle::IDENTIFIER_CLUB_API})) {
                continue;
            }

            $this->syncClass($club);
        }
    }

    public function syncClass(ElementsEntry $club): void
    {
        $apiKey = $club->{CraftHandle::IDENTIFIER_CLUB_API};
        $clubId = $club->{CraftHandle::IDENTIFIER_CLUB};
        echo "Syncing classes for club $clubId\n";
        if (!$apiKey) {
            echo "No Api key\n";
            return;
        }


        $response = FitnessClass::fromClassResponse($this->fetchStudioClasses($apiKey), $apiKey);

        $classSlots = [];
        $classIds = [];
        foreach($response as $classData) {
            $classEntry = CraftEntryController::updateOrCreateEntry(
                ClassRepository::getEntry($classData)
            );

            $classIds[] = $classEntry->id;

            // Class slots
            $responses = (new ClassSlotController())->syncClassSlots($apiKey, $clubId, $classData->{CraftHandle::IDENTIFIER_CLASS});
            $classSlots = array_merge($classSlots, $responses);
        }

        ClassRepository::attachClassToClub($clubId, $classIds);

        // Disable all class slots that aren't in the response anymore for that club
        ClassSlotRepository::disableEntries($clubId, array_column($classSlots, CraftHandle::IDENTIFIER_CLASS_SLOT));

        // Will be managed manually
        // ClassRepository::disableEntries(array_column($response, CraftHandle::IDENTIFIER_CONTRACT));
    }
}
