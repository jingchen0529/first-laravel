# 前端文档

## 🏗️ 前端架构

前端使用 Vue 3、TypeScript 和 Inertia.js 构建，提供现代单页应用体验的同时保持服务端渲染的优势。

### 目录结构

```
resources/js/
├── components/     # 可复用的 UI 组件
├── layouts/        # 页面布局
├── pages/         # 页面组件
├── types/         # TypeScript 类型定义
└── utils/         # 工具函数
```

## 🎨 UI 组件

### Shadcn UI 集成

我们使用 Shadcn UI 来提供一致且易访问的组件。组件安装和配置在 `components/ui` 目录中。

### 自定义组件

使用 Vue 3 组合式 API 的自定义组件：

```vue
<!-- resources/js/components/MyComponent.vue -->
<script setup lang="ts">
import { cn } from '@/lib/utils';

interface Props {
    className?: string;
}

const props = defineProps<Props>();
</script>

<template>
    <div :class="cn('base-styles', props.className)">
        <slot />
    </div>
</template>
```

## 🎯 页面和路由

### 页面结构

页面存储在 `resources/js/Pages/` 中，遵循 Laravel 路由结构：

```
Pages/
├── Auth/
│   ├── Login.vue
│   └── Register.vue
├── Dashboard/
│   └── Index.vue
└── Welcome.vue
```

### 使用组合式 API 的 Inertia 页面组件

```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import type { PageProps } from '@/types';

const props = defineProps<PageProps>();
const count = ref(0);
const doubled = computed(() => count.value * 2);

// 使用 watch 处理副作用
watch(count, (newValue) => {
    console.log(`计数变更为 ${newValue}`);
});
</script>

<template>
    <Head title="Dashboard" />

    <h1>欢迎 {{ props.auth.user.name }}</h1>
    <p>计数: {{ count }} (双倍: {{ doubled }})</p>
</template>
```

## 📐 布局

### 布局结构

布局位于 `resources/js/layouts/` 中，提供一致的页面结构：

- `AuthenticatedLayout.vue`: 用于已认证页面
- `GuestLayout.vue`: 用于公开页面
- `AuthenticationLayout.vue`: 用于认证相关页面

### 使用插槽的布局

```vue
<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h1>仪表板</h1>
        </template>
        <slot />
    </AuthenticatedLayout>
</template>
```

## 🔄 状态管理

### 使用组合式 API 的 Inertia.js 表单处理

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
});

function submit() {
    form.post('/login');
}
</script>

<template>
    <form @submit.prevent="submit">
        <input type="email" v-model="form.email" />
        <input type="password" v-model="form.password" />
        <button type="submit" :disabled="form.processing">登录</button>
    </form>
</template>
```

## 🎭 TypeScript 集成

### 类型定义

```typescript
// resources/js/types/index.ts
export interface User {
    id: number;
    name: string;
    email: string;
}

export interface PageProps {
    auth: {
        user: User;
    };
}
```

## 🔧 使用 TypeScript 的组件属性

应该使用 TypeScript 和 Vue 3 的组合式 API 来定义属性类型：

```vue
<script setup lang="ts">
interface Props {
    title: string;
    description?: string;
}

const props = defineProps<Props>();

// 从 props 计算状态
const titleLength = computed(() => props.title.length);
</script>

<template>
    <div>
        <h2>{{ title }}</h2>
        <p v-if="description">{{ description }}</p>
        <small>标题长度: {{ titleLength }}</small>
    </div>
</template>
```

## 🔍 Vue 3 组合式 API 的关键特性

- **ref/reactive**: 显式的响应式状态声明
- **defineProps**: 类型安全的属性，更好的 TypeScript 集成
- **computed**: 自动更新的计算值
- **watch/watchEffect**: 当依赖变化时运行的副作用
- **slots**: 灵活的插槽系统用于模板组合
