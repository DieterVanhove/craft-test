<?php

namespace modules\magicline\support\connect\club;

use modules\magicline\support\Base;
use craft\helpers\StringHelper;
use stdClass;

class ClubTag extends Base
{
    public string $mlTagId;
    public string $mlTagName;

    public function __construct(stdClass $original)
    {
        parent::__construct($original);

        $this->hydrate();
    }

    protected function hydrate(): self
    {
        $this->mlTagId = $this->original->identifier;
        $this->mlTagName = $this->original->name;

        return $this;
    }
}
