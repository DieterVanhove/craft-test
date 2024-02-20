<?php

namespace modules\magicline\controllers\open;

use craft\web\Controller;
use esign\craftcmscrud\controllers\CraftEntryController;
use modules\magicline\Magicline;
use modules\magicline\repositories\open\ClassSlotRepository;
use modules\magicline\services\MagiclineApiService;
use modules\magicline\support\open\class\ClassSlot;
use Psr\Http\Message\StreamInterface;

class ClassSlotController extends Controller
{
    private MagiclineApiService $apiService;

    public function __construct()
    {
        $this->apiService = Magicline::getInstance()->api;
        parent::init();
    }

    public function fetchClassSlots(string $apiKey, int $classId): StreamInterface
    {
        $response = $this->apiService->performOpenHttpRequest(MagiclineApiService::GET, "/v1/classes/$classId/slots", $apiKey);

        return $response->getBody();
    }

    public function syncClassSlots(string $apiKey, int $clubId, int $classId): array
    {
        $response = ClassSlot::fromClassSlotResponse($this->fetchClassSlots($apiKey, $classId), $clubId);
        foreach($response as $classSlotData) {
            $classSlotEntry = CraftEntryController::updateOrCreateEntry(
                ClassSlotRepository::getEntry($classSlotData)
            );
            $slots[] = $classSlotEntry;
        }

        ClassSlotRepository::attachClassSlotToClass($classId, $slots);

        return $response;
    }
}
