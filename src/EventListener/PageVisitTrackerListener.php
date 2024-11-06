<?php

namespace App\EventListener;

use App\Entity\PageVisit;
use App\Entity\User;
use App\Repository\PageVisitRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class PageVisitTrackerListener
{
    public function __construct(
        private Security $security,
        private PageVisitRepository $visits,
    ) {}

    public function __invoke(ResponseEvent $event): void {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if(!$response->isSuccessful() || ($response instanceof JsonResponse)) {
            return;
        }

        /**
         * @var User|null
         */
        $user = $this->security->getUser();

        $visit = new PageVisit();
        $visit->setDate(new DateTimeImmutable());

        $visit->setPath($request->getPathInfo());

        if(!empty($user)) {
            $visit->setUser($user);
        }

        $this->visits->save($visit);

        $response->headers->setCookie(new Cookie('visit_id', $visit->getId(), time() + 120, httpOnly: false));
    }
}
