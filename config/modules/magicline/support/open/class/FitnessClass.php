<?php

namespace modules\magicline\support\open\class;

use modules\magicline\controllers\open\ClassController;
use modules\magicline\support\Base;
use stdClass;

class FitnessClass extends Base
{
    public int $mlClassId;
    public ?string $mlClassTitle;
    public ?string $mlClassType;
    public ?int $mlClassDuration;
    public ?string $mlClassCategory;
    public ?string $mlClassDescription;
    public ?string $mlClassImageUrl;
    public ?bool $mlClassBookable;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlClassId = $this->original->id;
        $this->mlClassTitle = $this->original->title;
        $this->mlClassType = $this->original->type;
        $this->mlClassDuration = $this->original->duration;
        $this->mlClassCategory = $this->original->category;
        $this->mlClassDescription = $this->original->description;
        $this->mlClassImageUrl = $this->original->imgUrl;
        $this->mlClassBookable = $this->original->bookable;

        // matrix blocks

        // nested entries


        return $this;
    }

    public static function fromClassResponse(string $response): array
    {
        $responseArray = json_decode($response);
        $mappedData = array_map(function ($class) {
            return new FitnessClass($class);
        }, $responseArray->result);

        return $mappedData;
    }
}
