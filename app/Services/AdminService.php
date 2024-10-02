<?php

namespace App\Services;

use App\Models\Admin;

class AdminService
{
    public function getAll()
    {
        return Admin::get();
    }
}
