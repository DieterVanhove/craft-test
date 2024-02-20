<?php

namespace modules\magicline\support;

use stdClass;
use craft\helpers\StringHelper;

abstract class Base extends stdClass
{
    public function __construct(
        protected stdClass $original
    ) {}

    protected static function createTitle(array $fields)
    {
        return implode(' - ', $fields);
    }

    protected static function createSlug(string $string)
    {
        return StringHelper::slugify($string);
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this), true);
    }
}
