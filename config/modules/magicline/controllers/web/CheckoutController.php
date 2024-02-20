<?php

namespace modules\magicline\controllers\web;

use craft\helpers\App;
use craft\web\Controller;
use modules\magicline\Magicline;
use modules\magicline\services\MagiclineApiService;
use craft\helpers\Json;
use yii\web\Response;
use Craft;
use craft\elements\Entry;
use Exception;

class CheckoutController extends Controller
{
    // checkout is build on magicline docs https://developer.magicline.com/apis/connectapi/workflow-contract/
    // 1. fetch studios
    // 2. fetch contracts
    // 3. post preview
    // 4. post create customer

    protected array|bool|int $allowAnonymous = self::ALLOW_ANONYMOUS_LIVE;

    private MagiclineApiService $apiService;

    // !!! - TODO DELETE FOR GO LIVE - !!!
    public function beforeAction($action): bool
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function __construct($id, $module, $config = [])
    {
        $this->apiService = Magicline::getInstance()->api;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        return $this->renderTemplate('checkout/index', []);
    }

    public function actionClubs(): Response
    {
        $jsonData = $this->apiService
            ->performConnectHttpRequest(
                MagiclineApiService::GET,
                '/connect/v2/studio'
            )
            ->getBody();

        $data = Json::decodeIfJson($jsonData);

        $baseUrl = Craft::$app->getRequest()->getHostInfo();
        $isBelgium = $baseUrl === 'https://www.jims.be';
        $isLuxumbourg = $baseUrl === 'https://www.jims.lu';
        if (App::env('ENVIRONMENT') !== 'production') {
            $isBelgium = true;
            $isLuxumbourg = true;
        }
        $result = [];

        // TODO DELETE AFTER ML CLUUBS ARE IMPORTED
        if ($isBelgium) {
            $clubs = Entry::find()
                ->section('mlClubs')
                ->all();

                foreach ($clubs as $club) {

                    $result[] = [
                        'id' => $club->mlClubId ?? 210004460,
                        'name' => $club->title,
                    ];
                }

        } else {

            foreach ($data as $item) {
                $showStudio = Craft::$app->config->general->devMode;
                $hasLiveTag = false;
                $hasLocationTag = false;

                foreach ($item['studioTags'] as $value) {
                    if ($value['identifier'] === Craft::$app->config->custom->magiclineStudioLiveCode) {
                        $hasLiveTag = true;
                    }

                    if ($isLuxumbourg &&
                        $value['identifier'] === Craft::$app->config->custom->magiclineStudioLuxembourg) {
                        $hasLocationTag = true;
                    }

                    if ($isBelgium &&
                        $value['identifier'] === Craft::$app->config->custom->magiclineStudioBelgium) {
                        $hasLocationTag = true;
                    }
                }

                // Check if the studio has both the live tag and location tag
                if ($hasLiveTag && $hasLocationTag) {
                    $showStudio = true;
                }

                if (!$showStudio) {
                    continue;
                }

                $result[] = [
                    'id' => $item['id'],
                    'name' => $item['studioName'],
                ];
            }
        }


        return $this->asJson($result);
    }

    public function actionContracts(int $clubId): Response
    {
        $jsonData = $this->apiService
            ->performConnectHttpRequest(
                MagiclineApiService::GET,
                "/connect/v1/rate-bundle?studioId=$clubId"
            )
            ->getBody();


        $data = Json::decodeIfJson($jsonData);

        // Return the JSON data as the response
        return $this->asJson($data);
    }

    public function actionPreview()
    {
        try {
            $this->requirePostRequest();
            $options['json'] = json_decode($this->request->getRawBody(), true);
            $jsonData = $this->apiService
                ->performConnectHttpRequest(
                    MagiclineApiService::POST,
                    '/connect/v1/preview',
                    $options

                )
                ->getBody();

            $data = Json::decodeIfJson($jsonData);

            // Return the JSON data as the response
            return $this->asJson($data);

        } catch (\Exception $e) {
            $file = Craft::getAlias('@storage/logs/preview-errors.log');
            \craft\helpers\FileHelper::writeToFile($file, date('Y-m-d H:i:s'). "\n", ['append' => true]);
            \craft\helpers\FileHelper::writeToFile($file, json_encode($options['json'], true), ['append' => true]);

            throw new Exception($e, $e->getCode());
        }
    }

