<?php

namespace modules\magicline\support\connect\club;

trait AddressTrait
{
    public ?string $mlAddressStreet;
    public ?string $mlAddressSecondStreet;
    public ?string $mlAddressCityPart;
    public ?string $mlAddressDistrict;
    public ?string $mlAddressCity;
    public ?string $mlAddressZipCode;
    public ?string $mlAddressStreetAddition;
    public ?string $mlAddressHouseNumber;
    public ?string $mlAddressBuildingName;
    public ?string $mlAddressCountryCode;
    public ?string $mlAddressCountryCodeAlpha2;
    public ?float  $mlAddressLongitude;
    public ?float  $mlAddressLatitude;
    public ?string $mlAddressStreetType;
    public ?string $mlAddressStreetBlock;
    public ?string $mlAddressPortal;
    public ?string $mlAddressStairway;
    public ?string $mlAddressDoor;
    public ?string $mlAddressFloor;
    public ?string $mlAddressProvince;
    public ?string $mlAddressAdditionalInformation;
    public ?array $commonGoogleAddress;

    protected function hydrateAddress($original): self
    {
        $this->mlAddressStreet = $original->street;
        $this->mlAddressSecondStreet = $original->secondStreet;
        $this->mlAddressCityPart = $original->cityPart;
        $this->mlAddressDistrict = $original->district;
        $this->mlAddressCity = $original->city;
        $this->mlAddressZipCode = $original->zipCode;
        $this->mlAddressStreetAddition = $original->streetAddition;
        $this->mlAddressHouseNumber = $original->houseNumber;
        $this->mlAddressBuildingName = $original->buildingName;
        $this->mlAddressCountryCode = $original->countryCode;
        $this->mlAddressCountryCodeAlpha2 = $original->countryCodeAlpha2;
        $this->mlAddressLongitude = $original->longitude;
        $this->mlAddressLatitude = $original->latitude;
        $this->mlAddressStreetType = $original->streetType;
        $this->mlAddressStreetBlock = $original->streetBlock;
        $this->mlAddressPortal = $original->portal;
        $this->mlAddressStairway = $original->stairway;
        $this->mlAddressDoor = $original->door;
        $this->mlAddressFloor = $original->floor;
        $this->mlAddressProvince = $original->province;
        $this->mlAddressAdditionalInformation = $original->additionalAddressInformation;
        $this->commonGoogleAddress = [
            'lat' => $this->mlAddressLatitude,
            'lng' => $this->mlAddressLongitude,
        ];
        return $this;
    }
}
