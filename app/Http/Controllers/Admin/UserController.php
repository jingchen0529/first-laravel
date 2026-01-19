<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected string $indexPage = 'User/Index';
    protected string $formPage = 'User/Form';
    protected array $searchFields = ['status'];
    protected string $quickSearchField = 'name';

    public function __construct()
    {
        $this->model = new User();
    }

    /**
     * 保存新用户
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $data = $request->only(['name', 'email']);
        $data['password'] = Hash::make($request->password);

        $this->model->create($data);

        return $this->success('创建成功');
    }

    /**
     * 更新用户
     */
    public function update(Request $request, $id)
    {
        $row = $this->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|string|min:6',
        ]);

        $data = $request->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $row->update($data);

        return $this->success('更新成功');
    }

    /**
     * 删除用户
     */
    public function destroy($id)
    {
        $ids = is_array($id) ? $id : explode(',', $id);

        // 过滤掉 id=1（超级管理员不能删）
        $ids = array_filter($ids, fn($itemId) => $itemId != 1);

        if (empty($ids)) {
            return $this->error('超级管理员不能删除');
        }

        $count = $this->model->whereIn('id', $ids)->delete();

        return $this->success("成功删除 {$count} 条记录");
    }
}
