<?php

namespace App\Services;

use App\Models\PageFile;
use App\Models\ProductFile;
use Illuminate\Contracts\Database\Query\Builder;

class EditorJSService
{
    protected function addClass(&$el, $class)
    {
        $el->setAttribute('class', $el->getAttribute('class') . ' ' . $class);
    }

    public function toHtml($json)
    {
        $json = json_decode($json);
        $blocks = $json->blocks;
        $doc = new \DOMDocument();
        $doc->substituteEntities = false;
        foreach ($blocks as  $block) {
            switch ($block->type) {
                case 'header':
                    $el = $doc->createElement('h' . $block->data->level);
                    $text = $doc->createTextNode($block->data->text);
                    $el->appendChild($text);
                    break;
                case 'paragraph':
                    $el = $doc->createElement('p');
                    $text = $doc->createTextNode($block->data->text);
                    $el->appendChild($text);
                    break;
                case 'list':
                    $listType = 'unordered' === $block->data->style ? 'ul' : 'ol';
                    $el = $doc->createElement($listType);
                    foreach ($block->data->items as $liText) {
                        $li = $doc->createElement('li');
                        $text = $doc->createTextNode($liText);
                        $li->appendChild($text);
                        $el->appendChild($li);
                    }
                    break;
                case 'image':
                    $el = $doc->createElement('figure');
                    $this->addClass($el, 'figure-editorjs');
                    $imgDiv = $doc->createElement('div');
                    $img = $doc->createElement('img');
                    $imgDiv->appendChild($img);
                    if ($block->data->withBorder) {
                        $this->addClass($imgDiv, 'border');
                    }
                    if ($block->data->stretched) {
                        if ($block->data->withBackground) {
                            $wClass = 'w-75';
                        } else {
                            $wClass = 'w-100';
                        }
                        $this->addClass($img, $wClass);
                    }
                    if ($block->data->withBackground) {
                        $this->addClass($imgDiv, 'bg-light p-2 text-center');
                    }
                    $img->setAttribute('src', $block->data->file->url);
                    $img->setAttribute('alt', $block->data->caption);
                    $figcaption = $doc->createElement('figcaption');
                    $caption = $doc->createTextNode($block->data->caption);
                    $figcaption->appendChild($caption);
                    $el->appendChild($imgDiv);
                    $el->appendChild($figcaption);
                    break;
            }
            $doc->appendChild($el);
        }
        return html_entity_decode($doc->saveHTML());
    }

    public static function resetPageImages($model, $request, $type = 'page')
    {
        $json = 'page' === $type ? $model->body : $model->description;
        $blocks = json_decode($json)->blocks;
        $imageUrls = [];
        foreach ($blocks as $block) {
            if ('image' === $block->type) {
                $imageUrls[] = $block->data->file->urlDb;
            }
        }
        if ('product' === $type) {
            $files = ProductFile::whereIn('url', $imageUrls)
                ->when($request->productId, function (Builder $query, string $productId) {
                    $query->orWhere('product_id', $productId);
                })
                ->get();
        } elseif ('page' === $type) {
            $files = PageFile::whereIn('url', $imageUrls)
                ->when($request->pageId, function (Builder $query, string $pageId) {
                    $query->orWhere('page_id', $pageId);
                })
                ->get();
        }
        foreach ($files as $file) {
            if (false !== array_search($file->url, $imageUrls)) {
                $file->{$type . '_id'} = $model->id;
            } else {
                $file->{$type . '_id'} = null;
            }
            $file->save();
        }
    }
}
