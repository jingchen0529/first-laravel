<script setup lang="ts">
import ErrorFeedback from '@/components/custom/ErrorFeedback.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthenticationLayout from '@/layouts/AuthenticationLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

interface Props {
    status?: string;
}

defineProps<Props>();

const form = useForm({
    email: '',
});

function submit(e: Event) {
    e.preventDefault();
    form.post(route('password.email'), {
        onSuccess: () => router.visit(route('forgot-password.sent')),
    });
}
</script>

<template>
    <Head>
        <title>忘记密码</title>
    </Head>

    <AuthenticationLayout>
        <form class="flex flex-col gap-6" @submit="submit">
            <div class="flex flex-col items-center gap-4 text-center">
                <h1 class="text-2xl font-bold">忘记密码？</h1>
                <p class="text-balance text-sm text-muted-foreground">
                    没问题。请告诉我们您的邮箱地址，我们将向您发送密码重置链接。
                </p>
            </div>

            <div
                v-if="status"
                class="text-center text-sm font-medium text-green-600 dark:text-green-400"
            >
                {{ status }}
            </div>

            <ErrorFeedback :message="form.errors.email" />

            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">邮箱</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        v-model="form.email"
                        class="block w-full"
                        autocomplete="username"
                        placeholder="m@example.com"
                        required
                        autofocus
                    />
                </div>

                <Button
                    type="submit"
                    class="w-full"
                    :disabled="form.processing"
                >
                    发送
                </Button>

                <div class="text-center text-sm">
                    想起来了？
                    <Link
                        :href="route('login')"
                        class="underline underline-offset-4"
                    >
                        登录
                    </Link>
                </div>
            </div>
        </form>
    </AuthenticationLayout>
</template>
