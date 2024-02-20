<?php

namespace modules\magicline\support\connect\trailSession;

use modules\magicline\support\Base;
use stdClass;

class Slot extends Base
{
    public string $mlSlotStartDateTime;
    public string $mlSlotEndDateTime;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlSlotStartDateTime = $this->original->startDateTime;
        $this->mlSlotEndDateTime = $this->original->endDateTime;

        return $this;
    }
}
