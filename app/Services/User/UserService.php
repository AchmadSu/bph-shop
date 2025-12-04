<?php

namespace App\Services\User;

use LaravelEasyRepository\BaseService;

interface UserService extends BaseService
{

    public function login($data);
    public function me();
    public function logout();
    public function refresh();
}
