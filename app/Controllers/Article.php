<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Article extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    protected $articleModel;

    public function __construct()
    {
        // parent::__construct();
        $this -> articleModel = new ArticleModel();
    }
    public function index()
    {
        // Konfigurasi pagination sederhana tanpa fitur pencarian
        $perPage = 2; // Jumlah artikel per halaman
        $page = $this->request->getVar('page') ?? 1; // Halaman saat ini

        // Query builder untuk artikel terbit
        $builder = $this->articleModel->where('draft', 'false');

        // Hitung total data untuk informasi pagination
        $totalData = $builder->countAllResults(false); // false agar query tidak di-reset

        // Ambil data dengan pagination
        $articles = $builder->orderBy('created_at', 'DESC')
                           ->paginate($perPage, 'default', $page);

        // Siapkan data untuk view
        $data = [
            'title' => 'Daftar Artikel',
            'articles' => $articles,
            'pager' => $this->articleModel->pager,
            'total_data' => $totalData,
            'per_page' => $perPage,
            'current_page' => $page
        ];

        // Tampilkan list artikel atau halaman kosong jika tidak ada data
        if (empty($articles)) {
            return view('articles/list_article', $data); // Tetap tampilkan view dengan pesan kosong di view
        }
        return view('articles/list_article', $data);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($slug = null)
    {
        $data['article'] = $this->articleModel->findBySlug($slug);
        if(!$slug || !$data['article']){
            throw PageNotFoundException::forPageNotFound();
        }
        return view('articles/show_article',$data);
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
