<?php

namespace modules\magicline\support\open\class;

use modules\magicline\support\Base;
use stdClass;

class ClassInstructor extends Base
{
    public int $mlClassInstructorId;
    public string $mlClassInstructorFirstName;
    public string $mlClassInstructorLastName;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlClassInstructorId = $this->original->id;
        $this->mlClassInstructorFirstName = $this->original->firstName;
        $this->mlClassInstructorLastName = $this->original->lastName;

        // matrix blocks

        // nested entries

        return $this;
    }

    public static function fromInstructorResponse(string $response): array
    {
        $responseArray = json_decode($response);
        $mappedData = array_map(function ($instructor) {
            return new ClassInstructor($instructor);
        }, $responseArray);

        return $mappedData;
    }
}
