<?php

namespace App\Services\User;

use LaravelEasyRepository\Service;
use App\Repositories\User\UserRepository;
use Exception;
use Illuminate\Auth\Events\Logout;
use Symfony\Component\HttpFoundation\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserServiceImplement extends Service implements UserService
{

  /**
   * don't change $this->mainRepository variable name
   * because used in extends service class
   */
  protected $mainRepository;

  public function __construct(UserRepository $mainRepository)
  {
    $this->mainRepository = $mainRepository;
  }

  public function login($data)
  {
    $credentials = [
      'email' => $data['email'],
      'password' => $data['password']
    ];

    try {
      $token = $this->mainRepository->login($credentials);
      $cookie = cookie(
        name: 'access_token',
        value: $token,
        minutes: 60 * 24,
        path: '/',
        domain: null,
        httpOnly: true,
        secure: false,
        sameSite: 'Lax'
      );
      return $cookie;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function me()
  {
    $user = auth()->user()->load(['roles']);
    $data = [
      'id' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'role' => $user->roles->pluck('name')->first()
    ];
    return $data;
  }

  public function logout()
  {
    try {
      $token = request()->cookie('access_token');
      if (!$token) {
        throw new Exception("Token not found", 400);
      }
      JWTAuth::setToken($token)->invalidate();
      cookie()->forget('access_token', '/', null, true, true, false, 'Strict');
      return successResponse("Logout successfully!");
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function refresh()
  {
    return successResponse("Token refreshed", ['access_token' => auth()->refresh]);
  }
}
