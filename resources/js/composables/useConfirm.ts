import { ref } from 'vue';

interface ConfirmOptions {
    title?: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    variant?: 'default' | 'destructive';
}

/**
 * 确认对话框组合式函数
 */
export function useConfirm() {
    const isOpen = ref(false);
    const options = ref<ConfirmOptions>({
        title: '确认操作',
        message: '',
        confirmText: '确认',
        cancelText: '取消',
        variant: 'default',
    });

    let resolvePromise: ((value: boolean) => void) | null = null;

    const confirm = (opts: ConfirmOptions): Promise<boolean> => {
        options.value = {
            title: opts.title ?? '确认操作',
            message: opts.message,
            confirmText: opts.confirmText ?? '确认',
            cancelText: opts.cancelText ?? '取消',
            variant: opts.variant ?? 'default',
        };
        isOpen.value = true;

        return new Promise((resolve) => {
            resolvePromise = resolve;
        });
    };

    const handleConfirm = () => {
        isOpen.value = false;
        resolvePromise?.(true);
    };

    const handleCancel = () => {
        isOpen.value = false;
        resolvePromise?.(false);
    };

    return {
        isOpen,
        options,
        confirm,
        handleConfirm,
        handleCancel,
    };
}
