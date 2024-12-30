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
            'previewUrl' => $request->preview ? route('home', ['slug' => env('PREVIEW_SLUG')]) : '',
        ]);
    }

    protected function createPage(Request $request)
    {
        $pageId = $request->pageId;
        if ('true' === $request->preview) {
            $page = Page::whereSlug(env('PREVIEW_SLUG'))->first();
            $pageId = $page->id;
            $page->update([
                'title' => $request->title,
                'body_prod' => $request->body,
            ]);
        } else {
            $vals = [
                'title' => $request->title,
                'slug' => $request->slug,
                'body' => $request->body,
            ];
            if ($pageId) {
                $page = Page::find($pageId);
                $page->update($vals);
            } else {
                $page = Page::create($vals);
            }
        }
        EditorJSService::resetPageImages($page, 'page', update: !!$pageId);
        EditorJSService::resetPageGalleryImages($page, 'page', update: !!$pageId);
        EditorJSService::resetAttachments($page, 'page', update: !!$pageId);
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
        $pages = Page::where('slug', '!=', env('PREVIEW_SLUG'))->orWhere('slug', null)->get(['id', 'title', 'active']);
        return response()->json([
            'pages' => $pages,
        ]);
    }

    public function toggleActive(Request $request)
    {
        $page = Page::whereId($request->pageId)->first();
        $page->active = $request->active;
        $page->save();
    }

    public function applyChanges(Request $request)
    {
        $page = Page::whereId($request->pageId)->first();
        $page->body_prod = $page->body;
        $page->save();
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
