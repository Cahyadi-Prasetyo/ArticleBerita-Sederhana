<?php

namespace App\Controllers\Admin;

use App\Models\ArticleModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Controllers\BaseController;

class Post extends BaseController
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
        // Konfigurasi pagination berdasarkan tutorial Petani Kode
        $perPage = 2; // Jumlah post per halaman untuk admin
        $page = $this->request->getVar('page') ?? 1; // Halaman saat ini
        
        // Ambil keyword pencarian dari URL
        $keyword = $this->request->getGet('keyword');

        // Siapkan query builder
        $builder = $this->articleModel;

        // Jika ada keyword, tambahkan kondisi pencarian
        if (!empty($keyword)) {
            $builder = $builder->groupStart()
                             ->like('title', $keyword)
                             ->orLike('content', $keyword)
                             ->groupEnd();
        }
        
        // Hitung total data untuk pagination info
        $totalData = $builder->countAllResults(false); // false agar query tidak di-reset

        // Eksekusi query dengan pagination
        $articles = $builder->orderBy('created_at', 'DESC')
                           ->paginate($perPage, 'default', $page);

        // Siapkan data untuk dikirim ke view
        $data = [
            'articles' => $articles,
            'pager' => $this->articleModel->pager,
            'keyword' => $keyword,
            'total_data' => $totalData,
            'per_page' => $perPage,
            'current_page' => $page
        ];

        // Logika untuk menampilkan view yang sesuai
        if (count($articles) <= 0 && empty($keyword)) {
            // Tampilkan halaman kosong hanya jika tidak ada artikel sama sekali
            return view('admin/post_empty', $data);
        } else {
            // Tampilkan daftar artikel (hasil pencarian atau semua artikel)
            return view('admin/post_list', $data);
        }
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
        $data = [
            'title' => 'Create New Article',
            'return_url' => $this->buildReturnUrl()
        ];
        
        if (strtolower($this->request->getMethod()) === 'post') {
            $rules = [
                'title' => 'required|min_length[3]|max_length[128]',
                'content' => 'required'
            ];
            
            if ($this->validate($rules)) {
                $title = $this->request->getPost('title');
                $content = $this->request->getPost('content');
                $draft = $this->request->getPost('draft') ?? 'true';

                // Generate unique slug
                $baseSlug = url_title($title, '-', true);
                $slug = $baseSlug . '-' . time();

                $article = [
                    'id'      => uniqid(),
                    'title'   => $title,
                    'slug'    => $slug,
                    'content' => $content,
                    'draft'   => $draft
                ];

                try {
                    if ($this->articleModel->insert($article)) {
                        session()->setFlashdata('success', 'Article was successfully created.');
                        
                        // Preserve pagination state when returning from create operation
                        $returnUrl = $this->buildReturnUrl();
                        return redirect()->to($returnUrl);
                    } else {
                        $errors = $this->articleModel->errors();
                        $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Failed to create article.';
                        session()->setFlashdata('error', $errorMsg);
                        return redirect()->back()->withInput();
                    }
                } catch (\Exception $e) {
                    session()->setFlashdata('error', 'Database error: ' . $e->getMessage());
                    return redirect()->back()->withInput();
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('admin/post_new_form', $data);
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
        $article = $this->articleModel->find($id);

        if (!$article) {
            throw PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Edit Article',
            'article' => $article,
            'return_url' => $this->buildReturnUrl()
        ];

        if (strtolower($this->request->getMethod()) === 'post') {
            $rules = [
                'title' => 'required|min_length[3]|max_length[128]',
                'content' => 'required'
            ];

            if ($this->validate($rules)) {
                $articleData = [
                    'title'   => $this->request->getPost('title'),
                    'content' => $this->request->getPost('content'),
                    'draft'   => $this->request->getPost('draft') ?? 'true'
                ];

                try {
                    if ($this->articleModel->update($id, $articleData)) {
                        session()->setFlashdata('success', 'Article was successfully updated.');
                        
                        // Preserve pagination state when returning from edit operation
                        $returnUrl = $this->buildReturnUrl();
                        return redirect()->to($returnUrl);
                    } else {
                        $errors = $this->articleModel->errors();
                        $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Failed to update article.';
                        session()->setFlashdata('error', $errorMsg);
                        return redirect()->back()->withInput();
                    }
                } catch (\Exception $e) {
                    session()->setFlashdata('error', 'Database error: ' . $e->getMessage());
                    return redirect()->back()->withInput();
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('admin/post_edit_form', $data);
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
            session()->setFlashdata('error', 'Invalid article ID.');
            return redirect()->to('admin/post');
        }

        try {
            if ($this->articleModel->delete($id)) {
                session()->setFlashdata('success', 'Article was successfully deleted.');
                
                // Preserve pagination state when returning from delete operation
                $returnUrl = $this->buildReturnUrl();
                return redirect()->to($returnUrl);
            } else {
                session()->setFlashdata('error', 'Failed to delete article.');
                $returnUrl = $this->buildReturnUrl();
                return redirect()->to($returnUrl);
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Database error: ' . $e->getMessage());
            $returnUrl = $this->buildReturnUrl();
            return redirect()->to($returnUrl);
        }
    }

    /**
     * Build return URL with preserved search parameters
     * 
     * @return string The URL to return to with preserved state
     */
    private function buildReturnUrl()
    {
        $baseUrl = 'admin/post';
        $queryParams = [];
        
        // Check if request is available (for testing compatibility)
        if ($this->request) {
            // Preserve search keyword
            $keyword = $this->request->getGet('keyword');
            if (!empty($keyword)) {
                $queryParams['keyword'] = $keyword;
            }
        } else {
            // Fallback for testing - use $_GET directly
            if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
                $queryParams['keyword'] = $_GET['keyword'];
            }
        }
        
        // Build URL with query parameters
        if (!empty($queryParams)) {
            $baseUrl .= '?' . http_build_query($queryParams);
        }
        
        return $baseUrl;
    }


}
