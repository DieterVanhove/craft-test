<?php

namespace modules\magicline\support\connect\club;

use modules\magicline\support\Base;
use stdClass;

class Club extends Base
{
    use AddressTrait;

    public int $mlClubId;
    public string $mlClubName;
    public ?string $mlClubPhone;
    public ?string $mlClubEmail;
    public ?bool $mlClubTrialSessionBookable;
    public ?array $mlClubTags;
    public ?int $mlClubMasterId;
    public ?string $mlClubOpeningDate;
    public ?string $mlClubClosingDate;
    public ?array $mlClubOpeningHours;
    public ?string $mlClubZoneId;
    public ?bool $mlClubHasRateBundles;
    public ?string $mlClubTrialSessionName;
    public ?string $mlClubCurrencyCode;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlClubId = $this->original->id;
        $this->mlClubName = $this->original->studioName;
        $this->mlClubPhone = $this->original->studioPhone;
        $this->mlClubEmail = $this->original->studioEmail;
        $this->mlClubTrialSessionBookable = $this->original->trialSessionBookable;
        $this->mlClubMasterId = $this->original->masterStudioId;
        $this->mlClubOpeningDate = $this->original->openingDate;
        $this->mlClubClosingDate = $this->original->closingDate;
        $this->mlClubZoneId = $this->original->zoneId;
        $this->mlClubHasRateBundles = $this->original->hasRateBundles;
        $this->mlClubTrialSessionName = $this->original->trialSessionName;
        $this->mlClubCurrencyCode = $this->original->currencyCode;

        $this->hydrateAddress($this->original->address);

        $this->mlClubTags = array_map(function ($tag) {
            return new ClubTag($tag);
        }, $this->original->studioTags);

        $this->mlClubOpeningHours = array_map(function ($day) {
            return new OpeningHours($day);
        }, $this->original->openingHours);

        return $this;
    }

    public static function fromClubResponse(string $response): array
    {
        $responseArray = json_decode($response);
        $mappedData = array_map(function ($club) {
            return new Club($club);
        }, $responseArray);

        return $mappedData;
    }
}
