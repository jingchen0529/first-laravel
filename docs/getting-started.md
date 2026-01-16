# 快速开始

## 📋 前置要求

在开始之前，请确保已安装以下软件：

- PHP 8.x
- Node.js（推荐 LTS 版本）
- Composer
- Git
- Laravel Herd（推荐用于本地开发）

## 🚀 安装

1. **克隆仓库**

```bash
git clone <your-repository-url> your-project-name
cd your-project-name
```

2. **安装 PHP 依赖**

```bash
composer install
```

3. **安装 Node.js 依赖**

```bash
pnpm install
```

4. **环境配置**

```bash
cp .env.example .env
php artisan key:generate
```

5. **配置环境变量**
   编辑 `.env` 文件，配置数据库和其他设置。

6. **创建数据库**

```bash
php artisan migrate
```

7. **构建资源**

```bash
pnpm run dev
```

## 🏃‍♂️ 开发工作流

### 启动开发服务器

```bash
pnpm run dev
```

如果使用 Laravel Herd，应用将在 `http://vue-inertia-laravel.test` 可用。

### 开发命令

```bash
# 运行测试
php artisan test

# 格式化 PHP 代码
./vendor/bin/pint

# 类型检查 TypeScript 和 Vue 文件
pnpm run check

# Vue 类型检查
pnpm run check:vue

# 检查 JavaScript/TypeScript/Vue
pnpm run lint

# 格式化 JavaScript/TypeScript/Vue
pnpm run format
```

## 📦 生产环境部署

1. **优化 Composer**

```bash
composer install --optimize-autoloader --no-dev
```

2. **构建前端资源**

```bash
pnpm run build
```

3. **缓存配置**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔧 IDE 设置

为了获得最佳开发体验，我们推荐：

- VS Code 及以下扩展：
    - Volar (Vue Language Features)
    - TypeScript and JavaScript Language Features
    - Tailwind CSS IntelliSense
    - ESLint
    - Prettier

## 🐛 常见问题

### 热模块替换 (HMR)

如果 HMR 不工作：

1. 清除浏览器缓存
2. 重启 Vite 开发服务器
3. 检查 `vite.config.js` 配置

### TypeScript 错误

运行 `pnpm run check` 来识别类型问题。常见修复方法：

- 确保在 `resources/js/types` 中有正确的类型定义
- 检查是否缺少类型导入
- 验证 Vue 组件属性类型

### Vue 3 组合式 API 问题

组合式 API 的常见问题及解决方案：

1. **状态未更新**
   - 确保对响应式变量使用 `ref` 或 `reactive`
   - 检查是否正确访问 ref 的 `.value`
   - 验证响应式对象是否正确解构

2. **属性类型错误**
   - 使用 `defineProps<T>()` 实现类型安全的属性
   - 确保接口定义正确
   - 检查必需的属性是否已传递

3. **计算值未更新**
   - 确保所有依赖都是响应式的（`ref` 或 `reactive`）
   - 验证计算没有副作用
   - 检查是否正确访问 ref 的 `.value`

4. **Watch 未运行**
   - 检查依赖是否实际发生变化
   - 确保监听正确的源
   - 验证 watch 选项（deep、immediate）

5. **模板渲染问题**
   - 检查 Vue 3 的模板语法
   - 验证响应式状态是否正确引用
   - 确保组件已正确注册

## 🔒 类型安全

为确保应用的类型安全：

1. 在 `tsconfig.json` 中启用严格模式
2. 对所有新组件使用 TypeScript
3. 正确定义 Inertia 页面属性的类型
4. 对组件属性使用 `defineProps<T>()`
5. 为所有函数添加返回类型
