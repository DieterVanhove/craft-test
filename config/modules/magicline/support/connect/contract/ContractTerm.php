<?php

namespace modules\magicline\support\connect\contract;

use modules\magicline\support\Base;
use stdClass;

class ContractTerm extends Base
{
    public int $mlTermId;
    public string $mlTermDefaultContractStartDate;
    public string $mlTermContractStartDateOfUse;
    public int $mlTermValue;
    public int $mlTermValueWithoutBonusPeriod;
    public string $mlTermUnit;
    public int $mlTermPaymentFrequencyValue;
    public string $mlTermPaymentFrequencyUnit;
    public string $mlTermPaymentFrequencyType;
    public float $mlTermPrice;
    public ?int $mlTermExtensionFixed;
    public ?string $mlTermExtensionFixedUnit;
    public ?string $mlTermExtensionType;
    public ?int $mlTermCancellationPeriod;
    public ?string $mlTermCancellationPeriodUnit;
    public ?int $mlTermExtensionCancellationPeriod;
    public ?string $mlTermExtensionCancellationPeriodUnit;
    public string $mlTermRateStartPrice;
    public array $mlTermPriceAdjustmentRules;
    public array $mlTermFlatFees;
    public array $mlTermRateBonusPeriods;
    public array $mlTermOptionalModules;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlTermId = $this->original->id;
        $this->mlTermDefaultContractStartDate = $this->original->defaultContractStartDate;
        $this->mlTermContractStartDateOfUse = $this->original->contractStartDateOfUse;
        $this->mlTermValue = $this->original->termValue;
        $this->mlTermValueWithoutBonusPeriod = $this->original->termValueWithoutBonusPeriod;
        $this->mlTermUnit = $this->original->termUnit;
        $this->mlTermPaymentFrequencyValue = $this->original->paymentFrequencyValue;
        $this->mlTermPaymentFrequencyUnit = $this->original->paymentFrequencyUnit;
        $this->mlTermPaymentFrequencyType = $this->original->paymentFrequencyType;
        $this->mlTermPrice = $this->original->price;
        $this->mlTermExtensionFixed = $this->original->extensionFixedTerm;
        $this->mlTermExtensionFixedUnit = $this->original->extensionFixedTermUnit;
        $this->mlTermExtensionType = $this->original->extensionType;
        $this->mlTermCancellationPeriod = $this->original->cancellationPeriod;
        $this->mlTermCancellationPeriodUnit = $this->original->cancellationPeriodUnit;
        $this->mlTermExtensionCancellationPeriod = $this->original->extensionCancellationPeriod;
        $this->mlTermExtensionCancellationPeriodUnit = $this->original->extensionCancellationPeriodUnit;
        $this->mlTermRateStartPrice = $this->original->rateStartPrice;

        // matrix blocks
        $this->mlTermPriceAdjustmentRules = array_map(function ($rule) {
            return new PriceAdjustmentRule($rule);
        }, $this->original->priceAdjustmentRules);

        $this->mlTermFlatFees = array_map(function ($rule) {
            return new FlatFee($rule);
        }, $this->original->flatFees);

        $this->mlTermRateBonusPeriods = array_map(function ($bonus) {
            return new RateBonusPeriod($bonus);
        }, $this->original->rateBonusPeriods);

        $this->mlTermOptionalModules = array_map(function ($bonus) {
            return new OptionalModules($bonus);
        }, $this->original->optionalModules);

        return $this;
    }
}
