<?php

namespace App\Service;

use App\Common\NameGenerator;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Cookie;

class UserAuthService
{
    public const AUTH_COOKIE_NAME = 'user_token';

    public function __construct(
        private UserRepository $users,
        private NameGenerator $nameGenerator,
    ) {}

    public function createNewUser(): User 
    {
        $user = new User();
        $user->setName($this->nameGenerator->getRandomUnoccupiedUsername());
        $user->setToken($this->createAuthToken());
        $this->users->save($user);

        return $user;
    }

    public function getAuthCookie(User $user): Cookie {
        return new Cookie(self::AUTH_COOKIE_NAME, $user->getToken(), time() + 3600*24*365, secure: true, raw: true);
    }

    private function createAuthToken(): string 
    {
        return substr(base64_encode(random_bytes(200)), 0, 255);
    }
}
