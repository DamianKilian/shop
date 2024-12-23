<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddPageRequest;
use App\Models\Attachment;
use App\Models\File;
use App\Models\Page;
use App\Services\EditorJSService;
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

    protected function createPage(Request $request)
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
        EditorJSService::resetPageImages($page, 'page', update: !!$request->pageId);
        EditorJSService::resetPageGalleryImages($page, 'page', update: !!$request->pageId);
        EditorJSService::resetAttachments($page, 'page', update: !!$request->pageId);
        return $page;
    }

    public function getPage(Request $request)
    {
        $page = Page::where('id', $request->pageId)->first();
        return response()->json([
            'page' => $page,
        ]);
    }

    public function getPages()
    {
        $pages = Page::all('id', 'title');
        return response()->json([
            'pages' => $pages,
        ]);
    }

    public function deletePage(Request $request)
    {
        File::where('page_id', $request->pageId)
            ->update(['page_id' => null]);
        Attachment::where('page_id', $request->pageId)
            ->update(['page_id' => null]);
        Page::where('id', $request->pageId)->delete();
    }
}
