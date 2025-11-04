<?php

namespace App\Controllers\Admin;

use App\Models\FeedbackModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Psr\Log\LoggerInterface;

class Feedback extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    protected $feedbackModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request,$response,$logger);
        $this->feedbackModel = new FeedbackModel();
    }
    public function index()
    {
        $feedbacks = $this->feedbackModel->orderBy('created_at', 'DESC')->findAll();
        $data = [
            'title' => 'Manage Feedback',
            'feedbacks' => $feedbacks
        ];

        return view('admin/feedback_list', $data);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        //
    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        //
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to('admin/feedback')->with('error', 'Invalid feedback ID.');
        }

        try {
            if ($this->feedbackModel->delete($id)) {
                return redirect()->to('admin/feedback')->with('success', 'Feedback deleted successfully');
            } else {
                return redirect()->to('admin/feedback')->with('error', 'Failed to delete feedback');
            }
        } catch (\Exception $e) {
            return redirect()->to('admin/feedback')->with('error', 'Database error: ' . $e->getMessage());
        }
    }
}
