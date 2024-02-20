<?php

namespace modules\magicline\support\connect\contract;

use modules\magicline\support\Base;
use stdClass;

class OptionalModules extends Base
{
    public int $mlOptionalId;
    public string $mlOptionalName;
    public ?string $mlOptionalDescription;
    public ?int $mlOptionalPaymentFrequencyValue;
    public ?string $mlOptionalPaymentFrequencyUnit;
    public string $mlOptionalPaymentFrequencyType;
    public int $mlOptionalPrice;
    public ?string $mlOptionalImageUrl;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlOptionalId = $this->original->id;
        $this->mlOptionalName = $this->original->name;
        $this->mlOptionalDescription = $this->original->description;
        $this->mlOptionalPaymentFrequencyValue = $this->original->paymentFrequencyValue;
        $this->mlOptionalPaymentFrequencyUnit = $this->original->paymentFrequencyUnit;
        $this->mlOptionalPaymentFrequencyType = $this->original->paymentFrequencyType;
        $this->mlOptionalPrice = $this->original->price;
        $this->mlOptionalImageUrl = $this->original->imageUrl;


        return $this;
    }
}
