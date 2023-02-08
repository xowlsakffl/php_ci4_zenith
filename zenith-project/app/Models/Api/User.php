<?php
namespace App\Models\Api;

use CodeIgniter\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $useTimestaps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = ['user_id', 'user_pw', 'name'];
}