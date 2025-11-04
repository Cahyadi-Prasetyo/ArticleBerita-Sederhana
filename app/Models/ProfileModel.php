<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'email', 'password', 'avatar', 'password_updated_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'last_login';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get validation rules for profile data
     * Requirements: 4.2, 4.3, 4.4
     * 
     * @return array
     */
    public function getProfileRules(): array
    {
        return [
            'name' => [
                'rules' => 'required|min_length[2]|max_length[255]|regex_match[/^[a-zA-Z\s\-\'\.]+$/]',
                'errors' => [
                    'required' => 'Name is required.',
                    'min_length' => 'Name must be at least 2 characters long.',
                    'max_length' => 'Name cannot exceed 255 characters.',
                    'regex_match' => 'Name can only contain letters, spaces, hyphens, apostrophes, and periods.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|max_length[255]|is_unique[users.email,id,{id}]',
                'errors' => [
                    'required' => 'Email is required.',
                    'valid_email' => 'Please enter a valid email address.',
                    'max_length' => 'Email cannot exceed 255 characters.',
                    'is_unique' => 'This email address is already in use.'
                ]
            ]
        ];
    }

    /**
     * Get validation rules for password change
     * Requirements: 5.2, 5.3, 5.4
     * 
     * @return array
     */
    public function getPasswordRules(): array
    {
        return [
            'current_password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Current password is required.'
                ]
            ],
            'new_password' => [
                'rules' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
                'errors' => [
                    'required' => 'New password is required.',
                    'min_length' => 'New password must be at least 8 characters long.',
                    'regex_match' => 'New password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'required' => 'Password confirmation is required.',
                    'matches' => 'Password confirmation does not match the new password.'
                ]
            ]
        ];
    }

    /**
     * Update user profile information
     * Requirements: 4.2, 4.3, 4.4, 6.2
     * 
     * @param array $data Profile data to update
     * @return bool|int
     */
    public function updateProfile(array $data)
    {
        // Extract user ID from data
        $userId = $data['id'] ?? null;
        if (!$userId) {
            $this->errors = ['general' => 'User ID is required for profile update.'];
            return false;
        }
        
        // Remove ID from update data
        unset($data['id']);
        
        // Set validation rules for profile update with user ID context
        $rules = $this->getProfileRules();
        
        // Update email uniqueness rule to exclude current user
        if (isset($rules['email']['rules'])) {
            $rules['email']['rules'] = str_replace('{id}', $userId, $rules['email']['rules']);
        }
        
        $this->setValidationRules($rules);
        
        // Validate the data
        if (!$this->validate($data)) {
            return false;
        }
        
        // Additional business logic validation
        $existingUser = $this->find($userId);
        if (!$existingUser) {
            $this->errors = ['general' => 'User account not found.'];
            return false;
        }
        
        // Check if email is being changed to an existing email
        if (isset($data['email']) && $data['email'] !== $existingUser->email) {
            $emailExists = $this->where('email', $data['email'])
                               ->where('id !=', $userId)
                               ->first();
            if ($emailExists) {
                $this->errors = ['email' => 'This email address is already in use by another account.'];
                return false;
            }
        }
        
        // Update the user profile
        try {
            return $this->update($userId, $data);
        } catch (\Exception $e) {
            log_message('error', 'Profile update database error: ' . $e->getMessage());
            $this->errors = ['general' => 'Database error occurred while updating profile.'];
            return false;
        }
    }

    /**
     * Update user password
     * Requirements: 5.2, 5.3, 5.4, 6.2
     * 
     * @param int $userId User ID
     * @param string $hashedPassword Hashed password
     * @return bool|int
     */
    public function updatePassword(int $userId, string $hashedPassword)
    {
        // Validate user exists
        $existingUser = $this->find($userId);
        if (!$existingUser) {
            $this->errors = ['general' => 'User account not found.'];
            return false;
        }
        
        // Validate hashed password format
        if (empty($hashedPassword) || strlen($hashedPassword) < 60) {
            $this->errors = ['general' => 'Invalid password hash format.'];
            return false;
        }
        
        // Update the password and password_updated_at in database
        try {
            return $this->update($userId, [
                'password' => $hashedPassword,
                'password_updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Password update database error: ' . $e->getMessage());
            $this->errors = ['general' => 'Database error occurred while updating password.'];
            return false;
        }
    }

    /**
     * Verify current password for password change
     * Requirements: 5.1, 6.2
     * 
     * @param int $userId User ID
     * @param string $currentPassword Current password (plain text)
     * @return bool
     */
    public function verifyCurrentPassword(int $userId, string $currentPassword): bool
    {
        try {
            $user = $this->find($userId);
            if (!$user) {
                log_message('warning', 'Password verification attempted for non-existent user: ' . $userId);
                return false;
            }
            
            if (empty($currentPassword)) {
                return false;
            }
            
            return password_verify($currentPassword, $user->password);
        } catch (\Exception $e) {
            log_message('error', 'Password verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user profile data (without password)
     * 
     * @param int $userId User ID
     * @return object|null
     */
    public function getProfile(int $userId)
    {
        try {
            return $this->select('id, name, email, avatar, created_at, last_login, password_updated_at')
                        ->find($userId);
        } catch (\Exception $e) {
            log_message('error', 'Error retrieving profile for user ' . $userId . ': ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get detailed validation errors with user-friendly messages
     * Requirements: 6.2
     * 
     * @return array
     */
    public function getDetailedErrors(): array
    {
        $errors = $this->errors();
        $detailedErrors = [];
        
        foreach ($errors as $field => $message) {
            // Enhance error messages with more context
            switch ($field) {
                case 'name':
                    if (strpos($message, 'required') !== false) {
                        $detailedErrors[$field] = 'Your name is required and cannot be empty.';
                    } elseif (strpos($message, 'min_length') !== false) {
                        $detailedErrors[$field] = 'Your name must be at least 2 characters long.';
                    } elseif (strpos($message, 'regex_match') !== false) {
                        $detailedErrors[$field] = 'Your name can only contain letters, spaces, hyphens, apostrophes, and periods.';
                    } else {
                        $detailedErrors[$field] = $message;
                    }
                    break;
                    
                case 'email':
                    if (strpos($message, 'required') !== false) {
                        $detailedErrors[$field] = 'Your email address is required and cannot be empty.';
                    } elseif (strpos($message, 'valid_email') !== false) {
                        $detailedErrors[$field] = 'Please enter a valid email address (e.g., user@example.com).';
                    } elseif (strpos($message, 'is_unique') !== false) {
                        $detailedErrors[$field] = 'This email address is already registered. Please use a different email.';
                    } else {
                        $detailedErrors[$field] = $message;
                    }
                    break;
                    
                case 'new_password':
                    if (strpos($message, 'min_length') !== false) {
                        $detailedErrors[$field] = 'Your new password must be at least 8 characters long.';
                    } elseif (strpos($message, 'regex_match') !== false) {
                        $detailedErrors[$field] = 'Your new password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&).';
                    } else {
                        $detailedErrors[$field] = $message;
                    }
                    break;
                    
                case 'confirm_password':
                    if (strpos($message, 'matches') !== false) {
                        $detailedErrors[$field] = 'The password confirmation does not match your new password. Please type it again.';
                    } else {
                        $detailedErrors[$field] = $message;
                    }
                    break;
                    
                case 'current_password':
                    if (strpos($message, 'required') !== false) {
                        $detailedErrors[$field] = 'Please enter your current password to confirm this change.';
                    } else {
                        $detailedErrors[$field] = $message;
                    }
                    break;
                    
                default:
                    $detailedErrors[$field] = $message;
                    break;
            }
        }
        
        return $detailedErrors;
    }

    /**
     * Get formatted password last updated date
     * Requirements: 6.2
     * 
     * @param int $userId User ID
     * @return string Formatted date or 'Never'
     */
    public function getPasswordLastUpdated(int $userId): string
    {
        try {
            $user = $this->select('password_updated_at')->find($userId);
            if (!$user || !$user->password_updated_at) {
                return 'Never';
            }
            
            return date('d-m-Y H:i', strtotime($user->password_updated_at));
        } catch (\Exception $e) {
            log_message('error', 'Error getting password update date for user ' . $userId . ': ' . $e->getMessage());
            return 'Unknown';
        }
    }

    /**
     * Check if password needs to be updated (older than specified days)
     * Requirements: 6.2
     * 
     * @param int $userId User ID
     * @param int $days Number of days to check against
     * @return bool True if password is older than specified days
     */
    public function isPasswordExpired(int $userId, int $days = 90): bool
    {
        try {
            $user = $this->select('password_updated_at')->find($userId);
            if (!$user || !$user->password_updated_at) {
                return true; // Consider as expired if no update date
            }
            
            $updateDate = strtotime($user->password_updated_at);
            $expiryDate = strtotime("-{$days} days");
            
            return $updateDate < $expiryDate;
        } catch (\Exception $e) {
            log_message('error', 'Error checking password expiry for user ' . $userId . ': ' . $e->getMessage());
            return false;
        }
    }
}