    public function actionCreateContract()
    {
        try {
            $this->requirePostRequest();
            $options['json'] = json_decode($this->request->getRawBody(), true);

            $response = $this->apiService
                ->performConnectHttpRequest(
                    MagiclineApiService::POST,
                    '/connect/v1/rate-bundle',
                    $options
                );

            $jsonData = $response->getBody();

            $data = Json::decodeIfJson($jsonData);

            return $this->asJson($data);
        } catch (\Exception $e) {
            $file = Craft::getAlias('@storage/logs/checkout-errors.log');
            \craft\helpers\FileHelper::writeToFile($file, date('Y-m-d H:i:s'). "\n", ['append' => true]);
            \craft\helpers\FileHelper::writeToFile($file, json_encode($options['json'], true), ['append' => true]);

            throw new Exception($e, $e->getCode());
        }
    }

    public function actionPaymentMethods(int $clubId)
    {
        $languageAndCountryCode = explode('-', Craft::$app->language);
        $language = $languageAndCountryCode[0];
        $countryCode = $languageAndCountryCode[1];

        $jsonData = $this->apiService
            ->performConnectHttpRequest(
                MagiclineApiService::GET,
                "/connect/v2/creditcard/tokenization/payment-methods?studioId=$clubId&countryCode=$countryCode&locale=$language"
            )->getBody();

        $data = Json::decodeIfJson($jsonData);

        return $this->asJson($data);
    }

    public function actionInitiateTokenization()
    {
        $this->requirePostRequest();

            // "paymentMethod": "string",
            // "browserInfo": "string",
            // "studioId": 0,
            // "returnUrl": "string",
            // "origin": "string"

            $options['json'] = json_decode($this->request->getRawBody(), true);
            $jsonData = $this->apiService
            ->performConnectHttpRequest(
                MagiclineApiService::POST,
                "/connect/v2/creditcard/tokenization/initiate",
                $options
            )->getBody();

        $data = Json::decodeIfJson($jsonData);

        return $this->asJson($data);
    }

    public function actionCompleteTokenization(string $tokenizationReference)
    {
        $this->requirePostRequest();

        // "redirectResult": "string",
        // "threeDSResult": "string"

        $options['json'] = json_decode($this->request->getRawBody(), true);
        $jsonData = $this->apiService
            ->performConnectHttpRequest(
                MagiclineApiService::POST,
                "/connect/v1/creditcard/tokenization/$tokenizationReference/complete",
                $options
            )->getBody();

        $data = Json::decodeIfJson($jsonData);

        return $this->asJson($data);
    }

    public function actionStateTokenization(string $tokenizationReference)
    {
        $jsonData = $this->apiService
            ->performConnectHttpRequest(
                MagiclineApiService::GET,
                "/connect/v1/creditcard/tokenization/$tokenizationReference/state"
            )->getBody();

        $data = Json::decodeIfJson($jsonData);

        return $this->asJson($data);
    }

    public function actionValidateVoucher(int $clubId, string $contractVoucherCode)
    {
        try {
            $jsonData = $this->apiService
                ->performConnectHttpRequest(
                    MagiclineApiService::GET,
                    "/connect/v1/contractvoucher/$contractVoucherCode/validate?organizationUnitId=$clubId"
                )->getBody();

            $data = Json::decodeIfJson($jsonData);

            return $this->asJson($data);
        } catch (\Throwable $th) {
            return new Response(['statusCode' => 400]);
        }
    }

    public function actionValidateIban(string $iban)
    {
        try {
            $jsonData = $this->apiService
                ->performConnectHttpRequest(
                    MagiclineApiService::GET,
                    "/connect/v1/bankaccount?iban=$iban"
                )->getBody();

            $data = Json::decodeIfJson($jsonData);
            return $this->asJson($data);
        } catch (\Throwable $th) {
            return new Response(['statusCode' => 400]);
        }
    }
}
