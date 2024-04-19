<?php

namespace App\Service\RegionalConfigurationService;

use App\DataTransferObject\RegionalConfigurationDto;
use Symfony\Component\HttpFoundation\RequestStack;

final class RegionalConfigurationService
{

    public function configure(
        RequestStack $requestStack,
        RegionalConfigurationDto $regionalConfigurationDto
    ) : void
    {
        $session = $requestStack->getSession();

        $session->set('timezone', $regionalConfigurationDto->getTimezone());
        $session->set('currency', $regionalConfigurationDto->getCurrency());
        $session->set('_locale', $regionalConfigurationDto->getLocale());

    }

}