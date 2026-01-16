<script setup lang="ts">
import ErrorFeedback from '@/components/custom/ErrorFeedback.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthenticationLayout from '@/layouts/AuthenticationLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

interface Props {
    isRegisterEnabled: boolean;
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit(e: Event) {
    e.preventDefault();
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}

onMounted(() => {
    if (!props.isRegisterEnabled) {
        window.location.href = route('login');
    }
});
</script>

<template>
    <Head>
        <title>注册</title>
    </Head>

    <template v-if="isRegisterEnabled">
        <AuthenticationLayout>
            <form class="flex flex-col gap-6" @submit="submit">
                <div class="flex flex-col items-center gap-2 text-center">
                    <h1 class="text-2xl font-bold">注册账户</h1>
                    <p class="text-balance text-sm text-muted-foreground">
                        输入您的邮箱以注册账户
                    </p>
                </div>

                <div class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="name">姓名</Label>
                        <Input
                            id="name"
                            type="text"
                            name="name"
                            v-model="form.name"
                            autocomplete="username"
                            placeholder="输入您的姓名"
                            required
                            autofocus
                        />
                        <ErrorFeedback
                            class="text-center"
                            :message="form.errors.name"
                        />
                    </div>
                </div>

                <div class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="email">邮箱</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            v-model="form.email"
                            autocomplete="username"
                            placeholder="you@example.com"
                            required
                        />
                        <ErrorFeedback
                            class="text-center"
                            :message="form.errors.email"
                        />
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center">
                            <Label for="password">密码</Label>
                        </div>
                        <Input
                            id="password"
                            type="password"
                            name="password"
                            v-model="form.password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            required
                        />
                        <ErrorFeedback
                            class="text-center"
                            :message="form.errors.password"
                        />
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center">
                            <Label for="password_confirmation"
                                >确认密码</Label
                            >
                        </div>
                        <Input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            v-model="form.password_confirmation"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            required
                        />
                        <ErrorFeedback
                            class="text-center"
                            :message="form.errors.password_confirmation"
                        />
                    </div>

                    <Button
                        type="submit"
                        class="w-full"
                        :disabled="form.processing"
                    >
                        注册
                    </Button>
                </div>

                <div class="flex justify-center gap-1 text-sm">
                    已有账户？
                    <Link
                        :href="route('login')"
                        class="underline underline-offset-4"
                    >
                        登录
                    </Link>
                </div>
            </form>
        </AuthenticationLayout>
    </template>
</template>
