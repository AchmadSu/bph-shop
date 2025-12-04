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
        $missing = checkArrayRequired($data, $required);

        if (!empty($missing)) {
            $errorResponse = [
                "status_code" => 400,
                "success" => false,
                "message" => "Missing required fields: " . implode(', ', $missing)
            ];
            return response()->json($errorResponse, $errorResponse['status_code']);
        }

        try {
            $cookie = $this->service->login($data);
            $user = $this->service->me();
            $response = successResponse("Login successfully", $user);
            return response()->json($response, $response['status_code'])
                ->withCookie($cookie);
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
