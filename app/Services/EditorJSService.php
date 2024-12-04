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
                case 'imageExternal':
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
                case 'gallery':
                    $config = $block->data->config;
                    $el = $doc->createElement('div');
                    $el->setAttribute('class', "bs-lightbox");
                    if ('standard' === $config || 'masonry' === $config) {
                        $gallery = $doc->createElement('div');
                        $gallery->setAttribute('class', "$config gallery");
                        $el->appendChild($gallery);
                        foreach ($block->data->items as $item) {
                            $a = $doc->createElement('a');
                            $a->setAttribute('data-modal', 'bs-lightbox');
                            $a->setAttribute('href', $item->url);
                            $a->setAttribute('target', '_blank');
                            $a->setAttribute('data-caption', $item->caption);
                            $img = $doc->createElement('img');
                            $thumbnailUrl = preg_replace('/storage\//', 'storage/' . env('THUMBNAILS_FOLDER') . '/', $item->url, 1);
                            $img->setAttribute('src', $thumbnailUrl);
                            $a->appendChild($img);
                            $gallery->appendChild($a);
                        }
                    } elseif ('carousel' === $config) {
                        $items = $block->data->items;
                        $id = "gallery" . rand();
                        $carousel = $doc->createElement('div');
                        $carousel->setAttribute('class', "carousel slide");
                        $carousel->setAttribute('id', $id);
                        $el->appendChild($carousel);
                        $carouselIndicators = $doc->createElement('div');
                        $carouselIndicators->setAttribute('class', "carousel-indicators");
                        foreach ($items as $key => $item) {
                            $button = $doc->createElement('button');
                            $button->setAttribute('data-bs-target', "#$id");
                            $button->setAttribute('data-bs-slide-to', $key);
                            if (0 === $key) {
                                $button->setAttribute('class', "active");
                            }
                            $carouselIndicators->appendChild($button);
                        }
                        $carousel->appendChild($carouselIndicators);
                        $carouselInner = $doc->createElement('div');
                        $carouselInner->setAttribute('class', "carousel-inner");
                        foreach ($items as $key => $item) {
                            $carouselItem = $doc->createElement('div');
                            $carouselItem->setAttribute('class', 'carousel-item' . (0 === $key ? " active" : ''));
                            $img = $doc->createElement('img');
                            $img->setAttribute('class', 'd-block w-100');
                            $img->setAttribute('src', $item->url);
                            $carouselItem->appendChild($img);
                            $caption = $doc->createElement('div');
                            $caption->setAttribute('class', 'carousel-caption fs-5');
                            $caption->append($item->caption);
                            $carouselItem->appendChild($caption);
                            $carouselInner->appendChild($carouselItem);
                        }
                        $carousel->appendChild($carouselInner);
                        $carouselControl = <<<EOT
                            <button class="carousel-control-prev" data-bs-target="#$id" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" data-bs-target="#$id" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        EOT;
                        $carouselControlTemplate = $doc->createDocumentFragment();
                        $carouselControlTemplate->appendXML($carouselControl);
                        $carousel->appendChild($carouselControlTemplate);
                    }
                    break;
                case 'attaches':
                    $el = $doc->createElement('a');
                    $el->setAttribute('class', 'btn btn-outline-primary btn-sm d-block mb-2 text-start');
                    $el->setAttribute('href', $block->data->file->url);
                    $fileName = substr($block->data->title ?: 'attachment', 0, -4) . '.' . pathinfo($block->data->file->url, PATHINFO_EXTENSION);
                    $el->setAttribute('download', $fileName);
                    $i = $doc->createElement('i');
                    $i->setAttribute('class', 'fa-solid fa-paperclip me-2');
                    $el->appendChild($i);
                    $text = $doc->createTextNode($fileName . " - {$this->formatSizeUnits($block->data->file->size)}");
                    $el->appendChild($text);
                    break;
                case 'embed':
                    $el = $doc->createElement('figure');
                    $this->addClass($el, 'figure-editorjs');
                    $iframeDiv = $doc->createElement('div');
                    $this->addClass($iframeDiv, 'embed-tool');
                    $iframe = $doc->createElement('iframe');
                    $iframe->setAttribute('src', $block->data->embed);
                    $iframe->setAttribute('style', 'width:100%; max-width:560px; height:315px');
                    $iframe->setAttribute('frameborder', '0');
                    $iframe->setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
                    $iframe->setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
                    $iframe->setAttribute('allowfullscreen', 'true');
                    $iframeDiv->appendChild($iframe);
                    $figcaption = $doc->createElement('figcaption');
                    $caption = $doc->createTextNode($block->data->caption);
                    $figcaption->appendChild($caption);
                    $el->appendChild($iframeDiv);
                    $el->appendChild($figcaption);
                    break;
            }
            $doc->appendChild($el);
        }
        return html_entity_decode($doc->saveHTML());
    }

    protected function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
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
