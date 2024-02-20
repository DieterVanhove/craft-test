<?php

namespace modules\magicline\support\connect\trailSession;

use Carbon\Carbon;

class SessionDates
{
    public function __construct(
        public string $startDate,
        public string $endDate,
    ) {
    }

    public static function getSessionsForAMonth($startingDate = null): static
    {
        if (is_null($startingDate)) {
            $date = Carbon::now();
        }
        // magicline validation acts weird get one month sub 1 day because looks like thats the max date range
        $startDate = $date->format('Y-m-d');
        $endDate = $date->addMonth()->subDay()->format('Y-m-d');

        return new static(
            $startDate,
            $endDate
        );
    }

}
