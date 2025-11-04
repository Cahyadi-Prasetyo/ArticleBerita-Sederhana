<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            // Published articles
            [
                'id' => 'article-001',
                'title' => 'First Published Article',
                'slug' => 'first-published-article',
                'content' => 'This is the content of the first published article.',
                'draft' => 'false',
                'created_at' => '2024-01-01 10:00:00',
            ],
            [
                'id' => 'article-002',
                'title' => 'Second Published Article',
                'slug' => 'second-published-article',
                'content' => 'This is the content of the second published article.',
                'draft' => 'false',
                'created_at' => '2024-01-02 10:00:00',
            ],
            [
                'id' => 'article-003',
                'title' => 'Third Published Article',
                'slug' => 'third-published-article',
                'content' => 'This is the content of the third published article.',
                'draft' => 'false',
                'created_at' => '2024-01-03 10:00:00',
            ],
            [
                'id' => 'article-004',
                'title' => 'Fourth Published Article',
                'slug' => 'fourth-published-article',
                'content' => 'This is the content of the fourth published article.',
                'draft' => 'false',
                'created_at' => '2024-01-04 10:00:00',
            ],
            [
                'id' => 'article-005',
                'title' => 'Fifth Published Article',
                'slug' => 'fifth-published-article',
                'content' => 'This is the content of the fifth published article.',
                'draft' => 'false',
                'created_at' => '2024-01-05 10:00:00',
            ],
            [
                'id' => 'article-006',
                'title' => 'Sixth Published Article',
                'slug' => 'sixth-published-article',
                'content' => 'This is the content of the sixth published article.',
                'draft' => 'false',
                'created_at' => '2024-01-06 10:00:00',
            ],
            [
                'id' => 'article-007',
                'title' => 'Seventh Published Article',
                'slug' => 'seventh-published-article',
                'content' => 'This is the content of the seventh published article.',
                'draft' => 'false',
                'created_at' => '2024-01-07 10:00:00',
            ],
            [
                'id' => 'article-008',
                'title' => 'Eighth Published Article',
                'slug' => 'eighth-published-article',
                'content' => 'This is the content of the eighth published article.',
                'draft' => 'false',
                'created_at' => '2024-01-08 10:00:00',
            ],
            [
                'id' => 'article-009',
                'title' => 'Ninth Published Article',
                'slug' => 'ninth-published-article',
                'content' => 'This is the content of the ninth published article.',
                'draft' => 'false',
                'created_at' => '2024-01-09 10:00:00',
            ],
            [
                'id' => 'article-010',
                'title' => 'Tenth Published Article',
                'slug' => 'tenth-published-article',
                'content' => 'This is the content of the tenth published article.',
                'draft' => 'false',
                'created_at' => '2024-01-10 10:00:00',
            ],
            [
                'id' => 'article-011',
                'title' => 'Eleventh Published Article',
                'slug' => 'eleventh-published-article',
                'content' => 'This is the content of the eleventh published article.',
                'draft' => 'false',
                'created_at' => '2024-01-11 10:00:00',
            ],
            [
                'id' => 'article-012',
                'title' => 'Twelfth Published Article',
                'slug' => 'twelfth-published-article',
                'content' => 'This is the content of the twelfth published article.',
                'draft' => 'false',
                'created_at' => '2024-01-12 10:00:00',
            ],
            // Draft articles
            [
                'id' => 'draft-001',
                'title' => 'First Draft Article',
                'slug' => 'first-draft-article',
                'content' => 'This is the content of the first draft article.',
                'draft' => 'true',
                'created_at' => '2024-01-13 10:00:00',
            ],
            [
                'id' => 'draft-002',
                'title' => 'Second Draft Article',
                'slug' => 'second-draft-article',
                'content' => 'This is the content of the second draft article.',
                'draft' => 'true',
                'created_at' => '2024-01-14 10:00:00',
            ],
        ];

        $builder = $this->db->table('articles');

        foreach ($articles as $article) {
            $builder->insert($article);
        }
    }
}