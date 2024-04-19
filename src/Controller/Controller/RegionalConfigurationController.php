<?php

namespace App\Controller\Controller;

use App\DataTransferObject\RegionalConfigurationDto;
use App\Form\Filter\RegionalConfigurationFilter;
use App\Service\RegionalConfigurationService\RegionalConfigurationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegionalConfigurationController extends AbstractController
{


    public function __construct(
        private readonly RegionalConfigurationService $regionalConfigurationService,
        private readonly RequestStack                 $requestStack
    )
    {
    }

    #[Route(path: '/regional-settings', name: 'regional_settings')]
    public function configure(Request $request): Response
    {
        $regionalConfigurationDto = new RegionalConfigurationDto();
        $regionalConfigurationFilter = $this->createForm(RegionalConfigurationFilter::class, $regionalConfigurationDto);

        $regionalConfigurationFilter->handleRequest($request);
        if ($regionalConfigurationFilter->isSubmitted() && $regionalConfigurationFilter->isValid()) {

            $this->regionalConfigurationService->configure(
                requestStack: $this->requestStack,
                regionalConfigurationDto: $regionalConfigurationDto
            );

            return $this->redirectToRoute('regional_settings');
        }

        return $this->render('regional/configuration.html.twig', [
            'regionalConfigurationFilter' => $regionalConfigurationFilter
        ]);
    }

}