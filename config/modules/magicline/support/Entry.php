<?php

namespace modules\magicline\support;

use modules\magicline\support\connect\club\Club;
use modules\magicline\support\connect\campaign\Campaign;
use modules\magicline\support\connect\contract\Contract;
use modules\magicline\support\open\class\FitnessClass;
use modules\magicline\support\connect\trailSession\TrailSession;
use modules\magicline\support\open\class\ClassLocation;
use modules\magicline\support\open\class\ClassSlot;
use stdClass;

class Entry extends Base
{
    public static function prepareFields(
        object $data,
        string $title,
        ?bool $enabledOnCreate = true,
        ?bool $updateTitleAndSlug = true,
        ?int $siteId = null
    ): stdClass {
        $fields = clone $data;
        $fields->settings = new stdClass();
        $fields->settings->title = $title;
        $fields->settings->slug = self::createSlug($title);
        $fields->settings->enabledOnCreate = $enabledOnCreate;
        $fields->settings->updateTitleAndSlug = $updateTitleAndSlug;
        if ($siteId) {
            $fields->settings->siteId = $siteId;
        }

        return $fields;
    }

    public static function fieldsFromClub(Club $club): Club
    {
        $fields = self::prepareFields($club, $club->original->studioName, false, false);

        // unset matrix
        unset($fields->mlClubOpeningHours);
        // unset entries
        unset($fields->mlClubTags);

        return $fields;
    }

    public static function collectionFieldsFromClubTags(array $tags): array
    {
        $fields = array_map(function ($tag) {
            return self::prepareFields($tag, $tag->original->name);
        }, $tags);

        return $fields;
    }

    public static function fieldsFromContract(Contract $contract): Contract
    {
        $fields = self::prepareFields($contract, $contract->original->name, true, true, $contract->siteId);
        // unset entries
        unset($fields->mlContractTerms);

        // unset language
        unset($fields->siteId);

        return $fields;
    }

    public static function collectionFieldsFromContractTerms(array $terms): array
    {
        $collectionFields = array_map(function ($tag) {
            $fields = self::prepareFields($tag, $tag->original->id);

            // unset matrix
            unset($fields->mlTermPriceAdjustmentRules);
            unset($fields->mlTermFlatFees);
            unset($fields->mlTermRateBonusPeriods);
            unset($fields->mlTermOptionalModules);

            return $fields;
        }, $terms);

        return $collectionFields;
    }

    public static function fieldsFromTrailSessions($session): TrailSession
    {
        $fields = self::prepareFields($session, $session->original->name);

        // unset matrix
        unset($fields->mlSessionSlots);

        return $fields;
    }

    public static function fieldsFromClass(FitnessClass $fitnessClass): FitnessClass
    {
        $fields = self::prepareFields($fitnessClass, $fitnessClass->original->title, true);

        // unset entries
        unset($fields->mlClassSlots);

        return $fields;
    }

    public static function fieldsFromClassSlot(ClassSlot $classSlot): ClassSlot
    {
        $fields = self::prepareFields($classSlot, $classSlot->original->id);

        // unset entries
        unset($fields->mlClassSlotLocation);
        unset($fields->mlClassSlotInstructors);

        return $fields;
    }

    public static function collectionFieldsFromClassLocation(ClassLocation $location): array
    {
        $fields = self::prepareFields($location, $location->original->id);

        return [$fields];
    }

    public static function collectionFieldsFromClassInstructor(array $instructors): array
    {
        $collectionFields = array_map(function ($instructor) {
            $fields = self::prepareFields($instructor, $instructor->original->firstName . ' - ' . $instructor->original->lastName);

            return $fields;
        }, $instructors);

        return $collectionFields;
    }
}
