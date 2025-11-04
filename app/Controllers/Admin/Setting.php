<?php

namespace App\Controllers\Admin;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Models\ProfileModel;

class Setting extends ResourceController
{
    protected $profileModel;
    protected $session;

    public function __construct()
    {
        $this->profileModel = new ProfileModel();
        $this->session = session();
        helper('avatar');
    }

    /**
     * Display profile settings page
     */
    public function index()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in to access settings.');
        }

        $userId = $this->session->get('user_id');
        $user = $this->profileModel->getProfile($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        try {
            $avatar_url = get_user_avatar($user);
        } catch (\Exception $e) {
            log_message('error', 'Avatar helper error: ' . $e->getMessage());
            $avatar_url = generate_default_avatar('U', 64);
        }
        
        $data = [
            'title' => 'Profile Settings',
            'user' => $user,
            'avatar_url' => $avatar_url,
            'success' => $this->session->getFlashdata('success'),
            'error' => $this->session->getFlashdata('error'),
            'validation' => $this->session->getFlashdata('validation'),
            'old_input' => $this->session->getFlashdata('old_input')
        ];
        
        return view('admin/setting', $data);
    }

    /**
     * Show edit profile form
     */
    public function edit_profile()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in to access settings.');
        }

        $userId = $this->session->get('user_id');
        $user = $this->profileModel->getProfile($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        $data = [
            'title' => 'Edit Profile',
            'user' => $user,
            'validation' => $this->session->getFlashdata('validation')
        ];
        
        return view('admin/setting_edit_profile', $data);
    }

    /**
     * Show edit password form
     */
    public function edit_password()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in to access settings.');
        }

        $userId = $this->session->get('user_id');
        $user = $this->profileModel->getProfile($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        $data = [
            'title' => 'Change Password',
            'user' => $user,
            'validation' => $this->session->getFlashdata('validation')
        ];
        
        return view('admin/setting_edit_password', $data);
    }

    /**
     * Show upload avatar form
     */
    public function upload_avatar()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in to access settings.');
        }

        $userId = $this->session->get('user_id');
        $user = $this->profileModel->getProfile($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        try {
            $avatar_url = get_user_avatar($user);
        } catch (\Exception $e) {
            log_message('error', 'Avatar helper error: ' . $e->getMessage());
            $avatar_url = generate_default_avatar('U', 64);
        }

        $data = [
            'title' => 'Upload Avatar',
            'user' => $user,
            'avatar_url' => $avatar_url
        ];
        
        return view('admin/setting_upload_avatar', $data);
    }

    /**
     * Handle remove avatar
     */
    public function remove_avatar()
    {
        if (!$this->session->get('logged_in')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please log in to access settings.'
                ]);
            }
            return redirect()->to('/login')->with('error', 'Please log in to access settings.');
        }

        $userId = $this->session->get('user_id');

        try {
            $currentUser = $this->profileModel->getProfile($userId);
            if (!$currentUser) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'User account not found.'
                    ]);
                }
                return redirect()->to('/login')->with('error', 'User account not found.');
            }

            if (!$currentUser->avatar) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'You are already using the default avatar.'
                    ]);
                }
                return redirect()->to('admin/setting')->with('message', 'You are already using the default avatar.');
            }

            // Delete avatar file from server
            $uploadPath = FCPATH . 'uploads/avatars/';
            $avatarPath = $uploadPath . $currentUser->avatar;
            
            if (file_exists($avatarPath)) {
                unlink($avatarPath);
            }

            // Update database
            $this->profileModel->update($userId, ['avatar' => null]);
            
            // Update session avatar
            $this->session->set('avatar', null);
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Avatar removed successfully.'
                ]);
            }
            
            return redirect()->to('admin/setting')->with('success', 'Avatar removed successfully.');

        } catch (\Exception $e) {
            log_message('error', 'Avatar removal error: ' . $e->getMessage());
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'An error occurred while removing avatar.'
                ]);
            }
            
            return redirect()->to('admin/setting')->with('error', 'An error occurred while removing avatar.');
        }
    }

    /**
     * Handle avatar upload
     */
    public function uploadAvatar()
    {
        log_message('info', 'Avatar upload started');
        
        if (!$this->session->get('logged_in')) {
            log_message('warning', 'Avatar upload attempted without login');
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Unauthorized access.',
                    'error_type' => 'authentication'
                ]);
            }
            return redirect()->to('/login')->with('error', 'Please log in to continue.');
        }

        $userId = $this->session->get('user_id');
        $file = $this->request->getFile('avatar');
        
        log_message('info', 'Avatar upload for user ID: ' . $userId);
        log_message('info', 'File received: ' . ($file ? 'Yes' : 'No'));

        try {
            if (!$file || !$file->isValid()) {
                $errorMsg = 'No valid file uploaded.';
                log_message('error', 'Avatar upload failed: ' . $errorMsg);
                if ($file) {
                    log_message('error', 'File error: ' . $file->getErrorString());
                }
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $errorMsg]);
                }
                return redirect()->back()->with('error', $errorMsg);
            }

            // Check file size (2MB max)
            if ($file->getSize() > 2 * 1024 * 1024) {
                $errorMsg = 'File size must not exceed 2MB.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $errorMsg]);
                }
                return redirect()->back()->with('error', $errorMsg);
            }

            // Check file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                $errorMsg = 'Only JPG, PNG, and GIF files are allowed.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $errorMsg]);
                }
                return redirect()->back()->with('error', $errorMsg);
            }

            // Create upload directory
            $uploadPath = FCPATH . 'uploads/avatars/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate filename
            $extension = $file->getClientExtension();
            $filename = 'user_' . $userId . '_avatar_' . time() . '.' . $extension;

            // Remove old avatar
            $currentUser = $this->profileModel->getProfile($userId);
            if ($currentUser && $currentUser->avatar) {
                $oldPath = $uploadPath . $currentUser->avatar;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Move file
            log_message('info', 'Attempting to move file to: ' . $uploadPath . $filename);
            if (!$file->move($uploadPath, $filename)) {
                $errorMsg = 'Failed to upload file.';
                log_message('error', 'File move failed: ' . $errorMsg);
                log_message('error', 'Upload path: ' . $uploadPath);
                log_message('error', 'Filename: ' . $filename);
                log_message('error', 'File error: ' . $file->getErrorString());
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $errorMsg]);
                }
                return redirect()->back()->with('error', $errorMsg);
            }
            
            log_message('info', 'File moved successfully to: ' . $uploadPath . $filename);

            // Update database
            $this->profileModel->update($userId, ['avatar' => $filename]);
            
            // Update session with new avatar
            $this->session->set('avatar', $filename);

            $successMsg = 'Avatar uploaded successfully!';
            
            if ($this->request->isAJAX()) {
                // Get updated user data
                $user = $this->profileModel->getProfile($userId);
                
                // Generate avatar URL with timestamp to avoid cache
                helper('avatar');
                $avatarUrl = get_user_avatar($user);
                
                // Add timestamp to avoid browser cache
                if (strpos($avatarUrl, 'uploads/avatars/') !== false) {
                    $avatarUrl .= '?t=' . time();
                }
                
                // Log for debugging
                log_message('info', 'Avatar upload success - returning URL: ' . $avatarUrl);
                
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => $successMsg,
                    'avatar_url' => $avatarUrl,
                    'filename' => $filename
                ]);
            }
            
            return redirect()->to('admin/setting')->with('success', $successMsg);

        } catch (\Exception $e) {
            log_message('error', 'Avatar upload error: ' . $e->getMessage());
            log_message('error', 'Avatar upload stack trace: ' . $e->getTraceAsString());
            
            // More detailed error message for debugging
            $errorMsg = 'Upload failed: ' . $e->getMessage();
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => $errorMsg,
                    'debug_info' => [
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]
                ]);
            }
            
            return redirect()->back()->with('error', $errorMsg);
        }
    }

    /**
     * Handle profile update
     */
    public function updateProfile()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Session expired.');
        }

        $userId = $this->session->get('user_id');

        try {
            $data = [
                'id' => $userId,
                'name' => trim($this->request->getPost('name')),
                'email' => trim(strtolower($this->request->getPost('email')))
            ];

            $this->session->setFlashdata('old_input', [
                'name' => $data['name'],
                'email' => $data['email']
            ]);

            if (empty($data['name']) || empty($data['email'])) {
                return redirect()->back()->with('error', 'Please fill in all required fields.');
            }

            $updateResult = $this->profileModel->updateProfile($data);

            if (!$updateResult) {
                $errors = $this->profileModel->getDetailedErrors();
                $this->session->setFlashdata('validation', $errors);
                return redirect()->back()->with('error', 'Please correct the errors.');
            }

            // Update session with new profile data
            $this->session->set([
                'name' => $data['name'],
                'email' => $data['email']
            ]);

            return redirect()->to('admin/setting')->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Profile update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating profile.');
        }
    }

    /**
     * Handle password change
     */
    public function updatePassword()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Session expired.');
        }

        $userId = $this->session->get('user_id');

        try {
            $data = [
                'current_password' => $this->request->getPost('current_password'),
                'new_password' => $this->request->getPost('new_password'),
                'confirm_password' => $this->request->getPost('confirm_password')
            ];

            if (empty($data['current_password']) || empty($data['new_password']) || empty($data['confirm_password'])) {
                return redirect()->back()->with('error', 'Please fill in all password fields.');
            }

            if (!$this->profileModel->verifyCurrentPassword($userId, $data['current_password'])) {
                return redirect()->back()->with('error', 'Current password is incorrect.');
            }

            $validation = \Config\Services::validation();
            $validation->setRules($this->profileModel->getPasswordRules());

            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
                $this->session->setFlashdata('validation', $errors);
                return redirect()->back()->with('error', 'Please correct the password requirements.');
            }

            $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);
            $this->profileModel->updatePassword($userId, $hashedPassword);

            return redirect()->to('admin/setting')->with('success', 'Password changed successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Password update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while changing password.');
        }
    }

    // Required ResourceController methods
    public function show($id = null) { }
    public function new() { }
    public function create() { }
    public function edit($id = null) { }
    public function update($id = null) { }
    public function delete($id = null) { }
}