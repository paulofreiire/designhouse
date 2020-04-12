<?php

namespace App\Repositories\Contracts;

interface UserInterface
{
    public function findByEmail($email);

    public function search($request);
}
