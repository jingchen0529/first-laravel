<script setup lang="ts">
import ErrorFeedback from '@/components/custom/ErrorFeedback.vue';
import {
    PinInput,
    PinInputGroup,
    PinInputInput,
} from '@/components/ui/pin-input';
import AuthenticationLayout from '@/layouts/AuthenticationLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ShieldCheck } from 'lucide-vue-next';
import { ref } from 'vue';

const pinInputCode = ref<string[]>([]);
const form = useForm({
    code: '',
});

const handleChange = async () => {
    form.code = pinInputCode.value.join('');
    if (form.code.length !== 6 || form.processing) return;

    form.processing = true;
    form.post(route('two-factor.login.store'), {
        preserveScroll: true,
        preserveState: true,
        onError: () => {
            form.code = '';
            pinInputCode.value = [];
        },
        onFinish: () => {
            form.processing = false;
            form.code = '';
            pinInputCode.value = [];
        },
    });
};
</script>

<template>
    <Head>
        <title>双因素认证</title>
    </Head>

    <AuthenticationLayout>
        <div class="flex flex-col items-center gap-6 text-center">
            <div class="flex flex-col items-center gap-4">
                <ShieldCheck class="size-12" />
                <h1 class="text-2xl font-bold">双因素认证</h1>
            </div>

            <PinInput
                v-model="pinInputCode"
                length="6"
                @complete="handleChange"
                autofocus
            >
                <PinInputGroup>
                    <PinInputInput
                        v-for="(id, index) in 6"
                        :key="id"
                        :index="index"
                    />
                </PinInputGroup>
            </PinInput>

            <p class="text-balance text-sm text-muted-foreground">
                请输入您身份验证器应用中的一次性密码。
            </p>

            <ErrorFeedback :message="form.errors.code" />
        </div>
    </AuthenticationLayout>
</template>
