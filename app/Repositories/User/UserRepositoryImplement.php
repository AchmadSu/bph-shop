<?php

namespace App\Repositories\User;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserRepositoryImplement extends Eloquent implements UserRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $model;
    private $role;

    public function __construct(User $model)
    {
        $this->model = $model;
        $this->role = app(Role::class);
    }

    public function login(array $credentials)
    {
        try {
            if (!$token = auth()->attempt($credentials)) {
                throw new Exception("Invalid email or password", 401);
            }
            return $token;
        } catch (\Exception $e) {
            throw $e;
        }
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

    public function register($data)
    {
        $array = [
            'name' => $data['name'],
            'email' => $data['email'],
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt($data['password']),
            'status' => '1'
        ];
        try {
            return DB::transaction(function () use ($array) {
                $user = $this->model->create($array);
                if (!$user || empty($user->id)) {
                    throw new Exception("User creation failed", 500);
                }
                $role = $this->role->where('name', 'user')->first();
                if (!$role) {
                    throw new Exception("Role 'user' does not exist", 500);
                }
                $user->assignRole('user');

                return successResponse("User has been registered");
            });
        } catch (Exception $e) {
            return errorResponse($e);
        }
    }
}
