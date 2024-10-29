<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddPageRequest;
use App\Models\Page;
use App\Models\PageFile;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPanelPagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function pages()
    {
        return view('adminPanel.pages', []);
    }

    public function addPage(AddPageRequest $request)
    {
        $page = null;
        DB::transaction(function () use ($request, &$page) {
            $page = $this->createPage($request);
        });
        $pageId = $page ? $page->id : null;
        return response()->json([
            'pageId' => $pageId,
        ]);
    }

    protected function resetPageImages($page, $request)
    {
        $blocks = json_decode($page->body)->blocks;
        $imageUrls = [];
        foreach ($blocks as $block) {
            if ('image' === $block->type) {
                $imageUrls[] = $block->data->file->urlDb;
            }
        }
        $pageFiles = PageFile::whereIn('url', $imageUrls)
            ->when($request->pageId, function (Builder $query, string $pageId) {
                $query->orWhere('page_id', $pageId);
            })
            ->get();
        foreach ($pageFiles as $pageFile) {
            if (false !== array_search($pageFile->url, $imageUrls)) {
                $pageFile->page_id = $page->id;
            } else {
                $pageFile->page_id = null;
            }
            $pageFile->save();
        }
    }

    protected function createPage($request)
    {
        if ($request->pageId) {
            $page = Page::find($request->pageId);
            $page->update([
                'title' => $request->title,
                'slug' => $request->slug,
                'body' => $request->body,
            ]);
        } else {
            $page = Page::create([
                'title' => $request->title,
                'slug' => $request->slug,
                'body' => $request->body,
            ]);
        }
        $this->resetPageImages($page, $request);
        return $page;
    }

    public function getPage(Request $request)
    {
        $page = Page::where('id', $request->pageId)->first();
        return response()->json([
            'page' => $page,
        ]);
    }

    public function getPages(Request $request)
    {
        $pages = Page::all('id', 'title');
        return response()->json([
            'pages' => $pages,
        ]);
    }

    public function deletePage(Request $request)
    {
        Page::where('id', $request->pageId)->delete();
    }
}
