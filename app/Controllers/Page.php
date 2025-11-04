<?php

namespace App\Controllers;

use App\Models\FeedbackModel;

class Page extends BaseController
{
    public function index(): string
    {
        $data = [
            'meta' => [
                'title' => 'MyProjek',
            ],
        ];
        return view('home', $data);
    }

    public function about(): string
    {
        $data = [
            'meta' => [
                'title' => 'About MyProjek',
            ],
        ];
        return view('about', $data);
    }


    protected $feedbackModel;

    
    public function __construct()
    {
        $this->feedbackModel = new FeedbackModel();
    }

    public function contact()
    {
        $data = [
            'title'      => 'Contact Us',
            'validation' => null,
        ];

        if ($this->request->getMethod(true) === 'POST') {

            $rules = [
                'name'    => 'required|min_length[3]',
                'email'   => 'required|valid_email',
                'message' => 'required|min_length[10]',
            ];

            if ($this->validate($rules)) {
                $feedbackData = [
                    'name'    => $this->request->getPost('name'),
                    'email'   => $this->request->getPost('email'),
                    'message' => $this->request->getPost('message'),
                ];

                $result = $this->feedbackModel->insert($feedbackData);
                
                if ($result) {
                    session()->setFlashdata('success', 'Thank you! Your message has been sent.');
                    return redirect()->to('/contact');
                } else {
                    $errors = $this->feedbackModel->errors();
                    $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Sorry! There was a problem sending your message. Please try again.';
                    session()->setFlashdata('error', $errorMsg);
                    return redirect()->to('/contact');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('contact', $data);
    }

    public function delete($id = null){
        $this->feedbackModel->delete($id);

        return redirect()->to('admin/feedback')->with('message', 'Feedback was successfully deleted.');
    }
}
   
