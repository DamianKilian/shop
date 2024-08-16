<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddFilterRequest;
use App\Models\Category;
use App\Models\Filter;
use App\Services\CategoryService;
use App\Services\FilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPanelFiltersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function filters()
    {
        $categories = Category::orderBy('parent_id')->orderBy('position')->get();
        $categoryOptions = CategoryService::categoryOptions($categories);
        return view('adminPanel.filters', [
            'categoryOptions' => json_encode($categoryOptions),
        ]);
    }

    public function addFilter(AddFilterRequest $request)
    {
        DB::transaction(function () use ($request) {
            $this->createFilter($request);
        });
    }

    protected function createFilter($request)
    {
        if ($request->filterId) {
            $filter = Filter::find($request->filterId);
            $filter->update([
                'name' => $request->name,
                'order_priority' => $request->order_priority,
            ]);
        } else {
            $filter = Filter::create([
                'name' => $request->name,
                'order_priority' => $request->order_priority,
            ]);
        }
        return $filter->id;
    }

    public function getFilters(Request $request)
    {
        $categoryChildrenIds = [];
        if ($request->categoryId) {
            $categoryChildrenIds = CategoryService::getCategoryChildrenIds([$request->categoryId]);
        }
        $filters = FilterService::getFilters($request, $categoryChildrenIds, 20);
        return response()->json([
            'filters' => $filters,
        ]);
    }
}
