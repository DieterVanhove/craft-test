<?php

namespace modules\magicline\support\open\class;

use modules\magicline\support\Base;
use stdClass;

class ClassLocation extends Base
{
    public int $mlClassLocationId;
    public string $mlClassLocationName;
    public ?string $mlClassLocationDescrpition;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlClassLocationId = $this->original->id;
        $this->mlClassLocationName = $this->original->name;
        $this->mlClassLocationDescrpition = $this->original->description;

        // matrix blocks

        // nested entries

        return $this;
    }

    public static function fromInstructorResponse(string $response): array
    {
        $responseArray = json_decode($response);
        $mappedData = array_map(function ($location) {
            return new ClassLocation($location);
        }, $responseArray);

        return $mappedData;
    }
}
