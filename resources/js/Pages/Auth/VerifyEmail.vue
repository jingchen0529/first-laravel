<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AuthenticationLayout from '@/layouts/AuthenticationLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Props {
    status?: string;
}

defineProps<Props>();

const form = useForm({});

function submit(e: Event) {
    e.preventDefault();
    form.post(route('verification.send'));
}
</script>

<template>
    <Head>
        <title>验证邮箱</title>
    </Head>

    <AuthenticationLayout>
        <form class="flex flex-col gap-6" @submit="submit">
            <div class="flex flex-col items-center gap-4 text-center">
                <h1 class="text-2xl font-bold">验证您的邮箱</h1>
                <p class="text-balance text-sm text-muted-foreground">
                    请点击我们发送给您的邮件中的链接来验证您的邮箱地址。如果您没有收到，我们可以重新发送验证链接。
                </p>
            </div>

            <div
                v-if="status === 'verification-link-sent'"
                class="text-center text-sm font-medium text-green-600 dark:text-green-400"
            >
                新的验证链接已发送到您的邮箱地址。
            </div>

            <div class="grid gap-6">
                <Button
                    type="submit"
                    class="w-full"
                    :disabled="form.processing"
                >
                    重新发送验证链接
                </Button>

                <div class="text-center text-sm">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="underline underline-offset-4"
                    >
                        退出登录
                    </Link>
                </div>
            </div>
        </form>
    </AuthenticationLayout>
</template>
