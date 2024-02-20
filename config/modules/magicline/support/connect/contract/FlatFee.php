<?php

namespace modules\magicline\support\connect\contract;

use modules\magicline\support\Base;
use stdClass;

class FlatFee extends Base
{
    public string $mlFeeName;
    public ?string $mlFeeIdentifier;
    public ?string $mlFeeCalculation;
    public ?int $mlFeeFrequencyValue;
    public ?string $mlFeeFrequencyUnit;
    public ?string $mlFeeFrequencyType;
    public ?int $mlFeePrice;
    public ?string $mlFeeFrequency;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlFeeName = $this->original->name;
        $this->mlFeeIdentifier = $this->original->identifier;
        $this->mlFeeCalculation = $this->original->paidTimePeriodCalculationType;
        $this->mlFeeFrequencyValue = $this->original->paymentFrequencyValue;
        $this->mlFeeFrequencyUnit = $this->original->paymentFrequencyUnit;
        $this->mlFeeFrequencyType = $this->original->paymentFrequencyType;
        $this->mlFeePrice = $this->original->price;
        $this->mlFeeFrequency = $this->original->paymentFrequency;

        return $this;
    }
}
