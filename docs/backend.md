# 后端文档

## 🏗️ 后端架构

本应用的后端使用 Laravel 12 构建，遵循现代最佳实践和设计模式。

### 目录结构

```
app/
├── Console/          # Artisan 命令
├── Controllers/      # HTTP 控制器
├── Models/          # Eloquent 模型
├── Providers/       # 服务提供者
└── Services/        # 业务逻辑服务

database/
├── factories/       # 用于测试的模型工厂
├── migrations/      # 数据库迁移
└── seeders/        # 数据库填充
```

## 🔐 身份认证 (Laravel Fortify)

我们使用 Laravel Fortify 进行身份认证，它提供：

- 登录和注册
- 邮箱验证
- 双因素认证
- 密码重置
- 密码确认
- 个人信息更新

### Fortify 配置

Fortify 在 `config/fortify.php` 中配置。启用的关键功能：

```php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication(),
],
```

### 认证流程

1. 用户提交凭证
2. Fortify 验证凭证
3. 成功时：
    - 创建会话
    - 用户被重定向到仪表板
4. 失败时：
    - 返回错误响应
    - 用户停留在登录页面

## 📡 API 端点

### 认证端点

```
POST   /login                    # 用户登录
POST   /logout                   # 用户登出
POST   /register                 # 用户注册
POST   /forgot-password         # 密码重置请求
POST   /reset-password          # 密码重置
GET    /email/verify            # 邮箱验证
POST   /email/verification-notification  # 重新发送验证邮件
```

### 用户管理端点

```
PUT    /user/profile-information  # 更新个人资料
PUT    /user/password            # 更新密码
POST   /user/two-factor-authentication  # 启用双因素认证
DELETE /user/two-factor-authentication  # 禁用双因素认证
```

## 💾 数据库

### 数据库配置

项目默认使用 SQLite，但支持任何数据库。在 `.env` 中配置数据库：

```env
DB_CONNECTION=sqlite
DB_DATABASE=database.sqlite
```

### 核心模型

- `User.php`: 用户账户信息
- `Profile.php`: 扩展的用户资料数据
- `Session.php`: 用户会话管理

### 迁移

所有数据库架构都在 `database/migrations/` 下的迁移文件中定义。关键迁移：

- 用户表
- 密码重置令牌
- 失败任务表
- 会话表
- 双因素认证设置

运行迁移：

```bash
php artisan migrate
```

## 🔄 Inertia 集成

### 中间件

`HandleInertiaRequests` 中间件 (`app/Http/Middleware/HandleInertiaRequests.php`) 管理：

- 与所有页面共享通用数据
- 管理 Inertia 响应
- 处理版本冲突

### 共享数据

通过 Inertia 与所有页面共享的通用数据：

```php
public function share(Request $request): array
{
    return [
        'auth' => [
            'user' => $request->user(),
        ],
        'flash' => [
            'message' => fn () => $request->session()->get('message')
        ],
    ];
}
```

## 🧪 测试

### 测试结构

```
tests/
├── Feature/          # 功能测试
├── Unit/            # 单元测试
└── TestCase.php     # 基础测试类
```

### 运行测试

```bash
# 运行所有测试
php artisan test

# 运行特定测试
php artisan test --filter=UserTest

# 运行并生成覆盖率报告
php artisan test --coverage
```

## 🛠️ 开发工具

### Artisan 命令

开发中常用的 Artisan 命令：

```bash
# 创建新控制器
php artisan make:controller UserController

# 创建新模型并生成迁移文件
php artisan make:model Post -m

# 创建新测试
php artisan make:test UserTest

# 清除缓存
php artisan cache:clear
```

### 代码风格

我们使用 Laravel Pint 进行 PHP 代码格式化。运行：

```bash
./vendor/bin/pint
```

## 🔍 调试

### Laravel Telescope

如果启用，Laravel Telescope 提供以下调试工具：

- 请求/响应信息
- 数据库查询
- 缓存操作
- 队列任务
- 计划任务

在开发环境中通过 `/telescope` 访问 Telescope。

### 错误处理

自定义错误处理在 `app/Exceptions/Handler.php` 中配置。错误会：

1. 记录到 storage/logs
2. 报告到错误跟踪服务（如果已配置）
3. 根据请求类型适当地渲染
