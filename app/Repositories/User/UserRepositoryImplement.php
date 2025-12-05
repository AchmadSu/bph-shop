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
}
