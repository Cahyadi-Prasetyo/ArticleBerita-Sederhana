<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuthModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    protected $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        helper(['form', 'url']);
    }

    /**
     * Display login form
     */
    public function login()
    {
        // If user already logged in, redirect to admin dashboard
        if (session()->get('user_id')) {
            return redirect()->to('admin/dashboard');
        }

        $data = ['title' => 'Login'];

        if (strtolower($this->request->getMethod()) === 'post') {
            $rules = [
                'username' => 'required',
                'password' => 'required'
            ];

            if ($this->validate($rules)) {
                $identifier = $this->request->getPost('username');
                $password = $this->request->getPost('password');

                $user = $this->authModel->findByEmailOrUsername($identifier);

                if ($user && $this->authModel->verifyPassword($password, $user->password)) {
                    // Update last login
                    $this->authModel->updateLastLogin($user->id);

                    // Set session data
                    session()->set([
                        'user_id'   => $user->id,
                        'username'  => $user->username,
                        'name'      => $user->name,
                        'email'     => $user->email,
                        'avatar'    => $user->avatar ?? null,
                        'logged_in' => true
                    ]);

                    session()->setFlashdata('success', 'Welcome back, ' . $user->name . '!');
                    
                    // Check if there's a redirect URL stored
                    $redirectUrl = session()->get('redirect_url');
                    if ($redirectUrl) {
                        session()->remove('redirect_url');
                        return redirect()->to($redirectUrl);
                    }
                    
                    return redirect()->to('admin/dashboard');
                } else {
                    session()->setFlashdata('message_login_error', 'Invalid email/username or password.');
                    return redirect()->back()->withInput();
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('login', $data);
    }

    /**
     * Display register form
     */
    public function register()
    {
        // If user already logged in, redirect to admin dashboard
        if (session()->get('user_id')) {
            return redirect()->to('admin/dashboard');
        }

        $data = ['title' => 'Register'];

        if (strtolower($this->request->getMethod()) === 'post') {
            $rules = [
                'name'     => 'required|min_length[3]|max_length[32]',
                'email'    => 'required|valid_email|max_length[64]|is_unique[users.email]',
                'username' => 'required|min_length[3]|max_length[64]|is_unique[users.username]',
                'password' => 'required|min_length[6]',
                'confirm_password' => 'required|matches[password]'
            ];

            if ($this->validate($rules)) {
                $userData = [
                    'name'     => $this->request->getPost('name'),
                    'email'    => $this->request->getPost('email'),
                    'username' => $this->request->getPost('username'),
                    'password' => $this->request->getPost('password')
                ];

                try {
                    if ($this->authModel->insert($userData)) {
                        session()->setFlashdata('success', 'Registration successful! Please login.');
                        return redirect()->to('login');
                    } else {
                        $errors = $this->authModel->errors();
                        $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Registration failed.';
                        session()->setFlashdata('error', $errorMsg);
                        return redirect()->back()->withInput();
                    }
                } catch (\Exception $e) {
                    session()->setFlashdata('error', 'Registration failed: ' . $e->getMessage());
                    return redirect()->back()->withInput();
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('register', $data);
    }

    /**
     * Logout user
     */
    public function logout()
    {
        session()->destroy();
        session()->setFlashdata('success', 'You have been logged out successfully.');
        return redirect()->to('login');
    }

    /**
     * Check if user is logged in (for AJAX requests)
     */
    public function checkAuth()
    {
        $isLoggedIn = session()->get('logged_in') ? true : false;
        $response = ['logged_in' => $isLoggedIn];
        
        if ($isLoggedIn) {
            // Get current user data
            $userId = session()->get('user_id');
            $user = $this->authModel->find($userId);
            
            if ($user) {
                helper('avatar');
                $avatarUrl = get_user_avatar($user);
                
                // Add cache busting for uploaded avatars
                if ($user->avatar && strpos($avatarUrl, 'uploads/avatars/') !== false) {
                    $avatarPath = FCPATH . 'uploads/avatars/' . $user->avatar;
                    if (file_exists($avatarPath)) {
                        $avatarUrl = base_url('uploads/avatars/' . $user->avatar) . '?v=' . filemtime($avatarPath);
                    }
                }
                
                $response['avatar_url'] = $avatarUrl;
                $response['name'] = $user->name;
                $response['email'] = $user->email;
                $response['has_custom_avatar'] = !empty($user->avatar);
                
                // Debug logging
                log_message('debug', 'checkAuth - User ID: ' . $userId);
                log_message('debug', 'checkAuth - User avatar: ' . ($user->avatar ?? 'NULL'));
                log_message('debug', 'checkAuth - Avatar URL: ' . $avatarUrl);
            }
        }
        
        return $this->response->setJSON($response);
    }
}
