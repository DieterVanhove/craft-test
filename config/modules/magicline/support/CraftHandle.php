<?php

namespace modules\magicline\support;

class CraftHandle
{
    // --- CONNECT API ---

    // CLUB
    public const HANDLE_CLUB = 'mlClubs';
    public const IDENTIFIER_CLUB = 'mlClubId';
    public const IDENTIFIER_CLUB_API = 'mlApiKey';
    public const HANDLE_OPENING_HOURS = 'mlClubOpeningHours';
    public const HANDLE_OPENING_HOURS_BLOCK = 'mlOpeningBlock';
    public const HANDLE_CLUB_TAGS = 'mlClubTags';
    public const IDENTIFIER_CLUB_TAGS = 'mlTagId';
    public const HANDLE_CLUB_CAMPAIGN = 'mlClubCampaign';
    public const HANDLE_CLUB_CONTRACTS = 'mlClubContract';
    public const HANDLE_CLUB_CLASSES = 'mlClubClasses';

    // CONTRACT
    public const HANDLE_CONTRACT = 'mlContracts';
    public const IDENTIFIER_CONTRACT = 'mlContractId';
    public const HANDLE_CONTRACT_IMAGE = 'mlContractImage';
    public const HANDLE_CONTRACT_IMAGE_URL = 'mlContractImageUrl';
    public const HANDLE_CONTRACT_TERM = 'mlContractTerms';
    public const IDENTIFIER_CONTRACT_TERM = 'mlTermId';
    public const MATRIX_BLOCKS_CONTRACT_TERM = [
        'mlTermPriceAdjustmentRules' => 'mlPriceBlock',
        'mlTermFlatFees' => 'mlFeeBlock',
        'mlTermOptionalModules' => 'mlOptionalBlock',
        'mlTermRateBonusPeriods' => 'mlBonusBlock',
    ];

    // CAMPAIGN
    public const HANDLE_CAMPAIGN = 'mlCampaigns';
    public const IDENTIFIER_CAMPAIGN = 'mlCampaignId';



    // --- OPEN API ---
    public const HANDLE_CLASS = 'mlClasses'; //lessons
    public const IDENTIFIER_CLASS = 'mlClassId';
    public const HANDLE_CLASS_IMAGE = 'mlClassImage';
    public const HANDLE_CLASS_IMAGE_URL = 'mlClassImageUrl';

    public const HANDLE_CLASS_SLOT = 'mlClassSlots';
    public const IDENTIFIER_CLASS_SLOT = 'mlClassSlotId';


    public const HANDLE_CLASS_LOCATION = 'mlClassSlotLocation';
    public const IDENTIFIER_CLASS_LOCATION = 'mlClassLocationId';

    public const HANDLE_CLASS_INSTRUCTOR = 'mlClassSlotInstructors';
    public const IDENTIFIER_CLASS_INSTRUCTOR = 'mlClassInstructorId';
}
