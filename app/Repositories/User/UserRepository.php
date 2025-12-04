<?php

namespace App\Repositories\User;

use LaravelEasyRepository\Repository;

interface UserRepository extends Repository
{
    public function register($data);
    public function login(array $credentials);
    public function logout();
}
