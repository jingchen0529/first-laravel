<script setup lang="ts">
import ConfirmWithPassword from '@/components/custom/ConfirmWithPassword.vue';
import ErrorFeedback from '@/components/custom/ErrorFeedback.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
    PinInput,
    PinInputGroup,
    PinInputInput,
} from '@/components/ui/pin-input';
import { useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { CheckCircle, Siren } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

// State
const enabling = ref(false);
const disabling = ref(false);
const qrCode = ref<string | null>(null);
const recoveryCodes = ref<string[]>([]);
const pinInputCode = ref<string[]>([]);

// Form
const form = useForm({
    code: '',
});

// User data
const page = usePage();
const auth = computed(() => page.props.auth);
const user = computed(() => auth.value.user);
const twoFactorEnabled = ref(user.value.two_factor_confirmed_at !== null);

// Functions
function enableTwoFactorAuthentication() {
    enabling.value = true;

    form.post(route('two-factor.enable'), {
        onSuccess: () => {
            enabling.value = false;
            showQrCode();
        },
    });
}

function showQrCode() {
    axios
        .get(route('two-factor.qr-code'))
        .then((response) => {
            qrCode.value = response.data.svg;
        })
        .then(() => {
            toast.success('双因素认证二维码已生成');
        });
}

function confirmTwoFactorAuthentication(e: Event) {
    e.preventDefault();
    form.code = pinInputCode.value.join('');

    form.post(route('two-factor.confirm'), {
        errorBag: 'confirmTwoFactorAuthentication',
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            qrCode.value = null;
            twoFactorEnabled.value = true;
            showRecoveryCodes();
            toast.success('双因素认证已成功启用');
        },
        onError: () => {
            toast.error('启用双因素认证时出错');
        },
    });
}

function showRecoveryCodes() {
    axios.get(route('two-factor.recovery-codes')).then((response) => {
        recoveryCodes.value = response.data;
    });
}

function disableTwoFactorAuthentication() {
    disabling.value = true;

    form.delete(route('two-factor.disable'), {
        preserveScroll: true,
        preserveState: true,
        onBefore: () => {
            disabling.value = true;
        },
        onSuccess: () => {
            disabling.value = false;
            twoFactorEnabled.value = false;
            toast.success('双因素认证已成功禁用');
        },
        onError: () => {
            disabling.value = false;
            twoFactorEnabled.value = true;
            toast.error('禁用双因素认证时出错');
        },
        onFinish: () => {
            disabling.value = false;
        },
    });
}
</script>

<template>
    <section class="flex max-w-xl flex-col gap-6">
        <header class="flex flex-col gap-2">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                双因素认证
            </h2>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                使用双因素认证为您的账户添加额外的安全保护。
            </p>
        </header>

        <template v-if="!twoFactorEnabled && !qrCode">
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4 text-sm">
                    <Siren class="text-red-500" />
                    <h3>
                        双因素认证尚未启用。我们建议您启用它。
                    </h3>
                </div>
                <ConfirmWithPassword
                    title="启用双因素认证"
                    description="要开始启用双因素认证，您必须确认您的密码。"
                    @confirm="enableTwoFactorAuthentication"
                >
                    <Button type="button" :disabled="enabling">
                        {{ enabling ? '启用中...' : '启用' }}
                    </Button>
                </ConfirmWithPassword>
            </div>
        </template>

        <template v-if="qrCode">
            <div class="flex flex-col gap-6">
                <p
                    class="text-balance text-sm font-medium text-gray-900 dark:text-gray-100"
                >
                    要完成双因素认证的启用，请使用手机上的身份验证器应用扫描以下二维码，或输入设置密钥，然后提供生成的一次性密码。
                </p>

                <div class="contrast-200" v-html="qrCode"></div>

                <div class="flex flex-col gap-2">
                    <form @submit="confirmTwoFactorAuthentication">
                        <Label for="code">验证码</Label>

                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <PinInput
                                    v-model="pinInputCode"
                                    length="6"
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

                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    {{
                                        form.processing
                                            ? '确认中...'
                                            : '确认'
                                    }}
                                </Button>
                            </div>
                            <ErrorFeedback :message="form.errors.code" />
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <template v-if="twoFactorEnabled && !qrCode">
            <div class="flex flex-col gap-6">
                <div class="flex items-center gap-4 text-sm">
                    <CheckCircle class="text-green-500" />
                    <h3>您已启用双因素认证。</h3>
                </div>
                <template v-if="recoveryCodes.length > 0">
                    <div class="flex max-w-xl flex-col gap-4">
                        <div class="text-sm">
                            <span
                                class="font-semibold text-red-600 dark:text-red-400"
                            >
                                重要提示：
                            </span>
                            这些恢复代码只会显示一次。请立即将它们存储在安全的密码管理器中。如果您的双因素认证设备丢失，可以使用这些代码恢复对账户的访问。
                        </div>

                        <div
                            class="bg-sidebar grid gap-1 rounded-lg p-4 font-mono text-xs"
                        >
                            <div v-for="code in recoveryCodes" :key="code">
                                {{ code }}
                            </div>
                        </div>
                    </div>
                </template>
                <div class="flex gap-4">
                    <ConfirmWithPassword
                        title="禁用双因素认证"
                        description="要禁用双因素认证，您必须确认您的密码。"
                        @confirm="disableTwoFactorAuthentication"
                    >
                        <Button type="button" :disabled="disabling">
                            {{ disabling ? '禁用中...' : '禁用' }}
                        </Button>
                    </ConfirmWithPassword>
                </div>
            </div>
        </template>
    </section>
</template>
