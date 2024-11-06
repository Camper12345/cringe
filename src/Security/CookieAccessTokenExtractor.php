<?php

namespace App\Security;

use App\Service\UserAuthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\AccessToken\AccessTokenExtractorInterface;

class CookieAccessTokenExtractor implements AccessTokenExtractorInterface
{
    public function extractAccessToken(Request $request): ?string {
        $token = $request->cookies->get(UserAuthService::AUTH_COOKIE_NAME);

        if(is_string($token)) {
            return $token;
        }

        return null;
    }
}
