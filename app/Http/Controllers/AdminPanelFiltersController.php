<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddFilterRequest;
use App\Models\Category;
use App\Models\Filter;
use App\Models\FilterOption;
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

    public function deleteFilters(Request $request)
    {
        foreach ($request->filters as $filter) {
            $filterIds[] = $filter['id'];
        }
        Filter::whereIn('id', $filterIds)->delete();
    }

    public function addFilter(AddFilterRequest $request)
    {
        DB::transaction(function () use ($request) {
            $filter = $this->createFilter($request);
            if ($request->filterOptionIds) {
                $this->addFilterOptions($request, $filter);
            }
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
        return $filter;
    }

    protected function addFilterOptions($request, $filter)
    {
        foreach ($request->filterOptionIds as $key => $optionId) {
            if ($optionId) {
                $option = FilterOption::find($optionId);
                if ("true" === $request->filterOptionRemoves[$key]) {
                    $option->delete();
                } else {
                    $option->update([
                        'name' => $request->filterOptionNames[$key],
                        'order_priority' => $request->filterOptionOrderPriorities[$key],
                        'filter_id' => $filter->id,
                    ]);
                }
            } else {
                FilterOption::create([
                    'name' => $request->filterOptionNames[$key],
                    'order_priority' => $request->filterOptionOrderPriorities[$key],
                    'filter_id' => $filter->id,
                ]);
            }
        }
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
