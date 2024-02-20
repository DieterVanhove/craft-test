<?php

namespace modules\magicline\controllers\connect;

use craft\web\Controller;
use esign\craftcmscrud\controllers\CraftEntryController;
use esign\craftcmscrud\support\CraftEntry;
use esign\craftcmscrud\support\CraftMatrixBlock;
use modules\magicline\Magicline;
use modules\magicline\services\MagiclineApiService;
use modules\magicline\support\Entry;
use modules\magicline\support\connect\trailSession\SessionDates;
use modules\magicline\support\connect\trailSession\TrailSession;
use modules\magicline\support\CraftHandle;
use Psr\Http\Message\StreamInterface;

class TrialSessionController extends Controller
{
    public const HANDLE_TRAIL_SESSIONS = 'mlSessions';
    // TODO change identifier
    public const IDENTIFIER_TRAIL_SESSIONS = 'mlSessionName';
    public const HANDLE_SESSION_SLOT = 'mlSessionSlots';
    public const HANDLE_SESSION_SLOT_BLOCK = 'mlSlotBlock';

    private MagiclineApiService $apiService;

    public function __construct()
    {
        $this->apiService = Magicline::getInstance()->api;
        parent::init();
    }

    public function fetchTrailSessions(string $clubId, SessionDates $dates): StreamInterface
    {
        $response = $this->apiService->performConnectHttpRequest(
            MagiclineApiService::GET,
            "/connect/v1/trialsession?studioId=$clubId&startDate=$dates->startDate&endDate=$dates->endDate"
        );

        return $response->getBody();
    }

    public function syncTrialSessions(): void
    {
        foreach (ClubController::getAllClubs() as $club) {
            $this->syncTrialSession($club->{CraftHandle::IDENTIFIER_CLUB});
        }
    }

    public function syncTrialSession(string $clubId): void
    {
        $response = TrailSession::fromTrailSessionsResponse($this->fetchTrailSessions($clubId, SessionDates::getSessionsForAMonth()));
        foreach ($response as $session) {
            $trailSessionEntry = CraftEntryController::updateOrCreateEntry(
                new CraftEntry(
                    self::HANDLE_TRAIL_SESSIONS,
                    self::IDENTIFIER_TRAIL_SESSIONS,
                    Entry::fieldsFromTrailSessions($session),
                    [
                        new CraftMatrixBlock(
                            self::HANDLE_SESSION_SLOT,
                            self::HANDLE_SESSION_SLOT_BLOCK,
                            $session->{self::HANDLE_SESSION_SLOT}
                        )
                    ]
                )
            );

            // TODO not clear, club => class => slot
            // add trailSession to club
            // CraftEntryController::updateOrCreateEntry(
            //     new CraftEntry(
            //         ClubController::HANDLE_CLUB,
            //         ClubController::IDENTIFIER_CLUB,
            //         (object) [
            //             'mlClubId' => $clubId,
            //             'mlClubContract' => [$trailSessionEntry->id]
            //         ],
            //     ),
            // );
        }
    }
}
