<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * 模型实例
     */
    protected ?Model $model = null;

    /**
     * 列表页面组件路径
     */
    protected string $indexPage = '';

    /**
     * 详情/编辑页面组件路径
     */
    protected string $formPage = '';

    /**
     * 列表查询字段
     */
    protected array $indexFields = ['*'];

    /**
     * 排除入库的字段
     */
    protected array $excludeFields = [];

    /**
     * 默认排序字段
     */
    protected string $orderField = 'id';

    /**
     * 默认排序方向
     */
    protected string $orderDirection = 'desc';

    /**
     * 每页数量
     */
    protected int $perPage = 15;

    /**
     * 是否开启模型验证
     */
    protected bool $modelValidate = false;

    /**
     * 验证场景
     */
    protected bool $modelSceneValidate = true;

    /**
     * 关联预加载
     */
    protected array $withRelations = [];

    /**
     * 可搜索字段
     */
    protected array $searchFields = [];

    /**
     * 快速搜索字段（模糊匹配）
     */
    protected string $quickSearchField = '';

    /**
     * 渲染 Inertia 页面
     */
    protected function render(string $component, array $props = []): Response
    {
        return Inertia::render($component, $props);
    }

    /**
     * 成功响应
     */
    protected function success(string $message = '操作成功', array $data = [])
    {
        return back()->with('success', $message)->with('data', $data);
    }

    /**
     * 错误响应
     */
    protected function error(string $message = '操作失败')
    {
        return back()->with('error', $message);
    }

    /**
     * 列表查询
     */
    public function index(Request $request)
    {
        $query = $this->model->newQuery();

        // 关联预加载
        if (!empty($this->withRelations)) {
            $query->with($this->withRelations);
        }

        // 构建查询条件
        $query = $this->buildSearchQuery($query, $request);

        // 排序
        $orderField = $request->input('sort', $this->orderField);
        $orderDirection = $request->input('order', $this->orderDirection);
        $query->orderBy($orderField, $orderDirection);

        // 分页
        $perPage = $request->input('per_page', $this->perPage);
        $list = $query->select($this->indexFields)->paginate($perPage);

        return $this->render($this->indexPage, [
            'list' => $list,
            'filters' => $request->only(array_merge($this->searchFields, ['keyword', 'sort', 'order'])),
        ]);
    }

    /**
     * 构建搜索查询
     */
    protected function buildSearchQuery($query, Request $request)
    {
        // 快速搜索
        if ($this->quickSearchField && $keyword = $request->input('keyword')) {
            $query->where($this->quickSearchField, 'like', "%{$keyword}%");
        }

        // 精确搜索
        foreach ($this->searchFields as $field) {
            if ($value = $request->input($field)) {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        return $query;
    }

    /**
     * 创建页面
     */
    public function create()
    {
        return $this->render($this->formPage, [
            'row' => null,
        ]);
    }

    /**
     * 保存新记录
     */
    public function store(Request $request)
    {
        $data = $this->excludeFields($request->all());

        // 模型验证
        if ($this->modelValidate) {
            $this->validateData($data, 'create');
        }

        DB::beginTransaction();
        try {
            $this->model->fill($data)->save();
            DB::commit();
            return $this->success('创建成功');
        } catch (Throwable $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    /**
     * 详情页面
     */
    public function show($id)
    {
        $row = $this->findOrFail($id);

        return $this->render($this->formPage, [
            'row' => $row,
            'readonly' => true,
        ]);
    }

    /**
     * 编辑页面
     */
    public function edit($id)
    {
        $row = $this->findOrFail($id);

        return $this->render($this->formPage, [
            'row' => $row,
        ]);
    }

    /**
     * 更新记录
     */
    public function update(Request $request, $id)
    {
        $row = $this->findOrFail($id);
        $data = $this->excludeFields($request->all());

        // 模型验证
        if ($this->modelValidate) {
            $this->validateData($data, 'update');
        }

        DB::beginTransaction();
        try {
            $row->fill($data)->save();
            DB::commit();
            return $this->success('更新成功');
        } catch (Throwable $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    /**
     * 删除记录
     */
    public function destroy($id)
    {
        $ids = is_array($id) ? $id : explode(',', $id);

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($ids as $itemId) {
                $row = $this->model->find($itemId);
                if ($row) {
                    $row->delete();
                    $count++;
                }
            }
            DB::commit();

            if ($count > 0) {
                return $this->success("成功删除 {$count} 条记录");
            }
            return $this->error('没有记录被删除');
        } catch (Throwable $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    /**
     * 批量删除
     */
    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return $this->error('请选择要删除的记录');
        }

        return $this->destroy($ids);
    }

    /**
     * 排除字段
     */
    protected function excludeFields(array $data): array
    {
        foreach ($this->excludeFields as $field) {
            unset($data[$field]);
        }
        // 默认排除这些字段
        unset($data['_token'], $data['_method']);
        return $data;
    }

    /**
     * 查找记录或抛出404
     */
    protected function findOrFail($id): Model
    {
        $query = $this->model->newQuery();

        if (!empty($this->withRelations)) {
            $query->with($this->withRelations);
        }

        return $query->findOrFail($id);
    }

    /**
     * 模型验证
     */
    protected function validateData(array $data, string $scene = ''): void
    {
        $validateClass = str_replace('\\Models\\', '\\Http\\Requests\\', get_class($this->model)) . 'Request';

        if (class_exists($validateClass)) {
            $rules = app($validateClass)->rules();
            $messages = app($validateClass)->messages();

            $this->validate(request(), $rules, $messages);
        }
    }

    /**
     * 修改状态
     */
    public function changeStatus(Request $request, $id)
    {
        $row = $this->findOrFail($id);
        $field = $request->input('field', 'status');
        $value = $request->input('value');

        if (!in_array($field, $row->getFillable())) {
            return $this->error('不允许修改该字段');
        }

        try {
            $row->{$field} = $value;
            $row->save();
            return $this->success('状态更新成功');
        } catch (Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 排序
     */
    public function sort(Request $request)
    {
        $ids = $request->input('ids', []);
        $field = $request->input('field', 'sort');

        DB::beginTransaction();
        try {
            foreach ($ids as $index => $id) {
                $this->model->where($this->model->getKeyName(), $id)
                    ->update([$field => count($ids) - $index]);
            }
            DB::commit();
            return $this->success('排序更新成功');
        } catch (Throwable $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    /**
     * 导出数据（子类可重写）
     */
    public function export(Request $request)
    {
        return $this->error('导出功能未实现');
    }

    /**
     * Select 下拉数据
     */
    public function select(Request $request)
    {
        $query = $this->model->newQuery();
        $query = $this->buildSearchQuery($query, $request);

        $labelField = $request->input('label', 'name');
        $valueField = $request->input('value', 'id');

        $list = $query->select([$valueField, $labelField])->get()->map(function ($item) use ($labelField, $valueField) {
            return [
                'label' => $item->{$labelField},
                'value' => $item->{$valueField},
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $list,
        ]);
    }
}
