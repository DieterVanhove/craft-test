<?php

namespace modules\magicline\support\connect\campaign;

use modules\magicline\support\Base;
use modules\magicline\support\Entry;
use stdClass;

class Campaign extends Base
{
    public int $mlCampaignId;
    public string $mlCampaignName;
    public string $mlCampaignExternalIdentifier;


    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlCampaignId = $this->original->id;
        $this->mlCampaignName = $this->original->name;
        $this->mlCampaignExternalIdentifier = $this->original->externalIdentifier;

        return $this;
    }

    public static function fromClubResponse(string $response): array
    {
        $responseArray = json_decode($response);
        $mappedData = array_map(function ($club) {
            return new Campaign($club);
        }, $responseArray);

        return $mappedData;
    }

    public static function fieldsFromCampaign(Campaign $campaign): Campaign
    {
        $fields = Entry::prepareFields($campaign, $campaign->original->name);

        return $fields;
    }
}
