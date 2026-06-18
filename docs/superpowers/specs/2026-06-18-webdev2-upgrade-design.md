# Grand Transmission Auto — WebDev 2 Upgrade Design

**Date:** 2026-06-18  
**Repo:** `693428_Fred_Farid_Webdev2_Final_assignment_Auto_store`  
**Course:** Web Development 2 — Inholland University  
**Approach:** Option B — Clean Restructure (reuse ~80% of existing logic, reorganize into proper structure)

---

## 1. Goals

Hit 2/2 on every rubric by adding:

| Rubric | Target | Key additions |
|---|---|---|
| CSS / Responsive | 2pts | Tailwind CSS via Vue — full mobile/tablet/desktop responsiveness |
| Frontend Architecture | 2pts | Vue 3 SPA + Vue Router + Pinia state management |
| Backend REST API | 2pts | Proper REST naming, pagination on all list endpoints, error messages on all endpoints |
| Authentication | 2pts | JWT (firebase/php-jwt), Bearer token on every request, role-based access |
| Backend Architecture | 2pts | PSR-4 namespaced `src/` structure, AuthMiddleware, clean routing |

---

## 2. System Architecture

### 2.1 Repository Structure

```
693428_Fred_Farid_Webdev2_Final_assignment_Auto_store/
├── app/                        ← PHP backend
│   ├── public/
│   │   └── index.php           ← single entry point
│   ├── src/
│   │   ├── Controllers/
│   │   │   ├── CarController.php
│   │   │   ├── OrderController.php
│   │   │   └── UserController.php
│   │   ├── Models/
│   │   │   ├── BaseModel.php
│   │   │   ├── CarModel.php
│   │   │   ├── OrderModel.php
│   │   │   └── UserModel.php
│   │   ├── Middleware/
│   │   │   └── AuthMiddleware.php   ← JWT validation + role check
│   │   └── Routes/
│   │       ├── api.php
│   │       └── auth.php
│   └── composer.json               ← PSR-4 autoload for GTA\ namespace
├── frontend/                   ← Vue 3 SPA
│   ├── src/
│   │   ├── components/
│   │   ├── views/
│   │   ├── router/
│   │   ├── stores/
│   │   └── api/
│   ├── package.json
│   └── vite.config.js
├── docker-compose.yml
├── nginx.conf
└── PHP.Dockerfile
```

### 2.2 Request Flow

```
Browser (Vue SPA)
  → Axios + Authorization: Bearer <token>
  → nginx :80
    → /api/*  → PHP-FPM (AuthMiddleware → Controller → Model → MySQL)
    → /*      → frontend/dist/index.html  (SPA fallback)
```

### 2.3 Docker Services

| Service | Port | Role |
|---|---|---|
| nginx | 80 | Serves Vue dist + proxies /api to PHP |
| php | 9000 | PHP-FPM, API only |
| mysql | 3306 | Unchanged |
| phpmyadmin | 8080 | Unchanged |
| mailhog | 8025 / 1025 | Unchanged |

Vue is compiled during Docker image build (`npm run build`) — no separate dev service for submission.

---

## 3. Backend Design

### 3.1 Namespace & Autoloading

