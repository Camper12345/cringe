<?php

namespace App\Controller;

use App\Common\NameGenerator;
use App\Entity\User;
use App\Repository\PageVisitRepository;
use App\Repository\UserRepository;
use App\Service\UserAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\LockedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserAuthService $authService,
    ) {}

    #[Route(methods: ['GET'], path: '/api/user', name: 'get_user', format: "json")]
    public function getUserRequest(): Response {
        $user = $this->getUser();

        if(!$user instanceof User) {
            throw new UnauthorizedHttpException('');
        }

        return $this->makeUserResponse($user);
    }

    #[Route(methods: ['POST'], path: '/api/user', name: 'create_user', format: "json")]
    public function registerRequest(Request $request, PageVisitRepository $visitRepository): Response {
        $visitId = $request->getPayload()->get('visit_id');

        if(empty($visitId)) {
            throw new AccessDeniedHttpException();
        }

        $visit = $visitRepository->findOneById($visitId);

        if(empty($visit)) {
            throw new AccessDeniedHttpException($visitId . ' = ' . serialize($visit));
        }

        /**
         * @var User|null
         */
        $user = $this->getUser();

        if(!empty($user)) {
            if(!empty($visit->getUser()) && $user->getId() !== $visit->getUser()->getId()) {
                throw new AccessDeniedHttpException();
            }
        } else {
            $user = $this->authService->createNewUser();
        }

        $visit->setUser($user);
        $visitRepository->save($visit);

        return $this->makeUserResponse($user);
    }

    #[Route(methods: ['POST'], path: '/api/user/rename', name: 'rename_user', format: "json")]
    public function renameRequest(Request $request, UserRepository $userRepository, NameGenerator $nameGenerator): Response {
        $name = $request->getPayload()->get('name');

        if(!is_string($name) || empty($name) || strlen($name) > 255 || urlencode($name) !== $name) {
            throw new BadRequestException('Name is invalid');
        }

        /**
         * @var User|null
         */
        $user = $this->getUser();

        if(!$user instanceof User) {
            throw new UnauthorizedHttpException('');
        }

        if($nameGenerator->isUsernameOccupied($name)) {
            throw new LockedHttpException('Name is already taken');
        }

        $user->setName($name);

        $userRepository->save($user);

        return $this->makeUserResponse($user);
    }

    private function makeUserResponse(User $user): Response {
        $response = $this->json($user->toApi());

        $response->headers->setCookie($this->authService->getAuthCookie($user));

        return $response;
    }
}
