<?php

namespace App\Builder;

use Illuminate\Database\Eloquent\Builder;

class DataTableBuilder extends Builder
{
    /**
     * Helper function to convert custom datatable
     * @method datatable()
     * @param array $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function datatables(array $search = [], int $perpage = 10)
    {
        $accept_show = ['5', '10', '20', '50'];
        $q = $this;
        // use query where and then pass anonymous function 
        $q->where(function ($query) use ($search) {
            foreach ($search as $s) {
                // spesific search
                $query->orWhere($this->model->getTable() . '.' . $s, 'LIKE', "%" . request('search') . "%");
            }
        });
        $paginate = $q->paginate(in_array(request('show'), $accept_show) ? request('show') ?: $perpage : $perpage)->appends(request()->query())->onEachSide(1);
        return $paginate;
    }
}
