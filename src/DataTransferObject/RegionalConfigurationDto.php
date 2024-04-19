<?php

namespace App\DataTransferObject;

final class RegionalConfigurationDto
{
    private null|string $timezone = null;
    private null|string $currency = null;
    private null|string $locale = null;

    /**
     * @param string|null $timezone
     * @param string|null $currency
     * @param string|null $locale
     */
    public function __construct(
        null|string $timezone = null,
        null|string $currency = null,
        null|string $locale = null
    )

    {
        $this->timezone = $timezone;
        $this->currency = $currency;
        $this->locale = $locale;
    }


    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

}