<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run()
    {
        $articles = [
            [
                'id' => uniqid(),
                'title' => 'Panduan Lengkap Belajar PHP untuk Pemula',
                'slug' => 'panduan-lengkap-belajar-php-untuk-pemula',
                'content' => '<h2>Pengenalan PHP</h2>
<p>PHP (PHP: Hypertext Preprocessor) adalah bahasa pemrograman server-side yang sangat populer untuk pengembangan web. Bahasa ini mudah dipelajari dan memiliki sintaks yang sederhana.</p>

<h3>Mengapa Belajar PHP?</h3>
<ul>
<li>Mudah dipelajari untuk pemula</li>
<li>Gratis dan open source</li>
<li>Dukungan komunitas yang besar</li>
<li>Banyak framework populer seperti Laravel, CodeIgniter</li>
<li>Kompatibel dengan berbagai database</li>
</ul>

<h3>Instalasi PHP</h3>
<p>Untuk memulai belajar PHP, Anda perlu menginstall:</p>
<ol>
<li>Web server (Apache/Nginx)</li>
<li>PHP interpreter</li>
<li>Database (MySQL/PostgreSQL)</li>
</ol>

<p>Atau gunakan paket lengkap seperti XAMPP, WAMP, atau MAMP untuk kemudahan instalasi.</p>

<h3>Sintaks Dasar PHP</h3>
<pre><code>&lt;?php
echo "Hello, World!";
$nama = "Budi";
echo "Halo, " . $nama;
?&gt;</code></pre>

<p>PHP selalu dimulai dengan tag <code>&lt;?php</code> dan diakhiri dengan <code>?&gt;</code>.</p>',
                'draft' => 'false',
                'created_at' => date('Y-m-d H:i:s', strtotime('-7 days'))
            ],
            [
                'id' => uniqid(),
                'title' => 'Tips dan Trik Optimasi Database MySQL',
                'slug' => 'tips-dan-trik-optimasi-database-mysql',
                'content' => '<h2>Optimasi Database MySQL</h2>
<p>Database yang lambat dapat menjadi bottleneck utama dalam aplikasi web. Berikut adalah tips untuk mengoptimalkan performa MySQL.</p>

<h3>1. Gunakan Index dengan Bijak</h3>
<p>Index adalah kunci utama untuk query yang cepat:</p>
<pre><code>CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_created_at ON articles(created_at);</code></pre>

<h3>2. Optimasi Query</h3>
<ul>
<li>Hindari SELECT * jika tidak perlu</li>
<li>Gunakan LIMIT untuk membatasi hasil</li>
<li>Gunakan WHERE clause yang efisien</li>
<li>Hindari fungsi dalam WHERE clause</li>
</ul>

<h3>3. Konfigurasi MySQL</h3>
<p>Sesuaikan konfigurasi MySQL dengan kebutuhan:</p>
<pre><code>innodb_buffer_pool_size = 1G
query_cache_size = 256M
max_connections = 200</code></pre>

<h3>4. Monitoring dan Analisis</h3>
<p>Gunakan tools seperti:</p>
<ul>
<li>EXPLAIN untuk analisis query</li>
<li>MySQL Workbench untuk monitoring</li>
<li>Slow query log untuk identifikasi query lambat</li>
</ul>',
                'draft' => 'false',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ],
            [
                'id' => uniqid(),
                'title' => 'Membangun API RESTful dengan CodeIgniter 4',
                'slug' => 'membangun-api-restful-dengan-codeigniter-4',
                'content' => '<h2>API RESTful dengan CodeIgniter 4</h2>
<p>CodeIgniter 4 menyediakan fitur-fitur modern untuk membangun API RESTful yang powerful dan scalable.</p>

<h3>Struktur API RESTful</h3>
<p>API RESTful menggunakan HTTP methods untuk operasi CRUD:</p>
<ul>
<li>GET - Mengambil data</li>
<li>POST - Membuat data baru</li>
<li>PUT/PATCH - Mengupdate data</li>
<li>DELETE - Menghapus data</li>
</ul>

<h3>Resource Controller</h3>
<p>CodeIgniter 4 menyediakan Resource Controller untuk API:</p>
<pre><code>&lt;?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Articles extends ResourceController
{
    protected $modelName = "App\Models\ArticleModel";
    protected $format = "json";
    
    public function index()
    {
        return $this->respond($this->model->findAll());
    }
    
    public function show($id = null)
    {
        $data = $this->model->find($id);
        if ($data) {
            return $this->respond($data);
        }
        return $this->failNotFound("Article not found");
    }
}
?&gt;</code></pre>

<h3>Authentication & Authorization</h3>
<p>Implementasi JWT atau API Key untuk keamanan API:</p>
<pre><code>$routes->group("api", ["filter" => "apiauth"], function($routes) {
    $routes->resource("articles");
    $routes->resource("users");
});</code></pre>',
                'draft' => 'false',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            [
                'id' => uniqid(),
                'title' => 'JavaScript ES6+ Features yang Wajib Dikuasai',
                'slug' => 'javascript-es6-features-yang-wajib-dikuasai',
                'content' => '<h2>JavaScript ES6+ Features</h2>
<p>ECMAScript 6 (ES6) dan versi selanjutnya membawa banyak fitur baru yang membuat JavaScript lebih powerful dan mudah digunakan.</p>

<h3>1. Arrow Functions</h3>
<p>Sintaks yang lebih ringkas untuk function:</p>
<pre><code>// ES5
function add(a, b) {
    return a + b;
}

// ES6
const add = (a, b) => a + b;

// Array methods
const numbers = [1, 2, 3, 4, 5];
const doubled = numbers.map(n => n * 2);</code></pre>

<h3>2. Template Literals</h3>
<p>String interpolation yang lebih mudah:</p>
<pre><code>const name = "John";
const age = 30;

// ES5
const message = "Hello, my name is " + name + " and I am " + age + " years old";

// ES6
const message = `Hello, my name is ${name} and I am ${age} years old`;</code></pre>

<h3>3. Destructuring</h3>
<p>Ekstrak nilai dari array atau object:</p>
<pre><code>// Array destructuring
const [first, second, ...rest] = [1, 2, 3, 4, 5];

// Object destructuring
const {name, email} = user;
const {name: userName, email: userEmail} = user;</code></pre>

<h3>4. Async/Await</h3>
<p>Menangani asynchronous code dengan lebih clean:</p>
<pre><code>async function fetchUser(id) {
    try {
        const response = await fetch(`/api/users/${id}`);
        const user = await response.json();
        return user;
    } catch (error) {
        console.error("Error fetching user:", error);
    }
}</code></pre>',
                'draft' => 'false',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'id' => uniqid(),
                'title' => 'Keamanan Web: Mencegah Serangan XSS dan SQL Injection',
                'slug' => 'keamanan-web-mencegah-serangan-xss-dan-sql-injection',
                'content' => '<h2>Keamanan Web Application</h2>
<p>Keamanan adalah aspek yang sangat penting dalam pengembangan web. Dua serangan yang paling umum adalah XSS dan SQL Injection.</p>

<h3>Cross-Site Scripting (XSS)</h3>
<p>XSS terjadi ketika attacker menyisipkan script berbahaya ke dalam web page.</p>

<h4>Pencegahan XSS:</h4>
<ul>
<li>Selalu escape output HTML</li>
<li>Validasi dan sanitasi input</li>
<li>Gunakan Content Security Policy (CSP)</li>
<li>Hindari innerHTML, gunakan textContent</li>
</ul>

<pre><code>// PHP - Escape output
echo htmlspecialchars($userInput, ENT_QUOTES, "UTF-8");

// JavaScript - Escape HTML
function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}</code></pre>

<h3>SQL Injection</h3>
<p>SQL Injection terjadi ketika input user dimasukkan langsung ke dalam query SQL.</p>

<h4>Pencegahan SQL Injection:</h4>
<ul>
<li>Gunakan Prepared Statements</li>
<li>Validasi input dengan whitelist</li>
<li>Gunakan ORM/Query Builder</li>
<li>Principle of least privilege untuk database user</li>
</ul>

<pre><code>// PHP PDO - Prepared Statement
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

// CodeIgniter 4 - Query Builder
$users = $this->db->table("users")
                  ->where("email", $email)
                  ->get()
                  ->getResult();</code></pre>

<h3>Best Practices Keamanan</h3>
<ol>
<li>Selalu validasi input di server-side</li>
<li>Gunakan HTTPS untuk semua komunikasi</li>
<li>Implementasi rate limiting</li>
<li>Update dependencies secara berkala</li>
<li>Gunakan security headers</li>
</ol>',
                'draft' => 'false',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'id' => uniqid(),
                'title' => 'Responsive Web Design dengan CSS Grid dan Flexbox',
                'slug' => 'responsive-web-design-dengan-css-grid-dan-flexbox',
                'content' => '<h2>Responsive Web Design Modern</h2>
<p>CSS Grid dan Flexbox adalah dua teknologi layout modern yang memudahkan pembuatan design responsive.</p>

<h3>CSS Flexbox</h3>
<p>Flexbox ideal untuk layout 1-dimensional (baris atau kolom):</p>
<pre><code>.container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.item {
    flex: 1 1 300px; /* grow shrink basis */
}</code></pre>

<h3>CSS Grid</h3>
<p>Grid perfect untuk layout 2-dimensional (baris dan kolom):</p>
<pre><code>.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    grid-gap: 20px;
}

.header {
    grid-column: 1 / -1;
}

.sidebar {
    grid-row: span 2;
}</code></pre>

<h3>Media Queries</h3>
<p>Responsive breakpoints untuk berbagai device:</p>
<pre><code>/* Mobile First Approach */
.container {
    padding: 1rem;
}

/* Tablet */
@media (min-width: 768px) {
    .container {
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .container {
        display: grid;
        grid-template-columns: 1fr 3fr 1fr;
    }
}</code></pre>

<h3>Modern CSS Units</h3>
<ul>
<li><code>vw, vh</code> - Viewport width/height</li>
<li><code>rem, em</code> - Relative units</li>
<li><code>clamp()</code> - Responsive typography</li>
<li><code>min(), max()</code> - Dynamic sizing</li>
</ul>

<pre><code>/* Responsive typography */
h1 {
    font-size: clamp(1.5rem, 4vw, 3rem);
}

/* Dynamic width */
.card {
    width: min(90%, 500px);
}</code></pre>',
                'draft' => 'false',
                'created_at' => date('Y-m-d H:i:s', strtotime('-6 hours'))
            ],
            [
                'id' => uniqid(),
                'title' => 'Docker untuk Developer: Containerization Made Easy',
                'slug' => 'docker-untuk-developer-containerization-made-easy',
                'content' => '<h2>Docker untuk Developer</h2>
<p>Docker memungkinkan developer untuk mengemas aplikasi beserta dependenciesnya dalam container yang portable dan konsisten.</p>

<h3>Konsep Dasar Docker</h3>
<ul>
<li><strong>Image</strong> - Template untuk membuat container</li>
<li><strong>Container</strong> - Instance yang berjalan dari image</li>
<li><strong>Dockerfile</strong> - Script untuk build image</li>
<li><strong>Docker Compose</strong> - Orchestration untuk multi-container</li>
</ul>

<h3>Dockerfile untuk PHP Application</h3>
<pre><code>FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite

# Copy application code
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80</code></pre>

<h3>Docker Compose untuk Development</h3>
<pre><code>version: "3.8"

services:
  web:
    build: .
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_NAME=myapp
      - DB_USER=root
      - DB_PASS=secret

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: myapp
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - "3306:3306"

volumes:
  db_data:</code></pre>

<h3>Docker Commands Essentials</h3>
<pre><code># Build image
docker build -t myapp .

# Run container
docker run -d -p 8080:80 myapp

# List containers
docker ps

# Execute command in container
docker exec -it container_name bash

# Docker Compose
docker-compose up -d
docker-compose down
docker-compose logs</code></pre>

<h3>Benefits Docker untuk Developer</h3>
<ul>
<li>Environment consistency</li>
<li>Easy onboarding untuk team baru</li>
<li>Isolasi dependencies</li>
<li>Scalability dan deployment yang mudah</li>
</ul>',
                'draft' => 'true',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ],
            [
                'id' => uniqid(),
                'title' => 'Git Workflow: Best Practices untuk Tim Developer',
                'slug' => 'git-workflow-best-practices-untuk-tim-developer',
                'content' => '<h2>Git Workflow untuk Tim</h2>
<p>Git workflow yang baik adalah kunci kolaborasi yang efektif dalam tim developer. Berikut adalah best practices yang terbukti.</p>

<h3>Git Flow Model</h3>
<p>Model branching yang populer untuk project besar:</p>
<ul>
<li><strong>main/master</strong> - Production ready code</li>
<li><strong>develop</strong> - Integration branch</li>
<li><strong>feature/*</strong> - Feature development</li>
<li><strong>release/*</strong> - Release preparation</li>
<li><strong>hotfix/*</strong> - Emergency fixes</li>
</ul>

<h3>Feature Branch Workflow</h3>
<pre><code># Create feature branch
git checkout -b feature/user-authentication

# Work on feature
git add .
git commit -m "Add user login functionality"

# Push to remote
git push origin feature/user-authentication

# Create Pull Request
# After review and approval, merge to develop</code></pre>

<h3>Commit Message Conventions</h3>
<p>Gunakan format yang konsisten untuk commit messages:</p>
<pre><code>type(scope): description

feat(auth): add user login functionality
fix(api): resolve null pointer exception
docs(readme): update installation guide
style(css): fix button alignment
refactor(user): extract validation logic
test(auth): add unit tests for login</code></pre>

<h3>Code Review Best Practices</h3>
<ul>
<li>Review code, bukan programmer</li>
<li>Fokus pada logic, security, dan performance</li>
<li>Berikan feedback yang konstruktif</li>
<li>Test perubahan sebelum approve</li>
<li>Gunakan automated testing</li>
</ul>

<h3>Git Commands untuk Tim</h3>
<pre><code># Sync dengan remote
git fetch origin
git pull origin develop

# Interactive rebase untuk clean history
git rebase -i HEAD~3

# Squash commits
git reset --soft HEAD~3
git commit -m "Implement user authentication"

# Cherry pick specific commit
git cherry-pick commit-hash

# Resolve merge conflicts
git mergetool
git add .
git commit</code></pre>

<h3>Continuous Integration</h3>
<p>Integrasikan dengan CI/CD pipeline:</p>
<pre><code># .github/workflows/ci.yml
name: CI
on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: vendor/bin/phpunit</code></pre>',
                'draft' => 'true',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ]
        ];

        // Insert articles
        foreach ($articles as $article) {
            $this->db->table('articles')->insert($article);
        }

        echo "Inserted " . count($articles) . " articles successfully.\n";
    }
}