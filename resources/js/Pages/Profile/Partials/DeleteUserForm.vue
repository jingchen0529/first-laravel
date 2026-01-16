<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Props {
    className?: string;
}

defineProps<Props>();

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement>();

const form = useForm({
    password: '',
});

function confirmUserDeletion() {
    confirmingUserDeletion.value = true;
}

function deleteUser(e: Event) {
    e.preventDefault();

    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
    });
}

function closeModal() {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
}
</script>

<template>
    <section :class="`flex max-w-xl flex-col gap-6 ${className}`">
        <header class="flex flex-col gap-2">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                删除账户
            </h2>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                一旦您的账户被删除，所有资源和数据将被永久删除。在删除账户之前，请下载您希望保留的任何数据或信息。
            </p>
        </header>

        <Button
            variant="destructive"
            class="w-fit"
            @click="confirmUserDeletion"
        >
            删除账户
        </Button>
    </section>

    <Dialog
        :open="confirmingUserDeletion"
        @update:open="(open) => !open && closeModal()"
    >
        <DialogContent>
            <DialogTitle
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
            >
                您确定要删除您的账户吗？
            </DialogTitle>

            <DialogDescription class="text-sm text-gray-600 dark:text-gray-400">
                一旦您的账户被删除，所有资源和数据将被永久删除。请输入您的密码以确认您要永久删除您的账户。
            </DialogDescription>

            <form @submit="deleteUser" class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <Label for="password">密码</Label>

                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        placeholder="••••••••"
                        ref="passwordInput"
                    />

                    <p
                        v-if="form.errors.password"
                        class="mt-2 text-sm text-red-600 dark:text-red-400"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>

                <div class="mt-6 flex justify-end gap-4">
                    <Button type="button" variant="ghost" @click="closeModal">
                        取消
                    </Button>

                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="form.processing"
                    >
                        删除账户
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>
