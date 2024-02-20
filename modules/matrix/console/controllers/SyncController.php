<?php

namespace modules\matrix\console\controllers;

use Craft;
use esign\craftcmscrud\controllers\CraftEntryController;
use esign\craftcmscrud\support\CraftEntry;
use esign\craftcmscrud\support\CraftMatrixBlock;
use stdClass;
use yii\console\Controller;

class SyncController extends Controller
{
    public function actionMatrix(): void
    {
        $fields = new stdClass();
        $fields->identifier = '1';
        $fields->settings = new stdClass();
        $fields->settings->title = 'Test 123';
        $fields->settings->slug = 'test-123';
        $fields->settings->siteId = '1';
        $fields->settings->enabledOnCreate = true;
        $fields->settings->updateTitleAndSlug = true;

        CraftEntryController::updateOrCreateEntry(
            new CraftEntry(
                'hours',
                'identifier',
                $fields,
                self::getMatrixBlocks(),
            )
        );
    }

    public static function getMatrixBlocks(): ?array
    {
        return [
            new CraftMatrixBlock(
                'openingHours',
                'block',
                [
                    [
                        'from' => '10',
                        'to' => '12',
                    ],
                    [
                        'from' => '13',
                        'to' => '19',
                    ]
                ]
            ),
        ];
    }
}
