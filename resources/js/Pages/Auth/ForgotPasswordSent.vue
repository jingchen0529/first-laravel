<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AuthenticationLayout from '@/layouts/AuthenticationLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { MailOpen } from 'lucide-vue-next';
</script>

<template>
    <Head>
        <title>密码重置链接已发送</title>
    </Head>

    <AuthenticationLayout>
        <div class="flex flex-col gap-6">
            <div class="flex flex-col items-center gap-4 text-center">
                <div class="rounded-full bg-muted p-3">
                    <MailOpen class="size-6" />
                </div>
                <h1 class="text-2xl font-bold">请查收邮件</h1>
                <p class="text-balance text-sm text-muted-foreground">
                    我们已向您的邮箱发送了密码重置链接。请查看您的收件箱并按照说明重置密码。
                </p>
            </div>

            <div class="grid gap-4">
                <Button variant="outline" class="w-full">
                    <Link :href="route('login')">返回登录</Link>
                </Button>

                <div class="text-center text-sm">
                    没有收到邮件？
                    <Link
                        :href="route('auth.forgot-password')"
                        class="underline underline-offset-4"
                    >
                        重试
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticationLayout>
</template>
