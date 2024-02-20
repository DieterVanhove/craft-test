<?php

namespace modules\magicline\support\connect\trailSession;

use modules\magicline\support\Base;
use stdClass;

class TrailSession extends Base
{
    public string $mlSessionName;
    public ?string $mlSessionDescription;
    public ?bool $mlSessionBookingWithoutResourcesAllowed;
    public ?array $mlSessionSlots;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlSessionName = $this->original->name;
        $this->mlSessionDescription = $this->original->description;
        $this->mlSessionBookingWithoutResourcesAllowed = $this->original->bookingWithoutResourcesAllowed;
        $this->mlSessionSlots = array_map(function ($slot) {
            return new Slot($slot);
        }, $this->original->slots);

        return $this;
    }

    public static function fromTrailSessionsResponse(string $response): array
    {
        // TODO just object instead of array => only one sessions per club?
        $responseArray = json_decode($response);
        $mappedData = array_map(function ($trailSession) {
            return new TrailSession($trailSession);
        }, [$responseArray]);

        return $mappedData;
    }
}
