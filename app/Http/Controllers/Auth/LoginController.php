<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * @param AuthManager $authManager
     */
    public function __construct(private AuthManager $authManager)
    {
    }

    /**
     * @param Request $request
     * @return Response|JsonResponse
     */
    public function authenticate(Request $request): Response|JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $defaultGuard = $this->authManager->guard();
        if ($defaultGuard->attempt($credentials)) {
            // TODO: Api - Kill and replace with something better!
            $request->session()->regenerate();

            return new JsonResponse([
                'userId' => $defaultGuard->id(),
            ]);
        }

        return new JsonResponse([
            'message' => 'Invalid credentials',
        ], Response::HTTP_FORBIDDEN);
    }
}
