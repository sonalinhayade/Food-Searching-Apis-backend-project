<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $table = 'authors';
    protected $primaryKey = "id";
    protected $allowedFields = ['username', 'email', 'phone_no', 'password'];

}