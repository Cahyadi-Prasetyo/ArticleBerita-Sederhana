<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'email', 'username', 'password', 'avatar', 'last_login'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name'     => 'required|min_length[3]|max_length[32]',
        'email'    => 'required|valid_email|max_length[64]|is_unique[users.email,id,{id}]',
        'username' => 'required|min_length[3]|max_length[64]|is_unique[users.username,id,{id}]',
        'password' => 'required|min_length[6]',
    ];
    
    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email already exists.',
        ],
        'username' => [
            'is_unique' => 'Username already exists.',
        ],
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Find user by email or username
     */
    public function findByEmailOrUsername(string $identifier)
    {
        return $this->where('email', $identifier)
                    ->orWhere('username', $identifier)
                    ->first();
    }

    /**
     * Verify user password
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(int $userId)
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get user by ID without password
     */
    public function getUserById(int $id)
    {
        return $this->select('id, name, email, username, avatar, created_at, last_login')
                    ->find($id);
    }

    /**
     * Get all users without passwords
     */
    public function getAllUsers()
    {
        return $this->select('id, name, email, username, avatar, created_at, last_login')
                    ->findAll();
    }
}
