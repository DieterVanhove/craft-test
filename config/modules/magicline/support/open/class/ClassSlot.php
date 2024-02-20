<?php

namespace modules\magicline\support\open\class;

use modules\magicline\support\Base;
use stdClass;

class ClassSlot extends Base
{
    public int $mlClassSlotId;
    // Id of club to filter the slots related to that club because class is the same id over different clubs
    public int $mlClassSlotClubId;
    public ?string $mlClassSlotStartDateTime;
    public ?string $mlClassSlotEndDateTime;
    public ?array $mlClassSlotInstructors;
    public ?ClassLocation $mlClassSlotLocation;
    public ?string $mlClassSlotEarliestBookingDateTime;
    public ?string $mlClassSlotLatestBookingDateTime;
    public ?int $mlClassSlotMaxParticipants;
    public ?int $mlClassSlotMaxWaitingListParticipants;
    public ?int $mlClassSlotBookedParticipants;
    public ?int $mlClassSlotWaitingListParticipants;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlClassSlotId = $this->original->id;
        $this->mlClassSlotClubId = $this->original->clubId;
        $this->mlClassSlotStartDateTime = $this->original->startDateTime;
        $this->mlClassSlotEndDateTime = $this->original->endDateTime;
        $this->mlClassSlotEarliestBookingDateTime = $this->original->earliestBookingDateTime;
        $this->mlClassSlotLatestBookingDateTime = $this->original->latestBookingDateTime;
        $this->mlClassSlotMaxParticipants = $this->original->maxParticipants;
        $this->mlClassSlotMaxWaitingListParticipants = $this->original->maxWaitingListParticipants;
        $this->mlClassSlotBookedParticipants = $this->original->bookedParticipants;
        $this->mlClassSlotWaitingListParticipants = $this->original->waitingListParticipants;

        // matrix blocks

        // nested entries
        if ($this->original->instructors) {
            $this->mlClassSlotInstructors = array_map(function ($instructor) {
                return new ClassInstructor($instructor);
            }, $this->original->instructors);
        }

        if ($this->original->location) {
            $this->mlClassSlotLocation = new ClassLocation($this->original->location);
        } else {
            $this->mlClassSlotLocation = null;
        }

        return $this;
    }

    public static function fromClassSlotResponse(string $response, int $clubId): array
    {
        $responseArray = json_decode($response);

        $mappedData = array_map(function ($classSlot) use ($clubId) {
            $classSlot->clubId = $clubId;
            return new ClassSlot($classSlot);
        }, $responseArray->result);

        return $mappedData;
    }
}
