<?php

namespace App\Http\Controllers;

use App\Services\User\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    private $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function login(Request $request)
    {
        $data = $request->all();
        $required = ['email', 'password'];
        $errorResponse = checkArrayRequired($data, $required);
        if ($errorResponse) return $errorResponse;

        try {
            $cookie = $this->service->login($data);
            $response = successResponse("Login successfully");
            return response()->json($response, $response['status_code'])
                ->withCookie($cookie);
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function me()
    {
        try {
            $me = $this->service->me();
            return response()->json(successResponse("Get my personal data successfully", $me));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function logout()
    {
        try {
            $response = $this->service->logout();
        } catch (\Exception $e) {
            $response = errorResponse($e);
        }
        return response()->json($response, $response['status_code']);
    }
}
