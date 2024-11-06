<?php

namespace App\Controller;

use App\Entity\PageVisit;
use App\Entity\User;
use App\Repository\PageVisitRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route(methods: ['GET'], path: '/', name: 'index')]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route(methods: ['GET'], path: '/api/visits', name: 'visit_list', format: "json")]
    public function getVisitList(PageVisitRepository $visits): Response
    {
        return $this->json([
            'count' => $visits->getVisitCount('/'),
            'visits' => array_map(function(PageVisit $visit){return $visit->toApi();}, $visits->findRecent(20, '/')),
        ]);
    }
}
