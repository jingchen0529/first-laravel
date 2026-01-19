import { usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';

interface FlashMessages {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
}

/**
 * Flash 消息组合式函数
 *
 * 自动监听后端传来的 flash 消息并显示 toast
 */
export function useFlash() {
    const page = usePage();

    const flash = computed<FlashMessages>(() => page.props.flash as FlashMessages);

    // 监听 flash 消息变化并显示 toast
    watch(
        () => flash.value,
        (newFlash) => {
            if (newFlash?.success) {
                toast.success(newFlash.success);
            }
            if (newFlash?.error) {
                toast.error(newFlash.error);
            }
            if (newFlash?.warning) {
                toast.warning(newFlash.warning);
            }
            if (newFlash?.info) {
                toast.info(newFlash.info);
            }
        },
        { immediate: true }
    );

    return {
        flash,
    };
}
