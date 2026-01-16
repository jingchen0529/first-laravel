<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
}

const props = defineProps<{
    row?: User | null;
}>();

const isEdit = computed(() => !!props.row?.id);
const title = computed(() => (isEdit.value ? '编辑用户' : '新增用户'));

const form = useForm({
    name: props.row?.name || '',
    email: props.row?.email || '',
    password: '',
});

function submit() {
    if (isEdit.value) {
        form.put(route('user.update', props.row!.id));
    } else {
        form.post(route('user.store'));
    }
}
</script>

<template>
    <Head :title="title" />
    <AuthenticatedLayout>
        <div class="flex max-w-7xl flex-col sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8">
                <div class="max-w-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-bold">{{ title }}</h1>
                        <Link :href="route('user.index')">
                            <Button variant="outline">返回列表</Button>
                        </Link>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">用户名</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="请输入用户名"
                            />
                            <p v-if="form.errors.name" class="text-sm text-red-500">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="email">邮箱</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="请输入邮箱"
                            />
                            <p v-if="form.errors.email" class="text-sm text-red-500">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="password">
                                密码
                                <span v-if="isEdit" class="text-muted-foreground text-xs ml-1">(留空则不修改)</span>
                            </Label>
                            <Input
                                id="password"
                                v-model="form.password"
                                type="password"
                                placeholder="请输入密码"
                            />
                            <p v-if="form.errors.password" class="text-sm text-red-500">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <div class="flex gap-2 pt-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? '提交中...' : '保存' }}
                            </Button>
                            <Link :href="route('user.index')">
                                <Button type="button" variant="outline">取消</Button>
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
