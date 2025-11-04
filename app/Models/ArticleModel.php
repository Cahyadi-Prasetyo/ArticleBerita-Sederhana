<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $table            = 'articles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id','title','content','slug','draft','updated_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
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
     * Get all articles (equivalent to CI3 get() method)
     */
    public function get()
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    public function getPublished($limit = null, $offset = 0)
    {
        return $this -> where('draft','false') -> findAll($limit, $offset);
    }

    public function findBySlug($slug = null){
        if(!$slug){
            return null;
        }

        return $this->where('slug',$slug)->first();

    }

    public function search($keyword = null)
    {
        if (empty($keyword)) {
            return [];
        }

        return $this->like('title', $keyword)
            ->orLike('content', $keyword)
            ->findAll();
    }
    
    /**
     * Advanced search with multiple keywords
     */
    

    /**
     * Get paginated articles with optional filtering conditions
     * 
     * @param int $page Current page number (1-based)
     * @param int $perPage Number of articles per page
     * @param array $conditions Optional filtering conditions
     * @return array Array of article objects
     */
    public function countPublished()
    {
        // Gunakan where() untuk filter dan countAllResults() untuk menghitung hasilnya.
        return $this->where('draft', 'FALSE')
            ->countAllResults();
    }
}
