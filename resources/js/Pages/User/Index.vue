<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Trash2, Plus, Search, RefreshCw } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    created_at: string;
}

interface PaginatedData {
    data: User[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

const props = defineProps<{
    list: PaginatedData;
    filters: {
        keyword?: string;
    };
}>();

const keyword = ref(props.filters.keyword || '');
const selectedIds = ref<number[]>([]);
const showFormDialog = ref(false);
const showDeleteDialog = ref(false);
const editingUser = ref<User | null>(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
});

const isAllSelected = computed(() => {
    return props.list.data.length > 0 && selectedIds.value.length === props.list.data.length;
});

const isEdit = computed(() => !!editingUser.value);

function toggleSelectAll() {
    if (isAllSelected.value) {
        selectedIds.value = [];
    } else {
        selectedIds.value = props.list.data.map(user => user.id);
    }
}

function toggleSelect(id: number) {
    const index = selectedIds.value.indexOf(id);
    if (index > -1) {
        selectedIds.value.splice(index, 1);
    } else {
        selectedIds.value.push(id);
    }
}

function isSelected(id: number) {
    return selectedIds.value.includes(id);
}

function refresh() {
    router.reload();
}

function search() {
    router.get('/user', { keyword: keyword.value }, { preserveState: true });
}

function openCreateDialog() {
    editingUser.value = null;
    form.reset();
    form.clearErrors();
    showFormDialog.value = true;
}

function openEditDialog(user: User) {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.clearErrors();
    showFormDialog.value = true;
}

function submitForm() {
    if (isEdit.value) {
        form.put(route('user.update', editingUser.value!.id), {
            onSuccess: () => {
                showFormDialog.value = false;
                form.reset();
            },
        });
    } else {
        form.post(route('user.store'), {
            onSuccess: () => {
                showFormDialog.value = false;
                form.reset();
            },
        });
    }
}

function confirmBatchDelete() {
    if (selectedIds.value.length === 0) return;
    showDeleteDialog.value = true;
}

function handleBatchDelete() {
    const ids = selectedIds.value.filter(id => id !== 1);
    if (ids.length === 0) {
        showDeleteDialog.value = false;
        return;
    }

    router.delete(`/user/${ids.join(',')}`, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            selectedIds.value = [];
        },
    });
}

function goToPage(page: number) {
    router.get('/user', { keyword: keyword.value, page }, { preserveState: true });
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleString('zh-CN');
}

function maskEmail(email: string) {
    const [name, domain] = email.split('@');
    if (name.length <= 2) return email;
    return name.substring(0, 2) + '***@' + domain;
}
</script>

<template>
    <Head title="用户管理" />
    <AuthenticatedLayout>
        <div class="flex max-w-7xl flex-col sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <Button variant="outline" size="sm" @click="refresh">
                            <RefreshCw class="w-4 h-4 mr-1" />
                            刷新
                        </Button>
                        <Button  size="sm" @click="openCreateDialog">
                            <Plus class="w-4 h-4 mr-1" />
                            新增
                        </Button>
                        <Button
                            variant="outline"
                            size="sm"
                            class="text-red-500 hover:text-red-600"
                            :disabled="selectedIds.length === 0"
                            @click="confirmBatchDelete"
                        >
                            <Trash2 class="w-4 h-4 mr-1" />
                            删除
                        </Button>
                    </div>
                    <div class="flex items-center gap-2">
                        <Input
                            v-model="keyword"
                            placeholder="搜索用户名..."
                            class="w-64 h-9"
                            @keyup.enter="search"
                        />
                        <Button variant="outline" size="sm" @click="search">
                            <Search class="w-4 h-4" />
                        </Button>
                    </div>
                </div>

                <div class="border rounded-lg overflow-hidden">
                    <Table>
                        <TableHeader class="bg-gray-50">
                            <TableRow>
                                <TableHead class="w-12">
                                    <input
                                        type="checkbox"
                                        :checked="isAllSelected"
                                        @change="toggleSelectAll"
                                        class="h-4 w-4 rounded border-gray-300"
                                    />
                                </TableHead>
                                <TableHead class="w-20">ID</TableHead>
                                <TableHead>用户名</TableHead>
                                <TableHead>邮箱</TableHead>
                                <TableHead>创建时间</TableHead>
                                <TableHead class="w-32 text-center">操作</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="user in list.data" :key="user.id" class="hover:bg-gray-50">
                                <TableCell>
                                    <input
                                        type="checkbox"
                                        :checked="isSelected(user.id)"
                                        @change="toggleSelect(user.id)"
                                        class="h-4 w-4 rounded border-gray-300"
                                    />
                                </TableCell>
                                <TableCell>{{ user.id }}</TableCell>
                                <TableCell>{{ user.name }}</TableCell>
                                <TableCell>{{ user.email }}</TableCell>
                                <TableCell>{{ formatDate(user.created_at) }}</TableCell>
                                <TableCell class="text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 w-8 p-0"
                                            @click="openEditDialog(user)"
                                        >
                                            <Pencil class="w-4 h-4" />
                                        </Button>
                                        <Button
                                            v-if="user.id !== 1"
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 w-8 p-0"
                                            @click="selectedIds = [user.id]; confirmBatchDelete()"
                                        >
                                            <Trash2 class="w-4 h-4 text-red-500" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex items-center justify-between text-sm text-gray-600 mt-4">
                    <span>共 {{ list.total }} 条记录，已选 {{ selectedIds.length }} 条</span>
                    <div class="flex items-center gap-2">
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="list.current_page <= 1"
                            @click="goToPage(list.current_page - 1)"
                        >
                            上一页
                        </Button>
                        <span>第 {{ list.current_page }} / {{ list.last_page }} 页</span>
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="list.current_page >= list.last_page"
                            @click="goToPage(list.current_page + 1)"
                        >
                            下一页
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 新增/编辑弹窗 -->
        <Dialog v-model:open="showFormDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ isEdit ? '编辑用户' : '新增用户' }}</DialogTitle>
                    <DialogDescription>
                        {{ isEdit ? '修改用户信息' : '填写用户信息' }}
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="name">用户名</Label>
                        <Input
                            id="name"
                            v-model="form.name"
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
                            <span v-if="isEdit" class="text-xs text-gray-500 ml-1">(留空则不修改)</span>
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

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="showFormDialog = false">
                            取消
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? '提交中...' : '保存' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- 删除确认弹窗 -->
        <AlertDialog v-model:open="showDeleteDialog">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>确认删除</AlertDialogTitle>
                    <AlertDialogDescription>
                        确定要删除选中的 {{ selectedIds.filter(id => id !== 1).length }} 条用户吗？删除后无法恢复。
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>取消</AlertDialogCancel>
                    <AlertDialogAction @click="handleBatchDelete">删除</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AuthenticatedLayout>
</template>
