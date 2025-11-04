<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\Models\ArticleModel;

class Search extends BaseController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    protected $articleModel;
    
    public function __construct()
    {
        $this->articleModel = new ArticleModel();
        // helper('url');
    }
    public function index()
    {
        // Get keyword from GET parameter
        $keyword = $this->request->getGet('keyword');
        
        // Initialize search results
        $searchResults = [];
        
        // Perform search if keyword is provided
        if (!empty($keyword)) {
            $searchResults = $this->articleModel->search($keyword);
        }

        $data = [
            'keyword' => $keyword,
            'searchResults' => $searchResults,
            'total_results' => count($searchResults)
        ];

        return view('search', $data);
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
        //
    }
}
