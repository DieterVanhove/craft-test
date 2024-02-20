<?php

namespace modules\magicline\support\connect\club;

use modules\magicline\support\Base;
use stdClass;

class OpeningHours extends Base
{
    public string $mlOpeningDayFrom;
    public string $mlOpeningDayTo;
    public string $mlOpeningTimeFrom;
    public string $mlOpeningTimeTo;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlOpeningDayFrom = $this->original->dayOfWeekFrom;
        $this->mlOpeningDayTo = $this->original->dayOfWeekTo;
        $this->mlOpeningTimeFrom = $this->original->timeFrom;
        $this->mlOpeningTimeTo = $this->original->timeTo;

        return $this;
    }
}
