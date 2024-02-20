<?php

namespace modules\magicline\support\connect\contract;

use modules\magicline\support\Base;
use stdClass;

class Contract extends Base
{
    public string $mlContractId;
    public ?string $mlContractDescription;
    public ?string $mlContractName;
    public ?string $mlContractSubDescription;
    public ?string $mlContractPreuseType;
    public ?array $mlContractTerms;
    // public ?array $mlContractModules;
    public ?int $mlContractMaximumNumberOfSelectableModules;
    // public ?array $mlContractSelectableModules;
    // public ?array $mlContractRateCodeDto;
    // public ?array $mlContractTextBlocks;
    public ?string $mlContractImageUrl;
    public ?string $mlContractFootnote;
    public ?bool $mlContractInitialPaymentRequired;
    public ?string $mlContractOnlinePaymentType;
    // public ?array $mlContractAllowedPaymentChoices;
    // public ?object $mlContractLimitedOfferingPeriod;

    public ?int $siteId = null;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlContractId = $this->original->id;
        $this->mlContractDescription = $this->original->description;
        $this->mlContractName = $this->original->name;
        $this->mlContractSubDescription = $this->original->subDescription;
        $this->mlContractPreuseType = $this->original->preuseType;
        // $this->mlContractModules = $this->original->modules;
        $this->mlContractMaximumNumberOfSelectableModules = $this->original->maximumNumberOfSelectableModules;
        // $this->mlContractSelectableModules = $this->original->selectableModules;
        // $this->mlContractRateCodeDto = $this->original->rateCodeDto;
        // $this->mlContractTextBlocks = $this->original->contractTextBlocks;
        $this->mlContractImageUrl = $this->original->imageUrl;
        $this->mlContractFootnote = $this->original->footnote;
        $this->mlContractInitialPaymentRequired = $this->original->initialPaymentRequired;
        $this->mlContractOnlinePaymentType = $this->original->onlinePaymentType;
        // $this->mlContractAllowedPaymentChoices = $this->original->allowedPaymentChoices;
        // $this->mlContractLimitedOfferingPeriod = $this->original->limitedOfferingPeriod;

        // matrix blocks

        // nested entries
        $this->mlContractTerms = array_map(function ($term) {
            return new ContractTerm($term);
        }, $this->original->terms);

        return $this;
    }

    public static function fromContractResponse(string $response): array
    {
        $responseArray = json_decode($response);
        $mappedData = array_map(function ($contract) {
            return new Contract($contract);
        }, $responseArray);

        return $mappedData;
    }
}
