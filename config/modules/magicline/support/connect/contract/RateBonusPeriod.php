<?php

namespace modules\magicline\support\connect\contract;

use modules\magicline\support\Base;
use stdClass;

class RateBonusPeriod extends Base
{
    public string $mlBonusUnit;
    public string $mlBonusValue;
    public string $mlBonusStrategy;
    public string $mlBonusDisplay;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlBonusUnit = $this->original->termUnit;
        $this->mlBonusValue = $this->original->termValue;
        $this->mlBonusStrategy = $this->original->termStrategy;
        $this->mlBonusDisplay = $this->original->displaySeparately;

        return $this;
    }
}
