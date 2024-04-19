<?php

namespace App\Event\Subscriber;


use App\DataTransferObject\RegionalConfigurationDto;
use App\Service\RegionalConfigurationService\RegionalConfigurationService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: RequestEvent::class,method: 'handleTimezone')]
readonly class RegionalConfigurationSubscriber
{

    public function __construct(
        private RegionalConfigurationService $regionalConfigurationService,
        private RequestStack                 $requestStack
    )
    {
    }

    public function handleTimezone(RequestEvent $event): void
    {
        $session = $event->getRequest()->getSession();

        $timezone = $session->get('timezone');
        $currency = $session->get('currency');
        $locale = $session->get('_locale');

        $regionalConfigurationDto = new RegionalConfigurationDto(
            timezone: $timezone,
            currency: $currency,
            locale: $locale
        );

        $this->regionalConfigurationService->configure(
            requestStack: $this->requestStack,
            regionalConfigurationDto: $regionalConfigurationDto
        );
    }


}