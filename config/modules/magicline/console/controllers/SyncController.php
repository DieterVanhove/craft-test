<?php
/**
 * Magicline module for Craft CMS 4.x
 *
 * Jims CRM connection
 *
 * @link      esign.eu
 * @copyright Copyright (c) 2022 dieter vanhove
 */

namespace modules\magicline\console\controllers;

use modules\magicline\Magicline;

use Craft;
use modules\magicline\controllers\connect\CampaignController;
use modules\magicline\controllers\connect\ClubController;
use modules\magicline\controllers\connect\ContractController;
use modules\magicline\controllers\connect\TrialSessionController;
use modules\magicline\controllers\open\ClassController;
use modules\magicline\repositories\open\ClassSlotRepository;
use yii\console\Controller;

/**
 * Sync Commands
 *
 * @author    dieter vanhove
 * @package   MagiclineModule
 * @since     1.0.0
 */
class SyncController extends Controller
{
    public function actionAll()
    {
        echo "syncing clubs \n";
        $this->actionClubs();

        echo "syncing contracts \n";
        $this->actionContracts();

        // TODO Disabled trailsessions for now
        // echo "syncing sessions \n";
        // $this->actionSessions();

        echo "syncing campaigns \n";
        $this->actionCampaigns();

        echo "syncing fitness classes \n";
        $this->actionClasses();
    }

    public function actionClubs(): void
    {
        $client = new ClubController();
        $client->syncClubs();
    }

    public function actionContracts(): void
    {
        $client = new ContractController();
        $client->syncContracts();
    }

    public function actionSessions(): void
    {
        $client = new TrialSessionController();
        $client->syncTrialSessions();
    }

    public function actionCampaigns(): void
    {
        $client = new CampaignController();
        $client->syncCampaigns();
    }

    public function actionClasses(): void
    {
        $client = new ClassController();
        $client->syncClasses();
    }

    public function actionPurgeClassSlots(): void
    {
        echo "purge fitness class slots \n";
        $count = ClassSlotRepository::purgeExpiredEntries();
        echo "deleted $count slots \n";
    }
}
