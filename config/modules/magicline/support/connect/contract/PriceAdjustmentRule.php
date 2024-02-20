<?php

namespace modules\magicline\support\connect\contract;

use modules\magicline\support\Base;
use stdClass;

class PriceAdjustmentRule extends Base
{
    public string $mlPriceDescription;
    public string $mlPriceValue;
    public string $mlPriceRecurrence;
    public string $mlPriceType;
    public string $mlPriceAdjustment;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlPriceDescription = $this->original->defaultDescription;
        $this->mlPriceValue = $this->original->value;
        $this->mlPriceRecurrence = $this->original->recurrenceFrequency;
        $this->mlPriceType = $this->original->type;
        $this->mlPriceAdjustment = $this->original->chargeAdjustmentType;

        return $this;
    }
}
