# ✅ Simple Laravel Admin Template

A modern full-stack admin template built with Laravel 12, Vue 3, Inertia.js, and Shadcn UI.

## 🎉 Features

### Backend (Laravel 12)
- ✅ **Admin Controller Base Class** - CRUD operations built-in
- ✅ **API Controller Base Class** - Unified JSON response format
- ✅ **User Management** - Full CRUD with dialog forms
- ✅ **Notification System** - Send, read, delete notifications
- ✅ **Dashboard** - Statistics cards and recent notifications

### Frontend (Vue 3 + Inertia.js + Shadcn UI)
- ✅ **Responsive Layout** - Collapsible sidebar
- ✅ **User Management Page** - Table with search, batch operations
- ✅ **Notification Center** - List with read/unread status
- ✅ **Dashboard** - Overview with stats
- ✅ **Dialog Forms** - Create/edit users in modal
- ✅ **Flash Messages** - Auto-display toast notifications

### Core Features
- 🔐 Authentication (Fortify) - Login, register, 2FA, email verification
- 📊 Dashboard with real-time stats
- 👥 User CRUD with batch delete (ID=1 protected)
- 🔔 Notification system with send API
- 🎨 Clean UI with Shadcn components
- 📱 Mobile responsive

## 🚀 Quick Start

### Prerequisites
- PHP 8.4+
- Node.js (LTS)
- Composer
- pnpm

### Installation

```bash
# Clone repository
git clone <your-repo-url>
cd <project-name>

# Install dependencies
composer install
pnpm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate

# Generate test data (optional)
php artisan tinker --execute="User::factory()->count(10)->create()"
php artisan tinker --execute="
use App\Models\Notification;
Notification::send(1, 'Welcome', 'Thank you for using our system!', 'success');
"
```

### Development

```bash
# Terminal 1 - Backend
php artisan serve

# Terminal 2 - Frontend
pnpm run dev

# Or use single command (recommended)
composer dev
```

Visit: `http://127.0.0.1:8000`

### Production

```bash
# Build frontend
pnpm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run backend
php artisan serve
# Or configure Nginx/Apache
```

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   ├── Admin/              # Admin CRUD controllers
│   │   ├── Controller.php  # Base controller with CRUD methods
│   │   ├── UserController.php
│   │   ├── NotificationController.php
│   │   └── DashboardController.php
│   └── Api/
│       └── Controller.php  # API base controller
├── Models/
│   ├── User.php
│   └── Notification.php
└── Exceptions/

resources/js/
├── Pages/                  # Inertia pages
│   ├── Dashboard.vue
│   ├── User/
│   │   └── Index.vue
│   └── Notification/
│       └── Index.vue
├── components/
│   ├── custom/             # Custom components
│   │   ├── AppSidebar.vue  # Left sidebar menu
│   │   └── NavUser.vue     # User dropdown menu
│   └── ui/                 # Shadcn UI components
├── composables/            # Vue composables
│   ├── useFlash.ts
│   └── useConfirm.ts
└── layouts/
    └── AuthenticatedLayout.vue
```

## 🔧 Usage

### Add New Menu Item

Edit `resources/js/components/custom/AppSidebar.vue`:

```typescript
const navMain: MainNavigationItem[] = [
    {
        title: 'Dashboard',
        url: '/dashboard',
        icon: LayoutDashboard,
    },
    {
        title: 'User Management',
        url: '/user',
        icon: Users,
    },
    // Add your menu here
];
```

### Create New CRUD Module

1. **Create Model & Migration**
```bash
php artisan make:model Post -m
```

2. **Create Controller**
```bash
php artisan make:controller Admin/PostController
```

3. **Extend Base Controller**
```php
class PostController extends Controller
{
    protected string $indexPage = 'Post/Index';
    protected string $formPage = 'Post/Form';
    protected array $searchFields = ['status'];
    protected string $quickSearchField = 'title';

    public function __construct()
    {
        $this->model = new Post();
    }
}
```

4. **Add Route**
```php
Route::resource('post', PostController::class);
```

5. **Create Vue Page**
```bash
# Create resources/js/Pages/Post/Index.vue
# Copy from User/Index.vue and modify
```

### Send Notification

```php
use App\Models\Notification;

// Send to single user
Notification::send($userId, 'Title', 'Content', 'info');

// Send to multiple users
Notification::sendToMany([1, 2, 3], 'Title', 'Content', 'warning');

// Send to all users
Notification::sendToAll('System Announcement', 'Content', 'success');
```

## 📝 API Response Format

### Success Response
```json
{
    "code": 1,
    "msg": "Success",
    "time": 1737012345,
    "data": {...}
}
```

### Error Response
```json
{
    "code": 0,
    "msg": "Error message",
    "time": 1737012345,
    "data": null
}
```

## 🎨 Tech Stack

- **Backend**: Laravel 12, Fortify, Sanctum
- **Frontend**: Vue 3, TypeScript, Inertia.js
- **UI**: Shadcn UI, Tailwind CSS
- **Icons**: Lucide Vue
- **Build**: Vite

## 📄 License

MIT

## 🤝 Contributing

Contributions are welcome! Feel free to submit issues and pull requests.

---

**Ready to extend and customize!** 🎊