All PHP classes live under the `GTA\` namespace, PSR-4 mapped in `composer.json`:

```json
{
  "autoload": {
    "psr-4": { "GTA\\": "src/" }
  }
}
```

### 3.2 JWT Authentication

- Library: `firebase/php-jwt`
- Login returns `{ token, user }` — token signed with `APP_SECRET` env var, 24h expiry
- Token payload: `{ sub: user_id, role: "admin|employee|client", exp }`
- `AuthMiddleware::require(string $minRole)` — validates Bearer token, checks role hierarchy, sets `$_REQUEST['auth_user']`
- Role hierarchy: `admin > employee > client`

### 3.3 REST API Endpoints

#### Auth (public)
| Method | Endpoint | Returns |
|---|---|---|
| POST | `/api/auth/login` | `{ token, user }` |
| POST | `/api/auth/register` | `{ token, user }` |

#### Cars
| Method | Endpoint | Auth | Notes |
|---|---|---|---|
| GET | `/api/cars` | public | `?page&limit&brand&min_price&max_price&year&transmission&on_sale` |
| GET | `/api/cars/filters` | public | Returns distinct brands, years, transmissions, price bounds |
| GET | `/api/cars/{id}` | public | 404 if not found |
| POST | `/api/cars` | employee+ | Returns new car with generated id |
| PUT | `/api/cars/{id}` | employee+ | Partial update |
| DELETE | `/api/cars/{id}` | admin | 404 if not found |

#### Orders
| Method | Endpoint | Auth | Notes |
|---|---|---|---|
| GET | `/api/orders` | client+ | Client sees own; employee+ sees all. `?page&limit` |
| GET | `/api/orders/{id}` | client+ | 403 if not owner and not employee |
| POST | `/api/orders` | client+ | Returns new order with generated id |
| PUT | `/api/orders/{id}` | employee+ | Update status |

#### Users
| Method | Endpoint | Auth | Notes |
|---|---|---|---|
| GET | `/api/users` | admin | `?page&limit` |
| GET | `/api/users/{id}` | client+ | Own profile or admin |
| PUT | `/api/users/{id}` | client+ | Role change = admin only |
| DELETE | `/api/users/{id}` | admin | |

### 3.4 Pagination Response Shape

All list endpoints return:
```json
{
  "data": [ "...items..." ],
  "meta": { "total": 48, "page": 1, "limit": 12, "pages": 4 }
}
```

### 3.5 Error Responses

All endpoints return JSON errors:
```json
{ "error": "Car not found" }
```
With appropriate HTTP status codes (400, 401, 403, 404, 405, 500).

---

## 4. Frontend Design

### 4.1 Tech Stack

- Vue 3 (Composition API)
- Vue Router 4
- Pinia (state management)
- Axios (HTTP client)
- Tailwind CSS (utility-first, responsive)
- Vite (build tool)

### 4.2 Pages / Routes

| Route | View | Auth | Description |
|---|---|---|---|
| `/` | HomeView | public | Hero, featured cars, new arrivals, deals |
| `/cars` | CarsView | public | Car listing with filters + pagination |
| `/cars/:id` | CarDetailView | public | Car photos, specs, buy/lease buttons |
| `/login` | LoginView | public | JWT login form |
| `/register` | RegisterView | public | Client registration |
| `/dashboard` | DashboardView | client+ | My orders, profile |
| `/admin/cars` | AdminCarsView | employee+ | Car CRUD management |
| `/admin/orders` | AdminOrdersView | employee+ | Process all orders |
| `/admin/users` | AdminUsersView | admin | User + role management |

### 4.3 Components

| Component | Used by | Purpose |
|---|---|---|
| `Navbar.vue` | All views | Role-aware navigation links |
| `Footer.vue` | All views | Site footer |
| `CarCard.vue` | Home, Cars | Car thumbnail + price card |
| `CarFilter.vue` | CarsView | Sidebar filter panel |
| `Pagination.vue` | Cars, Orders, Users | Page navigation |
| `CarFormModal.vue` | AdminCarsView | Add/edit car with image upload |
| `OrderModal.vue` | CarDetailView | Buy/lease form |
| `OrderList.vue` | Dashboard | Client order history |
| `CarTable.vue` | AdminCarsView | Sortable car management table |
| `OrderTable.vue` | AdminOrdersView | All orders with status actions |
| `UserTable.vue` | AdminUsersView | User list with role assignment |
| `StatusBadge.vue` | Orders | Colour-coded order status pill |
| `RoleBadge.vue` | Users | Colour-coded role pill |

### 4.4 Pinia Stores

**`auth.js`**
- State: `token` (persisted to localStorage), `user { id, name, email, role }`
- Actions: `login()`, `logout()`, `register()`
- Getters: `isLoggedIn`, `isAdmin`, `isEmployee`

**`cars.js`**
- State: `cars[]`, `filters{}`, `pagination{}`
- Actions: `fetchCars(params)`, `fetchFilters()`

### 4.5 Router Guards

All `/admin/*` and `/dashboard` routes have a `beforeEnter` guard that checks `auth.isLoggedIn`. Role-specific routes additionally check `auth.isAdmin` or `auth.isEmployee`. Unauthorized users are redirected to `/login`.

### 4.6 Axios Client

`src/api/client.js` creates an Axios instance with:
- `baseURL` pointed at the PHP API
- Request interceptor: attaches `Authorization: Bearer <token>` from Pinia auth store
- Response interceptor: on 401, clears auth store and redirects to `/login`

---

## 5. Database

Existing `grand_transmission_auto` schema is reused. One addition:

- `users.role` column: `ENUM('admin', 'employee', 'client')` DEFAULT `'client'`

---

## 6. New Repo Setup

1. Copy current project files into new local folder
2. Set remote to `https://github.com/Fredd93/693428_Fred_Farid_Webdev2_Final_assignment_Auto_store.git`
3. Reorganize PHP into `src/` namespace structure
4. Add `frontend/` Vue project
5. Update `nginx.conf` to serve Vue dist + proxy `/api`
6. Update `docker-compose.yml` — Vue build step in PHP.Dockerfile or separate build stage

---

## 7. Out of Scope

- Email notifications (MailHog already set up, no new work needed)
- Unit tests (not in rubric)
- CI/CD pipeline
