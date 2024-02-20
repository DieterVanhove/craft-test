<?php
/**
 * Magicline module for Craft CMS 4.x
 *
 * Jims CRM connection
 *
 * @link      esign.eu
 * @copyright Copyright (c) 2022 dieter vanhove
 */

namespace modules\magicline\variables;

use modules\magicline\Magicline;

use Craft;
use craft\helpers\StringHelper;
use modules\magicline\services\MagiclineApiService;
use modules\magicline\support\connect\contract\Contract;
use modules\magicline\support\connect\contract\ContractTerm;
use Psr\Http\Message\StreamInterface;

/**
 * @author    dieter vanhove
 * @package   MagiclineModule
 * @since     1.0.0
 */
class MagiclineVariable
{
    private MagiclineApiService $apiService;

    private const HQ_LUXEMBOURG = 1210017980;
    private const HQ_BELGIUM = 1210004460;
    private const BELGIUM_GROUP_ID = 1;
    // Deze keys hebben we zo afgeproken om te gebruiken met jims
    private const DEFAULT_CONTRACTS = [
        'YOU PUBLIC',
        'WE PUBLIC',
        'WE+ PUBLIC',
    ];
    private const PREUSE_CONTRACTS = [
        'YOU PREUSE',
        'WE PREUSE',
        'WE+ PREUSE',
        'YOU FREE PREUSE',
        'WE FREE PREUSE',
        'WE+ FREE PREUSE',
    ];
    private const CORPORATE_CONTRACTS = [
        'YOU CORPORATE',
        'WE CORPORATE',
        'WE+ CORPORATE',
    ];

    private const ALL_CONTRACT_KEYS = [
        'YOU PUBLIC' => 'you',
        'WE PUBLIC' => 'we',
        'WE+ PUBLIC' => 'we-plus',

        'YOU PREUSE' => 'you',
        'WE PREUSE' => 'we',
        'WE+ PREUSE' => 'we-plus',
        'YOU FREE PREUSE' => 'you',
        'WE FREE PREUSE' => 'we',
        'WE+ FREE PREUSE' => 'we-plus',

        'YOU CORPORATE' => 'you',
        'WE CORPORATE' => 'we',
        'WE+ CORPORATE' => 'we-plus',
    ];


    public function __construct()
    {
        $this->apiService = Magicline::getInstance()->api;
    }

    public function fetchStudioContracts(string $clubId): StreamInterface
    {
        $response = $this->apiService->performConnectHttpRequest(MagiclineApiService::GET, "/connect/v1/rate-bundle?studioId=$clubId");

        return $response->getBody();
    }

    public function getAllContracts($clubId)
    {
        return Contract::fromContractResponse($this->fetchStudioContracts($clubId));
    }

    public function getGroupedContracts(string $clubId = null)
    {
        if (is_null($clubId) || empty($clubId)) {
            $currentSite = Craft::$app->getSites()->getCurrentSite();
            $clubId = $currentSite->groupId === self::BELGIUM_GROUP_ID ? self::HQ_BELGIUM : self::HQ_LUXEMBOURG;
        }

        $magiclineContracts = Contract::fromContractResponse($this->fetchStudioContracts($clubId));

        return $this->groupContractsByType($magiclineContracts);
    }

    private function groupContractsByType($contracts)
    {
        $groupedContracts = [];

        foreach ($contracts as $contract) {
            $contract->allTerms = $this->groupTermsByType($contract->mlContractTerms);
            $type = $this->determineContractType($contract->mlContractSubDescription);
            if (in_array($type, self::DEFAULT_CONTRACTS) || in_array($type, self::PREUSE_CONTRACTS)) {

                $groupedContracts['default'][self::ALL_CONTRACT_KEYS[$type]] = $contract;
            } elseif (in_array($type, self::CORPORATE_CONTRACTS)) {
                $groupedContracts['corporate'][self::ALL_CONTRACT_KEYS[$type]] = $contract;
            } else {

                $this->sendEmail($contract);
                $groupedContracts['other'][] = $contract;
            }
        }


        // Sort contracts within the "default" group if it is set
        if (isset($groupedContracts['default'])) {
            $this->sortContractsInGroup($groupedContracts['default']);
        }

        // Sort contracts within the "corporate" group if it is set
        if (isset($groupedContracts['corporate'])) {
            $this->sortContractsInGroup($groupedContracts['corporate']);
        }

        return $groupedContracts;
    }

    private function sortContractsInGroup(&$group)
    {
        if (!$group) {
            return;
        }
        // Define the desired order
        $desiredOrder = ['you', 'we', 'we-plus'];

        // User-defined comparison function
        $comparisonFunction = function ($a, $b) use ($desiredOrder) {
            $posA = array_search($a, $desiredOrder);
            $posB = array_search($b, $desiredOrder);

            return $posA - $posB;
        };

        // Sort the contracts using uksort
        uksort($group, $comparisonFunction);
    }

    private function sendEmail($contract)
    {
        Craft::$app
            ->getMailer()
            ->compose()
            ->setTo('dieter.vanhove@esign.eu')
            ->setSubject('Jims - Unknown contract type')
            ->setTextBody(print_r($contract, true))
            ->send();
    }

    private function determineContractType($contractName)
    {
        $allContractTypes = array_merge(
            self::DEFAULT_CONTRACTS,
            self::PREUSE_CONTRACTS,
            self::CORPORATE_CONTRACTS
        );
        foreach ($allContractTypes as $defaultContract) {
            if (StringHelper::startsWith(strtoupper($contractName), $defaultContract)) {
                return $defaultContract;
            }
        }

        return 'other';
    }

    private function groupTermsByType($terms)
    {
        $sortedTerms = [];

        foreach ($terms as $term) {
            // check if term is mothly or yearly
            if ($term->mlTermExtensionType === 'TERM_EXTENSION') {
                switch ($term->mlTermValue) {
                    case 4:
                        $sortedTerms['recurring'][] = $term;
                        break;
                    case 52:
                        $sortedTerms['recurring'][] = $term;
                        break;
                }
            }

            // check if term is fixed
            if ($term->mlTermExtensionType === 'NONE') {
                switch ($term->mlTermValue) {
                    case 1:
                        $sortedTerms['fixed'][] = $term;
                        break;
                    case 6:
                        $sortedTerms['fixed'][] = $term;
                        break;
                    case 12:
                        $sortedTerms['fixed'][] = $term;
                        break;
                }
            }
        }

        return $sortedTerms;
    }


    public function getCurrentContract(array $contracts, bool $isCorporate, string $subscription, bool $fixed): ?Contract
    {
        $contractType = $isCorporate ? 'corporate' : 'default';
        $contract = $contracts[$contractType][$subscription];

        $availableTerms = $contract->allTerms[$fixed ? 'fixed' : 'recurring'];

        if ($fixed) {
            usort($availableTerms, function ($a, $b) {
                return $a->mlTermValue <=> $b->mlTermValue;
            });
        } else {
            usort($availableTerms, function ($a, $b) {
                return $b->mlTermValue <=> $a->mlTermValue;
            });
        }

        $contract->availableTerms = $availableTerms;

        return $contract;
    }

    public function getCurrentContractTerm(array $terms, string $termId): ?ContractTerm
    {
        foreach ($terms as $term) {
            if ($term->mlTermId === (int) $termId) {
                return $term;
            }
        }

        return $terms[0];
    }
}
