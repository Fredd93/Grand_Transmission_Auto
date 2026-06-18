# WebDev 2 Upgrade Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Upgrade Grand Transmission Auto into a WebDev 2 compliant app: PHP PSR-4 MVC backend with JWT auth + REST API, Vue 3 SPA frontend with Pinia + Vue Router, all running under one Docker stack.

**Architecture:** PHP backend lives in `app/src/` under `GTA\` namespace (PSR-4), serves only `/api/*` routes through nginx. Vue 3 SPA in `frontend/` is compiled at build time and served as static files by nginx. JWT tokens carry role (`admin|employee|client`) and are validated by `AuthMiddleware` before every protected controller method.

**Tech Stack:** PHP 8 + `firebase/php-jwt` + Composer PSR-4 · Vue 3 + Vite + Pinia + Vue Router 4 + Axios + Tailwind CSS · MySQL/MariaDB · nginx · Docker Compose · Swagger UI

**Spec:** `docs/superpowers/specs/2026-06-18-webdev2-upgrade-design.md`

---

## PHASE 1 — Repository Setup

### Task 1: Clone old project into new repo

**Files:**
- Create: new local directory `693428_Fred_Farid_Webdev2_Final_assignment_Auto_store/`

- [ ] **Step 1: Clone the existing project as the base**

```bash
cd ~/Documents/GitHub
git clone https://github.com/Fredd93/Grand_Transmission_Auto.git 693428_Fred_Farid_Webdev2_Final_assignment_Auto_store
cd 693428_Fred_Farid_Webdev2_Final_assignment_Auto_store
```

- [ ] **Step 2: Point remote to the new repo**

```bash
git remote set-url origin https://github.com/Fredd93/693428_Fred_Farid_Webdev2_Final_assignment_Auto_store.git
git push -u origin main
```

- [ ] **Step 3: Add .gitignore entries**

Append to `.gitignore`:
```
frontend/node_modules/
frontend/dist/
app/vendor/
.env
.superpowers/
```

- [ ] **Step 4: Commit**

```bash
git add .gitignore
git commit -m "chore: init webdev2 repo from webdev1 base"
```

---

### Task 2: Scaffold new directory structure

**Files:**
- Create: `app/src/Controllers/`, `app/src/Models/`, `app/src/Middleware/`, `app/src/Routes/`
- Create: `frontend/` (placeholder until Task 14)
- Create: `app/docs/`

- [ ] **Step 1: Create PHP src directories**

```bash
mkdir -p app/src/Controllers
mkdir -p app/src/Models
mkdir -p app/src/Middleware
mkdir -p app/src/Routes
mkdir -p app/docs
```

- [ ] **Step 2: Create .gitkeep files so directories are tracked**

```bash
touch app/src/Controllers/.gitkeep
touch app/src/Models/.gitkeep
touch app/src/Middleware/.gitkeep
touch app/src/Routes/.gitkeep
touch app/docs/.gitkeep
```

- [ ] **Step 3: Commit**

```bash
git add app/src app/docs
git commit -m "chore: scaffold php src directory structure"
```

---

## PHASE 2 — PHP Backend

### Task 3: Update composer.json for PSR-4 autoloading

**Files:**
- Modify: `app/composer.json`

- [ ] **Step 1: Replace composer.json**

```json
{
    "require": {
        "phpmailer/phpmailer": "^6.8",
        "firebase/php-jwt": "^6.10"
    },
    "autoload": {
        "psr-4": {
            "GTA\\": "src/"
        }
    }
}
```

- [ ] **Step 2: Install dependencies**

```bash
cd app
composer require firebase/php-jwt
composer dump-autoload
cd ..
```

Expected: `vendor/firebase/php-jwt/` appears.

- [ ] **Step 3: Commit**

```bash
git add app/composer.json app/composer.lock app/vendor/
git commit -m "chore: add firebase/php-jwt, configure PSR-4 GTA namespace"
```

---

### Task 4: Create BaseModel

**Files:**
- Create: `app/src/Models/BaseModel.php`

- [ ] **Step 1: Write BaseModel**

```php
<?php
namespace GTA\Models;

use PDO;
use PDOException;

class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $host     = $_ENV['DB_HOST']     ?? getenv('DB_HOST');
        $name     = $_ENV['DB_NAME']     ?? getenv('DB_NAME');
        $user     = $_ENV['DB_USER']     ?? getenv('DB_USER');
        $pass     = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');
        $charset  = $_ENV['DB_CHARSET']  ?? getenv('DB_CHARSET') ?: 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$name;charset=$charset";

        $this->db = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    protected function paginate(string $sql, array $params, int $page, int $limit): array
    {
        $countSql = "SELECT COUNT(*) as total FROM ($sql) as sub";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare("$sql LIMIT :limit OFFSET :offset");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'meta' => [
                'total' => $total,
                'page'  => $page,
                'limit' => $limit,
                'pages' => (int) ceil($total / $limit),
            ],
        ];
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/src/Models/BaseModel.php
git commit -m "feat(backend): add BaseModel with PDO connection and paginate helper"
```

---

### Task 5: Create ResponseHelper

**Files:**
- Create: `app/src/Helpers/ResponseHelper.php`

- [ ] **Step 1: Write ResponseHelper**

```bash
mkdir -p app/src/Helpers
```

```php
<?php
namespace GTA\Helpers;

class ResponseHelper
{
    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function error(string $message, int $status = 400): void
    {
        self::json(['error' => $message], $status);
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/src/Helpers/ResponseHelper.php
git commit -m "feat(backend): add ResponseHelper"
```

---

### Task 6: Create AuthMiddleware (JWT)

**Files:**
- Create: `app/src/Middleware/AuthMiddleware.php`

- [ ] **Step 1: Write AuthMiddleware**

```php
<?php
namespace GTA\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GTA\Helpers\ResponseHelper;

class AuthMiddleware
{
    private static array $roleHierarchy = ['client' => 1, 'employee' => 2, 'admin' => 3];

    public static function require(string $minRole = 'client'): array
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!str_starts_with($header, 'Bearer ')) {
            ResponseHelper::error('Unauthorized', 401);
        }

        $token = substr($header, 7);
        $secret = $_ENV['APP_SECRET'] ?? getenv('APP_SECRET') ?: 'changeme_secret';

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            $payload = (array) $decoded;
        } catch (\Exception $e) {
            ResponseHelper::error('Invalid or expired token', 401);
        }

        $userRole  = $payload['role'] ?? 'client';
        $userLevel = self::$roleHierarchy[$userRole]  ?? 0;
        $minLevel  = self::$roleHierarchy[$minRole]   ?? 99;

        if ($userLevel < $minLevel) {
            ResponseHelper::error('Forbidden', 403);
        }

        return $payload;
    }

    public static function generateToken(array $user): string
    {
        $secret = $_ENV['APP_SECRET'] ?? getenv('APP_SECRET') ?: 'changeme_secret';

        $payload = [
            'sub'  => $user['id'],
            'role' => $user['role'],
            'name' => $user['name'],
            'exp'  => time() + 86400,
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/src/Middleware/AuthMiddleware.php
git commit -m "feat(backend): add JWT AuthMiddleware with role hierarchy"
```

---

### Task 7: Create UserModel

**Files:**
- Create: `app/src/Models/UserModel.php`

- [ ] **Step 1: Write UserModel**

```php
<?php
namespace GTA\Models;

class UserModel extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, string $email, string $passwordHash, string $role = 'client'): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
        );
        $stmt->execute([':name' => $name, ':email' => $email, ':password' => $passwordHash, ':role' => $role]);
        return (int) $this->db->lastInsertId();
    }

    public function listPaginated(int $page, int $limit): array
    {
        return $this->paginate(
            'SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC',
            [],
            $page,
            $limit
        );
    }

    public function update(int $id, array $data): bool
    {
        $allowed = ['name', 'email', 'role'];
        $sets = [];
        $params = [':id' => $id];
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $sets[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        if (empty($sets)) return false;
        $stmt = $this->db->prepare('UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = :id');
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
```

- [ ] **Step 2: Run the SQL migration to add role column**

```sql
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS role ENUM('admin','employee','client') NOT NULL DEFAULT 'client';

-- Create a default admin account (password: admin123)
INSERT INTO users (name, email, password, role)
VALUES ('Admin', 'admin@gta.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE role = 'admin';
```

Save this as `grand_transmission_auto_v2.sql` in the repo root, run it in phpMyAdmin or via:
```bash
docker exec -i <mysql_container> mysql -udeveloper -psecret123 grand_transmission_auto < grand_transmission_auto_v2.sql
```

- [ ] **Step 3: Commit**

```bash
git add app/src/Models/UserModel.php grand_transmission_auto_v2.sql
git commit -m "feat(backend): add UserModel + role migration SQL"
```

---

### Task 8: Port CarModel

**Files:**
- Create: `app/src/Models/CarModel.php`

- [ ] **Step 1: Write CarModel**

```php
<?php
namespace GTA\Models;

use PDO;

class CarModel extends BaseModel
{
    public function listPaginated(array $filters, int $page, int $limit): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['brand'])) {
            $where[] = 'brand = :brand';
            $params[':brand'] = $filters['brand'];
        }
        if (!empty($filters['year'])) {
            $where[] = 'year = :year';
            $params[':year'] = $filters['year'];
        }
        if (!empty($filters['transmission'])) {
            $where[] = 'transmission = :transmission';
            $params[':transmission'] = $filters['transmission'];
        }
        if (isset($filters['min_price'])) {
            $where[] = 'price >= :min_price';
            $params[':min_price'] = $filters['min_price'];
        }
        if (isset($filters['max_price'])) {
            $where[] = 'price <= :max_price';
            $params[':max_price'] = $filters['max_price'];
        }
        if (isset($filters['on_sale'])) {
            $where[] = 'on_sale = :on_sale';
            $params[':on_sale'] = $filters['on_sale'];
        }

        $sql = 'SELECT * FROM cars WHERE ' . implode(' AND ', $where) . ' ORDER BY id DESC';
        return $this->paginate($sql, $params, $page, $limit);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM cars WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO cars (brand, model, year, transmission, engine_spec, car_condition,
                description, color, price, on_sale, discount, lease_available, lease_terms,
                status, image_path)
            VALUES (:brand, :model, :year, :transmission, :engine_spec, :car_condition,
                :description, :color, :price, :on_sale, :discount, :lease_available, :lease_terms,
                :status, :image_path)
        ');
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $allowed = ['brand','model','year','transmission','engine_spec','car_condition',
                    'description','color','price','on_sale','discount','lease_available',
                    'lease_terms','status','image_path'];
        $sets = [];
        $params = [':id' => $id];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $sets[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        if (empty($sets)) return false;
        $stmt = $this->db->prepare('UPDATE cars SET ' . implode(', ', $sets) . ' WHERE id = :id');
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM cars WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function getFilterOptions(): array
    {
        return [
            'brands'        => $this->db->query('SELECT DISTINCT brand FROM cars ORDER BY brand')->fetchAll(PDO::FETCH_COLUMN),
            'years'         => $this->db->query('SELECT DISTINCT year FROM cars ORDER BY year DESC')->fetchAll(PDO::FETCH_COLUMN),
            'transmissions' => $this->db->query('SELECT DISTINCT transmission FROM cars')->fetchAll(PDO::FETCH_COLUMN),
            'price_bounds'  => $this->db->query('SELECT MIN(price) as min, MAX(price) as max FROM cars')->fetch(),
        ];
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/src/Models/CarModel.php
git commit -m "feat(backend): add namespaced CarModel with filtering + pagination"
```

---

### Task 9: Port OrderModel

**Files:**
- Create: `app/src/Models/OrderModel.php`

- [ ] **Step 1: Write OrderModel**

```php
<?php
namespace GTA\Models;

class OrderModel extends BaseModel
{
    public function listPaginated(int $page, int $limit, ?int $userId = null): array
    {
        if ($userId !== null) {
            $sql = 'SELECT o.*, c.brand, c.model FROM orders o JOIN cars c ON o.car_id = c.id WHERE o.user_id = :user_id ORDER BY o.created_at DESC';
            return $this->paginate($sql, [':user_id' => $userId], $page, $limit);
        }
        $sql = 'SELECT o.*, c.brand, c.model, u.name as client_name FROM orders o JOIN cars c ON o.car_id = c.id JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC';
        return $this->paginate($sql, [], $page, $limit);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, c.brand, c.model, u.name as client_name
             FROM orders o
             JOIN cars c ON o.car_id = c.id
             JOIN users u ON o.user_id = u.id
             WHERE o.id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO orders (user_id, car_id, order_type, status, notes)
             VALUES (:user_id, :car_id, :order_type, :status, :notes)'
        );
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE orders SET status = :status WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/src/Models/OrderModel.php
git commit -m "feat(backend): add namespaced OrderModel"
```

---

### Task 10: Create Controllers

**Files:**
- Create: `app/src/Controllers/AuthController.php`
- Create: `app/src/Controllers/CarController.php`
- Create: `app/src/Controllers/OrderController.php`
- Create: `app/src/Controllers/UserController.php`

- [ ] **Step 1: Write AuthController**

```php
<?php
namespace GTA\Controllers;

use GTA\Models\UserModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;

class AuthController
{
    private UserModel $users;

    public function __construct() { $this->users = new UserModel(); }

    public function login(): void
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $email    = trim($body['email']    ?? '');
        $password = trim($body['password'] ?? '');

        if (!$email || !$password) {
            ResponseHelper::error('Email and password are required', 400);
        }

        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            ResponseHelper::error('Invalid credentials', 401);
        }

        $token = AuthMiddleware::generateToken($user);
        ResponseHelper::json([
            'token' => $token,
            'user'  => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']],
        ]);
    }

    public function register(): void
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $name     = trim($body['name']     ?? '');
        $email    = trim($body['email']    ?? '');
        $password = trim($body['password'] ?? '');

        if (!$name || !$email || !$password) {
            ResponseHelper::error('Name, email and password are required', 400);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ResponseHelper::error('Invalid email', 400);
        }
        if ($this->users->findByEmail($email)) {
            ResponseHelper::error('Email already registered', 409);
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $id   = $this->users->create($name, $email, $hash);
        $user = $this->users->findById($id);
        $token = AuthMiddleware::generateToken($user);

        ResponseHelper::json([
            'token' => $token,
            'user'  => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']],
        ], 201);
    }
}
```

- [ ] **Step 2: Write CarController**

```php
<?php
namespace GTA\Controllers;

use GTA\Models\CarModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;

class CarController
{
    private CarModel $cars;

    public function __construct() { $this->cars = new CarModel(); }

    public function index(): void
    {
        $page    = max(1, (int)($_GET['page']  ?? 1));
        $limit   = min(50, max(1, (int)($_GET['limit'] ?? 12)));
        $filters = array_filter([
            'brand'        => $_GET['brand']        ?? null,
            'year'         => $_GET['year']          ?? null,
            'transmission' => $_GET['transmission']  ?? null,
            'min_price'    => isset($_GET['min_price']) ? (float)$_GET['min_price'] : null,
            'max_price'    => isset($_GET['max_price']) ? (float)$_GET['max_price'] : null,
            'on_sale'      => isset($_GET['on_sale'])   ? (int)$_GET['on_sale']     : null,
        ], fn($v) => $v !== null);

        ResponseHelper::json($this->cars->listPaginated($filters, $page, $limit));
    }

    public function filters(): void
    {
        ResponseHelper::json($this->cars->getFilterOptions());
    }

    public function show(int $id): void
    {
        $car = $this->cars->findById($id);
        $car ? ResponseHelper::json($car) : ResponseHelper::error('Car not found', 404);
    }

    public function store(): void
    {
        AuthMiddleware::require('employee');

        $requiredFields = ['brand','model','year','transmission','price','on_sale','discount','status'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) ResponseHelper::error("Missing field: $field", 400);
        }

        if (!isset($_FILES['image_path']) || $_FILES['image_path']['error'] !== UPLOAD_ERR_OK) {
            ResponseHelper::error('Image upload failed or missing', 400);
        }

        $originalName = basename($_FILES['image_path']['name']);
        $cleanName    = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $originalName);
        $uniqueName   = uniqid('car_', true) . '_' . $cleanName;
        $uploadPath   = __DIR__ . '/../../public/assets/images/' . $uniqueName;
        $relativePath = 'assets/images/' . $uniqueName;

        if (!move_uploaded_file($_FILES['image_path']['tmp_name'], $uploadPath)) {
            ResponseHelper::error('Failed to save image', 500);
        }

        $data = [
            ':brand'           => $_POST['brand'],
            ':model'           => $_POST['model'],
            ':year'            => $_POST['year'],
            ':transmission'    => $_POST['transmission'],
            ':engine_spec'     => $_POST['engine_spec']     ?? '',
            ':car_condition'   => $_POST['car_condition']   ?? '',
            ':description'     => $_POST['description']     ?? '',
            ':color'           => $_POST['color']           ?? '',
            ':price'           => $_POST['price'],
            ':on_sale'         => $_POST['on_sale'],
            ':discount'        => $_POST['discount'],
            ':lease_available' => ($_POST['lease_available'] ?? '') === 'yes' ? 1 : 0,
            ':lease_terms'     => $_POST['lease_terms']     ?? '',
            ':status'          => $_POST['status'],
            ':image_path'      => $relativePath,
        ];

        $id  = $this->cars->create($data);
        $car = $this->cars->findById($id);
        ResponseHelper::json($car, 201);
    }

    public function update(int $id): void
    {
        AuthMiddleware::require('employee');

        $car = $this->cars->findById($id);
        if (!$car) ResponseHelper::error('Car not found', 404);

        // Support multipart (with possible new image) or JSON
        if (!empty($_POST)) {
            $data = array_intersect_key($_POST, array_flip([
                'brand','model','year','transmission','engine_spec','car_condition',
                'description','color','price','on_sale','discount','lease_available','lease_terms','status'
            ]));

            if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
                $originalName = basename($_FILES['image_path']['name']);
                $cleanName    = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $originalName);
                $uniqueName   = uniqid('car_', true) . '_' . $cleanName;
                $uploadPath   = __DIR__ . '/../../public/assets/images/' . $uniqueName;
                move_uploaded_file($_FILES['image_path']['tmp_name'], $uploadPath);
                $data['image_path'] = 'assets/images/' . $uniqueName;
            }
        } else {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        $this->cars->update($id, $data);
        ResponseHelper::json($this->cars->findById($id));
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::require('admin');
        $car = $this->cars->findById($id);
        if (!$car) ResponseHelper::error('Car not found', 404);
        $this->cars->delete($id);
        ResponseHelper::json(['message' => 'Car deleted']);
    }
}
```

- [ ] **Step 3: Write OrderController**

```php
<?php
namespace GTA\Controllers;

use GTA\Models\OrderModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;

class OrderController
{
    private OrderModel $orders;

    public function __construct() { $this->orders = new OrderModel(); }

    public function index(): void
    {
        $auth   = AuthMiddleware::require('client');
        $page   = max(1, (int)($_GET['page']  ?? 1));
        $limit  = min(50, max(1, (int)($_GET['limit'] ?? 15)));
        $userId = in_array($auth['role'], ['employee','admin']) ? null : (int)$auth['sub'];
        ResponseHelper::json($this->orders->listPaginated($page, $limit, $userId));
    }

    public function show(int $id): void
    {
        $auth  = AuthMiddleware::require('client');
        $order = $this->orders->findById($id);
        if (!$order) ResponseHelper::error('Order not found', 404);

        if ($auth['role'] === 'client' && (int)$order['user_id'] !== (int)$auth['sub']) {
            ResponseHelper::error('Forbidden', 403);
        }
        ResponseHelper::json($order);
    }

    public function store(): void
    {
        $auth = AuthMiddleware::require('client');
        $body = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($body['car_id']) || empty($body['order_type'])) {
            ResponseHelper::error('car_id and order_type are required', 400);
        }

        $data = [
            ':user_id'    => (int)$auth['sub'],
            ':car_id'     => (int)$body['car_id'],
            ':order_type' => $body['order_type'],
            ':status'     => 'pending',
            ':notes'      => $body['notes'] ?? '',
        ];

        $id    = $this->orders->create($data);
        $order = $this->orders->findById($id);
        ResponseHelper::json($order, 201);
    }

    public function update(int $id): void
    {
        AuthMiddleware::require('employee');
        $order = $this->orders->findById($id);
        if (!$order) ResponseHelper::error('Order not found', 404);

        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $status = $body['status'] ?? '';
        $allowed = ['pending','approved','denied','completed'];
        if (!in_array($status, $allowed)) ResponseHelper::error('Invalid status', 400);

        $this->orders->updateStatus($id, $status);
        ResponseHelper::json($this->orders->findById($id));
    }
}
```

- [ ] **Step 4: Write UserController**

```php
<?php
namespace GTA\Controllers;

use GTA\Models\UserModel;
use GTA\Middleware\AuthMiddleware;
use GTA\Helpers\ResponseHelper;

class UserController
{
    private UserModel $users;

    public function __construct() { $this->users = new UserModel(); }

    public function index(): void
    {
        AuthMiddleware::require('admin');
        $page  = max(1, (int)($_GET['page']  ?? 1));
        $limit = min(50, max(1, (int)($_GET['limit'] ?? 15)));
        ResponseHelper::json($this->users->listPaginated($page, $limit));
    }

    public function show(int $id): void
    {
        $auth = AuthMiddleware::require('client');
        if ($auth['role'] !== 'admin' && (int)$auth['sub'] !== $id) {
            ResponseHelper::error('Forbidden', 403);
        }
        $user = $this->users->findById($id);
        $user ? ResponseHelper::json($user) : ResponseHelper::error('User not found', 404);
    }

    public function update(int $id): void
    {
        $auth = AuthMiddleware::require('client');
        if ($auth['role'] !== 'admin' && (int)$auth['sub'] !== $id) {
            ResponseHelper::error('Forbidden', 403);
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];

        // Only admin can change roles
        if (isset($body['role']) && $auth['role'] !== 'admin') {
            ResponseHelper::error('Only admins can change roles', 403);
        }

        $this->users->update($id, $body);
        ResponseHelper::json($this->users->findById($id));
    }

    public function destroy(int $id): void
    {
        AuthMiddleware::require('admin');
        $user = $this->users->findById($id);
        if (!$user) ResponseHelper::error('User not found', 404);
        $this->users->delete($id);
        ResponseHelper::json(['message' => 'User deleted']);
    }
}
```

- [ ] **Step 5: Commit**

```bash
git add app/src/Controllers/
git commit -m "feat(backend): add Auth/Car/Order/User controllers with JWT protection"
```

---

### Task 11: Create Routes and Entry Point

**Files:**
- Create: `app/src/Routes/api.php`
- Create: `app/src/Routes/auth.php`
- Modify: `app/public/index.php`
- Modify: `app/public/lib/Route.php` (keep existing, just verify it works)

- [ ] **Step 1: Write auth routes**

```php
<?php
// app/src/Routes/auth.php
use GTA\Controllers\AuthController;

Route::add('/api/auth/login',    fn() => (new AuthController())->login(),    'POST');
Route::add('/api/auth/register', fn() => (new AuthController())->register(), 'POST');
```

- [ ] **Step 2: Write API routes**

```php
<?php
// app/src/Routes/api.php
use GTA\Controllers\CarController;
use GTA\Controllers\OrderController;
use GTA\Controllers\UserController;

// Cars
Route::add('/api/cars',               fn() => (new CarController())->index(),   'GET');
Route::add('/api/cars/filters',       fn() => (new CarController())->filters(), 'GET');
Route::add('/api/cars/([0-9]+)',       fn($id) => (new CarController())->show((int)$id),    'GET');
Route::add('/api/cars',               fn() => (new CarController())->store(),   'POST');
Route::add('/api/cars/([0-9]+)',       fn($id) => (new CarController())->update((int)$id),  'PUT');
Route::add('/api/cars/([0-9]+)',       fn($id) => (new CarController())->destroy((int)$id), 'DELETE');

// Orders
Route::add('/api/orders',             fn() => (new OrderController())->index(),  'GET');
Route::add('/api/orders/([0-9]+)',     fn($id) => (new OrderController())->show((int)$id),   'GET');
Route::add('/api/orders',             fn() => (new OrderController())->store(),  'POST');
Route::add('/api/orders/([0-9]+)',     fn($id) => (new OrderController())->update((int)$id), 'PUT');

// Users
Route::add('/api/users',              fn() => (new UserController())->index(),   'GET');
Route::add('/api/users/([0-9]+)',      fn($id) => (new UserController())->show((int)$id),    'GET');
Route::add('/api/users/([0-9]+)',      fn($id) => (new UserController())->update((int)$id),  'PUT');
Route::add('/api/users/([0-9]+)',      fn($id) => (new UserController())->destroy((int)$id), 'DELETE');
```

- [ ] **Step 3: Update index.php**

```php
<?php
// app/public/index.php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../public/lib/Route.php';
require_once __DIR__ . '/../public/lib/env.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../src/Routes/auth.php';
require_once __DIR__ . '/../src/Routes/api.php';

Route::run('/');
```

- [ ] **Step 4: Commit**

```bash
git add app/src/Routes/ app/public/index.php
git commit -m "feat(backend): wire up REST routes and CORS headers in entry point"
```

---

## PHASE 3 — Vue 3 Frontend

### Task 12: Scaffold Vue 3 project

**Files:**
- Create: `frontend/` (entire Vite + Vue project)

- [ ] **Step 1: Scaffold with Vite**

```bash
cd 693428_Fred_Farid_Webdev2_Final_assignment_Auto_store
npm create vite@latest frontend -- --template vue
cd frontend
npm install
npm install vue-router@4 pinia axios
npm install -D tailwindcss @tailwindcss/vite
```

- [ ] **Step 2: Configure Tailwind — update `vite.config.js`**

```js
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [vue(), tailwindcss()],
  server: {
    proxy: {
      '/api': 'http://localhost:80'
    }
  }
})
```

- [ ] **Step 3: Replace `src/style.css`**

```css
@import "tailwindcss";
```

- [ ] **Step 4: Update `src/main.js`**

```js
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router/index.js'
import App from './App.vue'
import './style.css'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')
```

- [ ] **Step 5: Replace `src/App.vue`**

```vue
<template>
  <Navbar />
  <main class="min-h-screen bg-gray-950 text-white">
    <RouterView />
  </main>
  <Footer />
</template>

<script setup>
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
</script>
```

- [ ] **Step 6: Commit**

```bash
cd ..
git add frontend/
git commit -m "feat(frontend): scaffold Vue 3 + Vite + Tailwind + Pinia + Vue Router"
```

---

### Task 13: Axios client + Pinia stores

**Files:**
- Create: `frontend/src/api/client.js`
- Create: `frontend/src/stores/auth.js`
- Create: `frontend/src/stores/cars.js`

- [ ] **Step 1: Write Axios client**

```bash
mkdir -p frontend/src/api
```

```js
// frontend/src/api/client.js
import axios from 'axios'
import { useAuthStore } from '../stores/auth.js'
import router from '../router/index.js'

const client = axios.create({ baseURL: '/api' })

client.interceptors.request.use(config => {
  const auth = useAuthStore()
  if (auth.token) config.headers.Authorization = `Bearer ${auth.token}`
  return config
})

client.interceptors.response.use(
  res => res,
  err => {
    if (err.response?.status === 401) {
      useAuthStore().logout()
      router.push('/login')
    }
    return Promise.reject(err)
  }
)

export default client
```

- [ ] **Step 2: Write auth store**

```bash
mkdir -p frontend/src/stores
```

```js
// frontend/src/stores/auth.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import client from '../api/client.js'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || null)
  const user  = ref(JSON.parse(localStorage.getItem('user') || 'null'))

  const isLoggedIn  = computed(() => !!token.value)
  const isAdmin     = computed(() => user.value?.role === 'admin')
  const isEmployee  = computed(() => ['admin','employee'].includes(user.value?.role))

  function _persist(t, u) {
    token.value = t
    user.value  = u
    localStorage.setItem('token', t)
    localStorage.setItem('user',  JSON.stringify(u))
  }

  async function login(email, password) {
    const { data } = await client.post('/auth/login', { email, password })
    _persist(data.token, data.user)
    return data.user
  }

  async function register(name, email, password) {
    const { data } = await client.post('/auth/register', { name, email, password })
    _persist(data.token, data.user)
    return data.user
  }

  function logout() {
    token.value = null
    user.value  = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  return { token, user, isLoggedIn, isAdmin, isEmployee, login, register, logout }
})
```

- [ ] **Step 3: Write cars store**

```js
// frontend/src/stores/cars.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import client from '../api/client.js'

export const useCarsStore = defineStore('cars', () => {
  const cars       = ref([])
  const meta       = ref({})
  const filters    = ref({})
  const filterOpts = ref({})

  async function fetchCars(params = {}) {
    const { data } = await client.get('/cars', { params })
    cars.value = data.data
    meta.value = data.meta
  }

  async function fetchFilterOptions() {
    const { data } = await client.get('/cars/filters')
    filterOpts.value = data
  }

  return { cars, meta, filters, filterOpts, fetchCars, fetchFilterOptions }
})
```

- [ ] **Step 4: Commit**

```bash
git add frontend/src/api/ frontend/src/stores/
git commit -m "feat(frontend): add Axios client with JWT interceptor + Pinia stores"
```

---

### Task 14: Vue Router with guards

**Files:**
- Create: `frontend/src/router/index.js`

- [ ] **Step 1: Write router**

```bash
mkdir -p frontend/src/router
```

```js
// frontend/src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const routes = [
  { path: '/',              component: () => import('../views/HomeView.vue') },
  { path: '/cars',          component: () => import('../views/CarsView.vue') },
  { path: '/cars/:id',      component: () => import('../views/CarDetailView.vue') },
  { path: '/login',         component: () => import('../views/LoginView.vue') },
  { path: '/register',      component: () => import('../views/RegisterView.vue') },
  {
    path: '/dashboard',
    component: () => import('../views/DashboardView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/cars',
    component: () => import('../views/AdminCarsView.vue'),
    meta: { requiresAuth: true, requiresRole: 'employee' }
  },
  {
    path: '/admin/orders',
    component: () => import('../views/AdminOrdersView.vue'),
    meta: { requiresAuth: true, requiresRole: 'employee' }
  },
  {
    path: '/admin/users',
    component: () => import('../views/AdminUsersView.vue'),
    meta: { requiresAuth: true, requiresRole: 'admin' }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isLoggedIn) return '/login'

  if (to.meta.requiresRole === 'admin'    && !auth.isAdmin)    return '/dashboard'
  if (to.meta.requiresRole === 'employee' && !auth.isEmployee) return '/dashboard'
})

export default router
```

- [ ] **Step 2: Commit**

```bash
git add frontend/src/router/
git commit -m "feat(frontend): add Vue Router with auth + role guards"
```

---

### Task 15: Shared components (Navbar, Footer, CarCard, Pagination, StatusBadge)

**Files:**
- Create: `frontend/src/components/Navbar.vue`
- Create: `frontend/src/components/Footer.vue`
- Create: `frontend/src/components/CarCard.vue`
- Create: `frontend/src/components/Pagination.vue`
- Create: `frontend/src/components/StatusBadge.vue`

- [ ] **Step 1: Write Navbar.vue**

```vue
<template>
  <nav class="bg-gray-900 border-b border-gray-800 px-6 py-3 flex items-center justify-between">
    <RouterLink to="/" class="text-white font-bold text-xl tracking-wide">
      GT <span class="text-red-500">Auto</span>
    </RouterLink>

    <div class="flex items-center gap-6 text-sm">
      <RouterLink to="/cars" class="text-gray-300 hover:text-white">Inventory</RouterLink>

      <template v-if="auth.isLoggedIn">
        <RouterLink v-if="auth.isEmployee" to="/admin/cars"   class="text-gray-300 hover:text-white">Manage Cars</RouterLink>
        <RouterLink v-if="auth.isEmployee" to="/admin/orders" class="text-gray-300 hover:text-white">Orders</RouterLink>
        <RouterLink v-if="auth.isAdmin"    to="/admin/users"  class="text-gray-300 hover:text-white">Users</RouterLink>
        <RouterLink to="/dashboard" class="text-gray-300 hover:text-white">Dashboard</RouterLink>
        <button @click="handleLogout" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
          Logout
        </button>
      </template>
      <template v-else>
        <RouterLink to="/login"    class="text-gray-300 hover:text-white">Login</RouterLink>
        <RouterLink to="/register" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">Register</RouterLink>
      </template>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '../stores/auth.js'
import { useRouter } from 'vue-router'
const auth   = useAuthStore()
const router = useRouter()
function handleLogout() { auth.logout(); router.push('/') }
</script>
```

- [ ] **Step 2: Write Footer.vue**

```vue
<template>
  <footer class="bg-gray-900 border-t border-gray-800 text-center text-gray-500 text-sm py-6">
    © 2025 Grand Transmission Auto. All rights reserved.
  </footer>
</template>
```

- [ ] **Step 3: Write CarCard.vue**

```vue
<template>
  <RouterLink :to="`/cars/${car.id}`"
    class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden hover:border-red-500 transition group">
    <img :src="`/${car.image_path}`" :alt="`${car.brand} ${car.model}`"
      class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300" />
    <div class="p-4">
      <h3 class="text-white font-semibold">{{ car.brand }} {{ car.model }}</h3>
      <p class="text-gray-400 text-sm mt-1">{{ car.year }} · {{ car.transmission }}</p>
      <div class="flex items-center justify-between mt-3">
        <span class="text-red-400 font-bold text-lg">€{{ Number(car.price).toLocaleString() }}</span>
        <span v-if="car.on_sale" class="bg-red-600 text-white text-xs px-2 py-0.5 rounded">SALE</span>
      </div>
    </div>
  </RouterLink>
</template>

<script setup>
defineProps({ car: Object })
</script>
```

- [ ] **Step 4: Write Pagination.vue**

```vue
<template>
  <div v-if="meta.pages > 1" class="flex items-center justify-center gap-2 mt-8">
    <button @click="$emit('change', meta.page - 1)" :disabled="meta.page <= 1"
      class="px-3 py-1 bg-gray-800 text-white rounded disabled:opacity-40">‹</button>

    <span class="text-gray-400 text-sm">Page {{ meta.page }} of {{ meta.pages }}</span>

    <button @click="$emit('change', meta.page + 1)" :disabled="meta.page >= meta.pages"
      class="px-3 py-1 bg-gray-800 text-white rounded disabled:opacity-40">›</button>
  </div>
</template>

<script setup>
defineProps({ meta: Object })
defineEmits(['change'])
</script>
```

- [ ] **Step 5: Write StatusBadge.vue**

```vue
<template>
  <span :class="colours[status] ?? 'bg-gray-700 text-gray-200'"
    class="text-xs px-2 py-0.5 rounded-full font-medium capitalize">
    {{ status }}
  </span>
</template>

<script setup>
defineProps({ status: String })
const colours = {
  pending:   'bg-yellow-700 text-yellow-200',
  approved:  'bg-green-700  text-green-200',
  denied:    'bg-red-700    text-red-200',
  completed: 'bg-blue-700   text-blue-200',
}
</script>
```

- [ ] **Step 6: Commit**

```bash
git add frontend/src/components/
git commit -m "feat(frontend): add Navbar, Footer, CarCard, Pagination, StatusBadge components"
```

---

### Task 16: Public views (Home, Cars, CarDetail, Login, Register)

**Files:**
- Create: `frontend/src/views/HomeView.vue`
- Create: `frontend/src/views/CarsView.vue`
- Create: `frontend/src/views/CarDetailView.vue`
- Create: `frontend/src/views/LoginView.vue`
- Create: `frontend/src/views/RegisterView.vue`

- [ ] **Step 1: Write HomeView.vue**

```vue
<template>
  <section class="px-6 py-16 max-w-7xl mx-auto">
    <div class="text-center mb-12">
      <h1 class="text-5xl font-bold text-white mb-4">Find Your <span class="text-red-500">Dream Car</span></h1>
      <p class="text-gray-400 text-lg">Browse our premium selection of vehicles available for purchase or lease</p>
      <RouterLink to="/cars" class="inline-block mt-6 bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold">
        View Inventory
      </RouterLink>
    </div>

    <div v-if="loading" class="text-center text-gray-400">Loading...</div>
    <template v-else>
      <h2 class="text-2xl font-semibold text-white mb-4">Featured Cars</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <CarCard v-for="car in featured" :key="car.id" :car="car" />
      </div>
    </template>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import CarCard from '../components/CarCard.vue'
import client from '../api/client.js'

const featured = ref([])
const loading  = ref(true)

onMounted(async () => {
  const { data } = await client.get('/cars', { params: { limit: 8 } })
  featured.value = data.data
  loading.value  = false
})
</script>
```

- [ ] **Step 2: Write CarsView.vue**

```vue
<template>
  <div class="max-w-7xl mx-auto px-6 py-10 flex gap-8">
    <!-- Sidebar filters -->
    <aside class="w-64 shrink-0">
      <h2 class="text-white font-semibold mb-4">Filter</h2>

      <div class="space-y-4 text-sm">
        <div>
          <label class="text-gray-400 block mb-1">Brand</label>
          <select v-model="filters.brand" @change="load(1)"
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-2 py-1">
            <option value="">All</option>
            <option v-for="b in filterOpts.brands" :key="b">{{ b }}</option>
          </select>
        </div>

        <div>
          <label class="text-gray-400 block mb-1">Year</label>
          <select v-model="filters.year" @change="load(1)"
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-2 py-1">
            <option value="">All</option>
            <option v-for="y in filterOpts.years" :key="y">{{ y }}</option>
          </select>
        </div>

        <div>
          <label class="text-gray-400 block mb-1">Transmission</label>
          <select v-model="filters.transmission" @change="load(1)"
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-2 py-1">
            <option value="">All</option>
            <option v-for="t in filterOpts.transmissions" :key="t">{{ t }}</option>
          </select>
        </div>

        <div>
          <label class="text-gray-400 block mb-1">Max Price (€)</label>
          <input v-model="filters.max_price" @change="load(1)" type="number"
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-2 py-1" />
        </div>

        <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
          <input v-model="filters.on_sale" @change="load(1)" type="checkbox" class="accent-red-500" />
          On Sale only
        </label>
      </div>
    </aside>

    <!-- Grid -->
    <div class="flex-1">
      <div v-if="loading" class="text-gray-400">Loading...</div>
      <template v-else>
        <p class="text-gray-400 text-sm mb-4">{{ meta.total }} vehicles found</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <CarCard v-for="car in cars" :key="car.id" :car="car" />
        </div>
        <Pagination :meta="meta" @change="load" />
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import CarCard   from '../components/CarCard.vue'
import Pagination from '../components/Pagination.vue'
import client from '../api/client.js'

const cars       = ref([])
const meta       = ref({})
const filterOpts = ref({ brands: [], years: [], transmissions: [] })
const filters    = ref({ brand: '', year: '', transmission: '', max_price: '', on_sale: false })
const loading    = ref(true)

async function load(page = 1) {
  loading.value = true
  const params = { page, limit: 12, ...Object.fromEntries(Object.entries(filters.value).filter(([,v]) => v !== '' && v !== false)) }
  if (filters.value.on_sale) params.on_sale = 1
  const { data } = await client.get('/cars', { params })
  cars.value    = data.data
  meta.value    = data.meta
  loading.value = false
}

onMounted(async () => {
  const { data } = await client.get('/cars/filters')
  filterOpts.value = data
  await load()
})
</script>
```

- [ ] **Step 3: Write CarDetailView.vue**

```vue
<template>
  <div class="max-w-5xl mx-auto px-6 py-10">
    <div v-if="loading" class="text-gray-400">Loading...</div>
    <div v-else-if="!car" class="text-red-400">Car not found.</div>
    <template v-else>
      <div class="grid md:grid-cols-2 gap-10">
        <img :src="`/${car.image_path}`" :alt="`${car.brand} ${car.model}`"
          class="w-full rounded-xl object-cover" />

        <div>
          <h1 class="text-3xl font-bold text-white mb-2">{{ car.brand }} {{ car.model }}</h1>
          <p class="text-gray-400 mb-4">{{ car.year }} · {{ car.transmission }} · {{ car.color }}</p>
          <p class="text-gray-300 mb-6">{{ car.description }}</p>

          <div class="text-2xl font-bold text-red-400 mb-6">€{{ Number(car.price).toLocaleString() }}</div>

          <div class="flex gap-3">
            <button @click="placeOrder('purchase')"
              class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-semibold">
              Buy Now
            </button>
            <button v-if="car.lease_available" @click="placeOrder('lease')"
              class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-lg font-semibold">
              Lease
            </button>
          </div>

          <p v-if="orderMsg" class="mt-4 text-green-400 text-sm">{{ orderMsg }}</p>
          <p v-if="orderErr" class="mt-4 text-red-400 text-sm">{{ orderErr }}</p>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'
import client from '../api/client.js'

const route  = useRoute()
const router = useRouter()
const auth   = useAuthStore()
const car     = ref(null)
const loading = ref(true)
const orderMsg = ref('')
const orderErr = ref('')

onMounted(async () => {
  try {
    const { data } = await client.get(`/cars/${route.params.id}`)
    car.value = data
  } catch { car.value = null }
  loading.value = false
})

async function placeOrder(type) {
  if (!auth.isLoggedIn) { router.push('/login'); return }
  try {
    await client.post('/orders', { car_id: car.value.id, order_type: type })
    orderMsg.value = `${type === 'purchase' ? 'Purchase' : 'Lease'} request submitted!`
  } catch (e) {
    orderErr.value = e.response?.data?.error ?? 'Failed to place order'
  }
}
</script>
```

- [ ] **Step 4: Write LoginView.vue**

```vue
<template>
  <div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 w-full max-w-md">
      <h2 class="text-2xl font-bold text-white mb-6 text-center">Sign In</h2>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="text-gray-400 text-sm block mb-1">Email</label>
          <input v-model="email" type="email" required
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
        </div>
        <div>
          <label class="text-gray-400 text-sm block mb-1">Password</label>
          <input v-model="password" type="password" required
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
        </div>

        <p v-if="error" class="text-red-400 text-sm">{{ error }}</p>

        <button type="submit" :disabled="loading"
          class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold disabled:opacity-50">
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>

      <p class="text-center text-gray-500 text-sm mt-4">
        No account? <RouterLink to="/register" class="text-red-400 hover:text-red-300">Register</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const auth     = useAuthStore()
const router   = useRouter()
const email    = ref('')
const password = ref('')
const loading  = ref(false)
const error    = ref('')

async function submit() {
  loading.value = true
  error.value   = ''
  try {
    const user = await auth.login(email.value, password.value)
    router.push(['admin','employee'].includes(user.role) ? '/admin/cars' : '/dashboard')
  } catch (e) {
    error.value = e.response?.data?.error ?? 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>
```

- [ ] **Step 5: Write RegisterView.vue**

```vue
<template>
  <div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 w-full max-w-md">
      <h2 class="text-2xl font-bold text-white mb-6 text-center">Create Account</h2>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="text-gray-400 text-sm block mb-1">Full Name</label>
          <input v-model="name" type="text" required
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
        </div>
        <div>
          <label class="text-gray-400 text-sm block mb-1">Email</label>
          <input v-model="email" type="email" required
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
        </div>
        <div>
          <label class="text-gray-400 text-sm block mb-1">Password</label>
          <input v-model="password" type="password" required minlength="6"
            class="w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500" />
        </div>

        <p v-if="error" class="text-red-400 text-sm">{{ error }}</p>

        <button type="submit" :disabled="loading"
          class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-semibold disabled:opacity-50">
          {{ loading ? 'Creating...' : 'Create Account' }}
        </button>
      </form>

      <p class="text-center text-gray-500 text-sm mt-4">
        Already have an account? <RouterLink to="/login" class="text-red-400 hover:text-red-300">Sign in</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const auth     = useAuthStore()
const router   = useRouter()
const name     = ref('')
const email    = ref('')
const password = ref('')
const loading  = ref(false)
const error    = ref('')

async function submit() {
  loading.value = true
  error.value   = ''
  try {
    await auth.register(name.value, email.value, password.value)
    router.push('/dashboard')
  } catch (e) {
    error.value = e.response?.data?.error ?? 'Registration failed'
  } finally {
    loading.value = false
  }
}
</script>
```

- [ ] **Step 6: Commit**

```bash
git add frontend/src/views/HomeView.vue frontend/src/views/CarsView.vue \
        frontend/src/views/CarDetailView.vue frontend/src/views/LoginView.vue \
        frontend/src/views/RegisterView.vue
git commit -m "feat(frontend): add public views (Home, Cars, CarDetail, Login, Register)"
```

---

### Task 17: Protected views (Dashboard, AdminCars, AdminOrders, AdminUsers)

**Files:**
- Create: `frontend/src/views/DashboardView.vue`
- Create: `frontend/src/views/AdminCarsView.vue`
- Create: `frontend/src/views/AdminOrdersView.vue`
- Create: `frontend/src/views/AdminUsersView.vue`

- [ ] **Step 1: Write DashboardView.vue**

```vue
<template>
  <div class="max-w-4xl mx-auto px-6 py-10">
    <h1 class="text-3xl font-bold text-white mb-2">Welcome, {{ auth.user?.name }}</h1>
    <p class="text-gray-400 mb-8">Role: <span class="capitalize text-red-400">{{ auth.user?.role }}</span></p>

    <h2 class="text-xl font-semibold text-white mb-4">My Orders</h2>

    <div v-if="loading" class="text-gray-400">Loading...</div>
    <div v-else-if="!orders.length" class="text-gray-500">No orders yet.</div>
    <div v-else class="space-y-3">
      <div v-for="o in orders" :key="o.id"
        class="bg-gray-900 border border-gray-800 rounded-lg p-4 flex justify-between items-center">
        <div>
          <p class="text-white font-medium">{{ o.brand }} {{ o.model }}</p>
          <p class="text-gray-400 text-sm capitalize">{{ o.order_type }} · {{ new Date(o.created_at).toLocaleDateString() }}</p>
        </div>
        <StatusBadge :status="o.status" />
      </div>
    </div>
    <Pagination :meta="meta" @change="load" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth.js'
import StatusBadge from '../components/StatusBadge.vue'
import Pagination  from '../components/Pagination.vue'
import client from '../api/client.js'

const auth    = useAuthStore()
const orders  = ref([])
const meta    = ref({})
const loading = ref(true)

async function load(page = 1) {
  const { data } = await client.get('/orders', { params: { page, limit: 10 } })
  orders.value  = data.data
  meta.value    = data.meta
  loading.value = false
}

onMounted(() => load())
</script>
```

- [ ] **Step 2: Write AdminCarsView.vue**

```vue
<template>
  <div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-white">Car Inventory</h1>
      <button @click="openAdd" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">+ Add Car</button>
    </div>

    <div v-if="loading" class="text-gray-400">Loading...</div>
    <table v-else class="w-full text-sm text-left text-gray-300">
      <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
        <tr>
          <th class="px-4 py-3">Car</th>
          <th class="px-4 py-3">Year</th>
          <th class="px-4 py-3">Price</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="car in cars" :key="car.id" class="border-b border-gray-800 hover:bg-gray-900">
          <td class="px-4 py-3 font-medium text-white">{{ car.brand }} {{ car.model }}</td>
          <td class="px-4 py-3">{{ car.year }}</td>
          <td class="px-4 py-3">€{{ Number(car.price).toLocaleString() }}</td>
          <td class="px-4 py-3 capitalize">{{ car.status }}</td>
          <td class="px-4 py-3 flex gap-2">
            <button @click="openEdit(car)" class="text-blue-400 hover:text-blue-300">Edit</button>
            <button @click="deleteCar(car.id)" class="text-red-400 hover:text-red-300">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <Pagination :meta="meta" @change="load" />

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
      <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <h2 class="text-white font-bold text-lg mb-4">{{ editing ? 'Edit Car' : 'Add Car' }}</h2>
        <form @submit.prevent="submitCar" class="space-y-3 text-sm">
          <div class="grid grid-cols-2 gap-3">
            <div><label class="text-gray-400 block mb-1">Brand</label>
              <input v-model="form.brand" required class="input-field" /></div>
            <div><label class="text-gray-400 block mb-1">Model</label>
              <input v-model="form.model" required class="input-field" /></div>
            <div><label class="text-gray-400 block mb-1">Year</label>
              <input v-model="form.year" type="number" required class="input-field" /></div>
            <div><label class="text-gray-400 block mb-1">Transmission</label>
              <input v-model="form.transmission" required class="input-field" /></div>
            <div><label class="text-gray-400 block mb-1">Price (€)</label>
              <input v-model="form.price" type="number" required class="input-field" /></div>
            <div><label class="text-gray-400 block mb-1">Status</label>
              <select v-model="form.status" class="input-field">
                <option>available</option><option>sold</option><option>reserved</option>
              </select></div>
          </div>
          <div><label class="text-gray-400 block mb-1">Description</label>
            <textarea v-model="form.description" rows="2" class="input-field w-full"></textarea></div>
          <div><label class="text-gray-400 block mb-1">Image</label>
            <input type="file" @change="e => form.imageFile = e.target.files[0]" accept="image/*" class="text-gray-300" /></div>
          <div class="flex gap-3 mt-2">
            <label class="flex items-center gap-2 text-gray-300">
              <input v-model="form.on_sale" type="checkbox" class="accent-red-500" /> On Sale
            </label>
            <label class="flex items-center gap-2 text-gray-300">
              <input v-model="form.lease_available" type="checkbox" class="accent-red-500" /> Lease Available
            </label>
          </div>
          <p v-if="formErr" class="text-red-400">{{ formErr }}</p>
          <div class="flex gap-3 pt-2">
            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">Save</button>
            <button type="button" @click="showModal = false" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Pagination from '../components/Pagination.vue'
import client from '../api/client.js'

const cars      = ref([])
const meta      = ref({})
const loading   = ref(true)
const showModal = ref(false)
const editing   = ref(null)
const formErr   = ref('')
const form      = ref({})

async function load(page = 1) {
  loading.value = true
  const { data } = await client.get('/cars', { params: { page, limit: 15 } })
  cars.value    = data.data
  meta.value    = data.meta
  loading.value = false
}

function openAdd() {
  editing.value = null
  form.value    = { brand:'', model:'', year:'', transmission:'', price:'', status:'available', description:'', on_sale:false, lease_available:false, imageFile:null }
  showModal.value = true
}

function openEdit(car) {
  editing.value = car.id
  form.value    = { ...car, on_sale: !!car.on_sale, lease_available: !!car.lease_available, imageFile: null }
  showModal.value = true
}

async function deleteCar(id) {
  if (!confirm('Delete this car?')) return
  await client.delete(`/cars/${id}`)
  await load()
}

async function submitCar() {
  formErr.value = ''
  const fd = new FormData()
  Object.entries(form.value).forEach(([k, v]) => {
    if (k === 'imageFile') { if (v) fd.append('image_path', v) }
    else fd.append(k, v === true ? 'yes' : v === false ? 'no' : v)
  })
  try {
    if (editing.value) {
      await client.put(`/cars/${editing.value}`, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    } else {
      await client.post('/cars', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    }
    showModal.value = false
    await load()
  } catch (e) {
    formErr.value = e.response?.data?.error ?? 'Save failed'
  }
}

onMounted(() => load())
</script>

<style scoped>
.input-field {
  @apply w-full bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:border-red-500;
}
</style>
```

- [ ] **Step 3: Write AdminOrdersView.vue**

```vue
<template>
  <div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-2xl font-bold text-white mb-6">All Orders</h1>

    <div v-if="loading" class="text-gray-400">Loading...</div>
    <table v-else class="w-full text-sm text-left text-gray-300">
      <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
        <tr>
          <th class="px-4 py-3">#</th>
          <th class="px-4 py-3">Client</th>
          <th class="px-4 py-3">Car</th>
          <th class="px-4 py-3">Type</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Date</th>
          <th class="px-4 py-3">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="o in orders" :key="o.id" class="border-b border-gray-800 hover:bg-gray-900">
          <td class="px-4 py-3">{{ o.id }}</td>
          <td class="px-4 py-3">{{ o.client_name }}</td>
          <td class="px-4 py-3">{{ o.brand }} {{ o.model }}</td>
          <td class="px-4 py-3 capitalize">{{ o.order_type }}</td>
          <td class="px-4 py-3"><StatusBadge :status="o.status" /></td>
          <td class="px-4 py-3">{{ new Date(o.created_at).toLocaleDateString() }}</td>
          <td class="px-4 py-3">
            <select @change="e => updateStatus(o.id, e.target.value)" :value="o.status"
              class="bg-gray-800 border border-gray-700 text-white rounded px-2 py-1 text-xs">
              <option>pending</option>
              <option>approved</option>
              <option>denied</option>
              <option>completed</option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
    <Pagination :meta="meta" @change="load" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import StatusBadge from '../components/StatusBadge.vue'
import Pagination  from '../components/Pagination.vue'
import client from '../api/client.js'

const orders  = ref([])
const meta    = ref({})
const loading = ref(true)

async function load(page = 1) {
  loading.value = true
  const { data } = await client.get('/orders', { params: { page, limit: 15 } })
  orders.value  = data.data
  meta.value    = data.meta
  loading.value = false
}

async function updateStatus(id, status) {
  await client.put(`/orders/${id}`, { status })
  await load()
}

onMounted(() => load())
</script>
```

- [ ] **Step 4: Write AdminUsersView.vue**

```vue
<template>
  <div class="max-w-5xl mx-auto px-6 py-10">
    <h1 class="text-2xl font-bold text-white mb-6">User Management</h1>

    <div v-if="loading" class="text-gray-400">Loading...</div>
    <table v-else class="w-full text-sm text-left text-gray-300">
      <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
        <tr>
          <th class="px-4 py-3">Name</th>
          <th class="px-4 py-3">Email</th>
          <th class="px-4 py-3">Role</th>
          <th class="px-4 py-3">Joined</th>
          <th class="px-4 py-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="u in users" :key="u.id" class="border-b border-gray-800 hover:bg-gray-900">
          <td class="px-4 py-3 text-white">{{ u.name }}</td>
          <td class="px-4 py-3">{{ u.email }}</td>
          <td class="px-4 py-3">
            <select @change="e => changeRole(u.id, e.target.value)" :value="u.role"
              class="bg-gray-800 border border-gray-700 text-white rounded px-2 py-1 text-xs capitalize">
              <option>client</option>
              <option>employee</option>
              <option>admin</option>
            </select>
          </td>
          <td class="px-4 py-3">{{ new Date(u.created_at).toLocaleDateString() }}</td>
          <td class="px-4 py-3">
            <button @click="deleteUser(u.id)" class="text-red-400 hover:text-red-300 text-xs">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <Pagination :meta="meta" @change="load" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Pagination from '../components/Pagination.vue'
import client from '../api/client.js'

const users   = ref([])
const meta    = ref({})
const loading = ref(true)

async function load(page = 1) {
  loading.value = true
  const { data } = await client.get('/users', { params: { page, limit: 15 } })
  users.value   = data.data
  meta.value    = data.meta
  loading.value = false
}

async function changeRole(id, role) {
  await client.put(`/users/${id}`, { role })
}

async function deleteUser(id) {
  if (!confirm('Delete this user?')) return
  await client.delete(`/users/${id}`)
  await load()
}

onMounted(() => load())
</script>
```

- [ ] **Step 5: Commit**

```bash
git add frontend/src/views/
git commit -m "feat(frontend): add protected views (Dashboard, AdminCars, AdminOrders, AdminUsers)"
```

---

## PHASE 4 — Infrastructure

### Task 18: Update nginx.conf

**Files:**
- Modify: `nginx.conf`

- [ ] **Step 1: Replace nginx.conf**

```nginx
events {}

http {
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;

    server {
        listen 80;
        root /usr/share/nginx/html;
        index index.html;

        # API → PHP-FPM
        location /api/ {
            root /app/public;
            index index.php;

            location ~ \.php$ {
                include fastcgi_params;
                fastcgi_pass php:9000;
                fastcgi_param SCRIPT_FILENAME /app/public/index.php;
            }

            try_files $uri $uri/ /index.php?$query_string;
        }

        # Static assets served from PHP app (car images etc.)
        location /assets/ {
            root /app/public;
            try_files $uri =404;
        }

        # Vue SPA — all other routes go to index.html
        location / {
            try_files $uri $uri/ /index.html;
        }
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add nginx.conf
git commit -m "feat(infra): update nginx to serve Vue SPA + proxy /api to PHP"
```

---

### Task 19: Update docker-compose.yml and add Vue build

**Files:**
- Modify: `docker-compose.yml`
- Create: `frontend/Dockerfile`

- [ ] **Step 1: Write frontend/Dockerfile**

```dockerfile
FROM node:20-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM scratch AS export
COPY --from=builder /app/dist /dist
```

- [ ] **Step 2: Update docker-compose.yml**

```yaml
services:
  nginx:
    image: nginx:latest
    ports:
      - "80:80"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
      - ./app:/app
      - vue_dist:/usr/share/nginx/html
    depends_on:
      - php
      - vue_build

  vue_build:
    build:
      context: ./frontend
      dockerfile: Dockerfile
      target: builder
    command: sh -c "npm run build && cp -r /app/dist/* /dist/"
    volumes:
      - vue_dist:/dist

  php:
    build:
      context: .
      dockerfile: PHP.Dockerfile
    volumes:
      - ./app:/app
    environment:
      DB_HOST: mysql
      DB_NAME: grand_transmission_auto
      DB_USER: developer
      DB_PASSWORD: secret123
      DB_CHARSET: utf8mb4
      APP_SECRET: gta_jwt_secret_change_in_prod
    depends_on:
      - mysql
      - mailhog

  mysql:
    image: mariadb:latest
    environment:
      MYSQL_ROOT_PASSWORD: 'secret123'
      MYSQL_USER: 'developer'
      MYSQL_PASSWORD: 'secret123'
      MYSQL_DATABASE: 'grand_transmission_auto'
    volumes:
      - mysqldata:/var/lib/mysql
    ports:
      - "3306:3306"

  phpmyadmin:
    image: phpmyadmin:latest
    ports:
      - "8080:80"
    environment:
      - PMA_HOST=mysql

  mailhog:
    image: mailhog/mailhog
    ports:
      - "8025:8025"
      - "1025:1025"

  swagger:
    image: swaggerapi/swagger-ui
    ports:
      - "8090:8080"
    environment:
      SWAGGER_JSON: /docs/openapi.yaml
    volumes:
      - ./app/docs:/docs

volumes:
  mysqldata: {}
  vue_dist: {}
```

- [ ] **Step 3: Commit**

```bash
git add docker-compose.yml frontend/Dockerfile
git commit -m "feat(infra): add Vue build service + Swagger UI to Docker Compose"
```

---

## PHASE 5 — Documentation

### Task 20: Write openapi.yaml

**Files:**
- Create: `app/docs/openapi.yaml`

- [ ] **Step 1: Write openapi.yaml**

```yaml
openapi: 3.0.3
info:
  title: Grand Transmission Auto API
  version: 2.0.0
  description: REST API for the Grand Transmission Auto vehicle dealership platform

servers:
  - url: http://localhost/api

components:
  securitySchemes:
    BearerAuth:
      type: http
      scheme: bearer
      bearerFormat: JWT

  schemas:
    Car:
      type: object
      properties:
        id:          { type: integer }
        brand:       { type: string }
        model:       { type: string }
        year:        { type: integer }
        transmission:{ type: string }
        price:       { type: number }
        on_sale:     { type: integer }
        status:      { type: string }
        image_path:  { type: string }

    Order:
      type: object
      properties:
        id:         { type: integer }
        car_id:     { type: integer }
        user_id:    { type: integer }
        order_type: { type: string, enum: [purchase, lease] }
        status:     { type: string, enum: [pending, approved, denied, completed] }

    User:
      type: object
      properties:
        id:    { type: integer }
        name:  { type: string }
        email: { type: string }
        role:  { type: string, enum: [admin, employee, client] }

    PaginatedCars:
      type: object
      properties:
        data: { type: array, items: { $ref: '#/components/schemas/Car' } }
        meta:
          type: object
          properties:
            total: { type: integer }
            page:  { type: integer }
            limit: { type: integer }
            pages: { type: integer }

    Error:
      type: object
      properties:
        error: { type: string }

paths:
  /auth/login:
    post:
      summary: Login
      tags: [Auth]
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required: [email, password]
              properties:
                email:    { type: string }
                password: { type: string }
      responses:
        '200':
          description: JWT token + user
          content:
            application/json:
              schema:
                type: object
                properties:
                  token: { type: string }
                  user:  { $ref: '#/components/schemas/User' }
        '401':
          description: Invalid credentials
          content:
            application/json:
              schema: { $ref: '#/components/schemas/Error' }

  /auth/register:
    post:
      summary: Register new client
      tags: [Auth]
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required: [name, email, password]
              properties:
                name:     { type: string }
                email:    { type: string }
                password: { type: string }
      responses:
        '201':
          description: Created
          content:
            application/json:
              schema:
                type: object
                properties:
                  token: { type: string }
                  user:  { $ref: '#/components/schemas/User' }
        '409':
          description: Email already registered
          content:
            application/json:
              schema: { $ref: '#/components/schemas/Error' }

  /cars:
    get:
      summary: List cars with filters and pagination
      tags: [Cars]
      parameters:
        - { in: query, name: page,         schema: { type: integer, default: 1 } }
        - { in: query, name: limit,        schema: { type: integer, default: 12 } }
        - { in: query, name: brand,        schema: { type: string } }
        - { in: query, name: year,         schema: { type: integer } }
        - { in: query, name: transmission, schema: { type: string } }
        - { in: query, name: min_price,    schema: { type: number } }
        - { in: query, name: max_price,    schema: { type: number } }
        - { in: query, name: on_sale,      schema: { type: integer, enum: [0,1] } }
      responses:
        '200':
          description: Paginated car list
          content:
            application/json:
              schema: { $ref: '#/components/schemas/PaginatedCars' }
    post:
      summary: Add a car (employee+)
      tags: [Cars]
      security: [{ BearerAuth: [] }]
      requestBody:
        required: true
        content:
          multipart/form-data:
            schema:
              type: object
              required: [brand, model, year, transmission, price, status]
              properties:
                brand:        { type: string }
                model:        { type: string }
                year:         { type: integer }
                transmission: { type: string }
                price:        { type: number }
                status:       { type: string }
                image_path:   { type: string, format: binary }
      responses:
        '201':
          description: Created car
          content:
            application/json:
              schema: { $ref: '#/components/schemas/Car' }
        '401': { description: Unauthorized, content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }
        '403': { description: Forbidden,    content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }

  /cars/filters:
    get:
      summary: Get filter options (brands, years, transmissions, price bounds)
      tags: [Cars]
      responses:
        '200':
          description: Filter options
          content:
            application/json:
              schema:
                type: object
                properties:
                  brands:        { type: array, items: { type: string } }
                  years:         { type: array, items: { type: integer } }
                  transmissions: { type: array, items: { type: string } }
                  price_bounds:  { type: object, properties: { min: { type: number }, max: { type: number } } }

  /cars/{id}:
    parameters:
      - { in: path, name: id, required: true, schema: { type: integer } }
    get:
      summary: Get car by ID
      tags: [Cars]
      responses:
        '200':
          description: Car detail
          content:
            application/json:
              schema: { $ref: '#/components/schemas/Car' }
        '404': { description: Not found, content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }
    put:
      summary: Update car (employee+)
      tags: [Cars]
      security: [{ BearerAuth: [] }]
      requestBody:
        content:
          multipart/form-data:
            schema:
              type: object
              properties:
                brand: { type: string }
                price: { type: number }
      responses:
        '200': { description: Updated car, content: { application/json: { schema: { $ref: '#/components/schemas/Car' } } } }
        '404': { description: Not found, content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }
    delete:
      summary: Delete car (admin only)
      tags: [Cars]
      security: [{ BearerAuth: [] }]
      responses:
        '200': { description: Deleted }
        '403': { description: Forbidden, content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }
        '404': { description: Not found, content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }

  /orders:
    get:
      summary: List orders (own for clients, all for employee+)
      tags: [Orders]
      security: [{ BearerAuth: [] }]
      parameters:
        - { in: query, name: page,  schema: { type: integer, default: 1 } }
        - { in: query, name: limit, schema: { type: integer, default: 15 } }
      responses:
        '200': { description: Paginated orders }
        '401': { description: Unauthorized, content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }
    post:
      summary: Create order
      tags: [Orders]
      security: [{ BearerAuth: [] }]
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required: [car_id, order_type]
              properties:
                car_id:     { type: integer }
                order_type: { type: string, enum: [purchase, lease] }
                notes:      { type: string }
      responses:
        '201': { description: Created order, content: { application/json: { schema: { $ref: '#/components/schemas/Order' } } } }

  /orders/{id}:
    parameters:
      - { in: path, name: id, required: true, schema: { type: integer } }
    get:
      summary: Get order by ID
      tags: [Orders]
      security: [{ BearerAuth: [] }]
      responses:
        '200': { description: Order detail, content: { application/json: { schema: { $ref: '#/components/schemas/Order' } } } }
        '403': { description: Forbidden, content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }
        '404': { description: Not found, content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }
    put:
      summary: Update order status (employee+)
      tags: [Orders]
      security: [{ BearerAuth: [] }]
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required: [status]
              properties:
                status: { type: string, enum: [pending, approved, denied, completed] }
      responses:
        '200': { description: Updated order }

  /users:
    get:
      summary: List users (admin only)
      tags: [Users]
      security: [{ BearerAuth: [] }]
      parameters:
        - { in: query, name: page,  schema: { type: integer, default: 1 } }
        - { in: query, name: limit, schema: { type: integer, default: 15 } }
      responses:
        '200': { description: Paginated users }
        '403': { description: Forbidden, content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }

  /users/{id}:
    parameters:
      - { in: path, name: id, required: true, schema: { type: integer } }
    get:
      summary: Get user (own profile or admin)
      tags: [Users]
      security: [{ BearerAuth: [] }]
      responses:
        '200': { description: User, content: { application/json: { schema: { $ref: '#/components/schemas/User' } } } }
        '403': { description: Forbidden, content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }
    put:
      summary: Update user (own or admin; role change = admin only)
      tags: [Users]
      security: [{ BearerAuth: [] }]
      requestBody:
        content:
          application/json:
            schema:
              type: object
              properties:
                name:  { type: string }
                email: { type: string }
                role:  { type: string, enum: [admin, employee, client] }
      responses:
        '200': { description: Updated user }
    delete:
      summary: Delete user (admin only)
      tags: [Users]
      security: [{ BearerAuth: [] }]
      responses:
        '200': { description: Deleted }
        '403': { description: Forbidden, content: { application/json: { schema: { $ref: '#/components/schemas/Error' } } } }
```

- [ ] **Step 2: Commit**

```bash
git add app/docs/openapi.yaml
git commit -m "docs: add OpenAPI 3.0 spec for all endpoints"
```

---

### Task 21: Write README.md

**Files:**
- Create: `README.md`

- [ ] **Step 1: Write README**

```markdown
# Grand Transmission Auto — Web Development 2

> **693428 · Fred & Farid · Inholland University · Web Development 2**

A full-stack vehicle dealership platform built for the Web Development 2 assignment.
Clients can browse, purchase, or lease vehicles. Employees manage inventory and orders.
Administrators manage users and roles.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | Vue 3, Vite, Pinia, Vue Router 4, Tailwind CSS |
| Backend | PHP 8, PSR-4 namespaces, firebase/php-jwt |
| Database | MariaDB / MySQL |
| Web Server | nginx |
| Dev Tools | Docker Compose, phpMyAdmin, MailHog, Swagger UI |

---

## Getting Started

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)

### Run the project

```bash
git clone https://github.com/Fredd93/693428_Fred_Farid_Webdev2_Final_assignment_Auto_store.git
cd 693428_Fred_Farid_Webdev2_Final_assignment_Auto_store
docker-compose up --build
```

Import the database:
```bash
docker exec -i $(docker-compose ps -q mysql) \
  mysql -udeveloper -psecret123 grand_transmission_auto \
  < grand_transmission_auto_v2.sql
```

### URLs

| Service | URL |
|---|---|
| Application | http://localhost |
| phpMyAdmin | http://localhost:8080 |
| MailHog | http://localhost:8025 |
| Swagger API Docs | http://localhost:8090 |

---

## Default Accounts

| Role | Email | Password |
|---|---|---|
| Admin | admin@gta.com | admin123 |
| Employee | employee@gta.com | employee123 |
| Client | client@gta.com | client123 |

---

## Project Structure

```
├── app/                    PHP backend (PSR-4, GTA\ namespace)
│   ├── public/index.php    Single entry point
│   ├── src/
│   │   ├── Controllers/    CarController, OrderController, UserController, AuthController
│   │   ├── Models/         CarModel, OrderModel, UserModel, BaseModel
│   │   ├── Middleware/     AuthMiddleware (JWT validation + role checks)
│   │   └── Routes/         api.php, auth.php
│   └── docs/openapi.yaml   OpenAPI 3.0 spec
├── frontend/               Vue 3 SPA (Vite + Pinia + Vue Router + Tailwind)
│   └── src/
│       ├── views/          HomeView, CarsView, CarDetailView, LoginView, RegisterView,
│       │                   DashboardView, AdminCarsView, AdminOrdersView, AdminUsersView
│       ├── components/     Navbar, Footer, CarCard, Pagination, StatusBadge
│       ├── stores/         auth.js (JWT + user), cars.js
│       ├── router/         Vue Router with auth + role guards
│       └── api/client.js   Axios with Bearer token interceptor
├── docker-compose.yml
├── nginx.conf
└── grand_transmission_auto_v2.sql
```

---

## User Roles

| Role | Permissions |
|---|---|
| **Client** | Browse cars, place purchase/lease orders, view own orders, manage own profile |
| **Employee** | All client permissions + add/edit cars, process all orders |
| **Admin** | All employee permissions + delete cars, manage users, assign roles |

---

## API Reference

Full interactive documentation available at **http://localhost:8090** (Swagger UI).

All protected endpoints require:
```
Authorization: Bearer <token>
```

Token is obtained from `POST /api/auth/login`.

---

## Environment Variables

Set in `docker-compose.yml` under the `php` service:

| Variable | Description |
|---|---|
| `DB_HOST` | MySQL hostname (default: `mysql`) |
| `DB_NAME` | Database name |
| `DB_USER` | Database user |
| `DB_PASSWORD` | Database password |
| `APP_SECRET` | JWT signing secret — **change in production** |

---

## AI Disclosure Statement

AI tools (Claude) were used during the development of this project to assist with:
- Structuring the PHP PSR-4 namespace architecture
- Writing boilerplate for JWT middleware and Axios interceptors
- Generating the OpenAPI specification

All generated code was reviewed, understood, and adapted by the student. The student is able to explain and demonstrate understanding of all code in this project. AI was not used to replace understanding — it was used as a development accelerator.

---

## References

- [Vue.js Official Guide](https://vuejs.org/guide)
- [RESTful API Best Practices](https://restfulapi.net)
- [firebase/php-jwt](https://github.com/firebase/php-jwt)
- [Pinia Documentation](https://pinia.vuejs.org)
```

- [ ] **Step 2: Commit**

```bash
git add README.md
git commit -m "docs: add comprehensive README with setup, structure, roles, AI disclosure"
```

---

### Task 22: Push to GitHub and verify

- [ ] **Step 1: Push all commits**

```bash
git push origin main
```

- [ ] **Step 2: Verify the project runs end-to-end**

```bash
docker-compose up --build
```

Open http://localhost and confirm:
- Home page loads with car cards
- `/cars` shows filter sidebar + paginated listing
- `/login` logs in as `admin@gta.com` / `admin123` and redirects to `/admin/cars`
- Admin can add a car via the modal
- `/admin/orders` shows all orders with status dropdown
- `/admin/users` shows user list with role selector
- Client login shows only own orders on `/dashboard`
- Swagger UI at http://localhost:8090 shows all endpoints

- [ ] **Step 3: Verify rubric checklist**

```
✅ CSS (2pts)         — Tailwind, dark theme, responsive grid (mobile/tablet/desktop)
✅ Frontend (2pts)    — Vue 3 SPA, Vue Router with guards, Pinia state management
✅ REST API (2pts)    — GET/POST/PUT/DELETE proper verbs, pagination on all lists, error JSON on all failures
✅ Auth (2pts)        — JWT login/register, Bearer token, role-based access (admin > employee > client)
✅ Backend arch (2pts)— PSR-4 GTA\ namespace, Controllers/Models/Middleware/Routes structure, composer autoload
```

- [ ] **Step 4: Final commit**

```bash
git add .
git commit -m "chore: final submission build verified"
git push origin main
```
