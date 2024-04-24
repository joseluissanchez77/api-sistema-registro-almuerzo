<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;
use App\Models\User;

class AuthRepository extends BaseRepository
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
    }
}
