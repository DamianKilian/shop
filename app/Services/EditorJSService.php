<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\File;

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
                            $urlExplode = explode('/', $item->url);
                            $img->setAttribute('src', '/storage/' . env('THUMBNAILS_FOLDER') . '/' . end($urlExplode));
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

    protected static function resetImages($model, $type, $update, $imageUrls, $displayType)
    {
        $c = $type . '_id';
        $type2 = 'page' === $type ? 'product' : 'page';
        $c2 = $type2 . '_id';
        if ($update) {
            $filesOld = File::where($c, $model->id)
                ->whereDisplayType($displayType)
                ->get();
            foreach ($filesOld as $fileOld) {
                $key = array_search($fileOld->url, $imageUrls);
                if (false === $key) {
                    $fileOld->{$c} = null;
                    $fileOld->save();
                } else {
                    unset($imageUrls[$key]);
                }
            }
        }
        $files = File::where($c, null)
            ->where($c2, null)
            ->whereIn('url', $imageUrls)
            ->whereDisplayType($displayType)
            ->get();
        foreach ($files as $file) {
            $key = array_search($file->url, $imageUrls);
            if (false === $key) {
                continue;
            } else {
                $file->{$c} = $model->id;
                $file->save();
                unset($imageUrls[$key]);
            }
        }
    }

    public static function resetPageImages($model, $type, $update)
    {
        $json = 'page' === $type ? $model->body : $model->description;
        $blocks = json_decode($json)->blocks;
        $imageUrls = [];
        foreach ($blocks as $block) {
            if ('image' === $block->type) {
                $imageUrls[] = $block->data->file->urlDb;
            }
        }
        if ($imageUrls) {
            self::resetImages($model, $type, $update, $imageUrls, displayType: 'image');
        }
    }

    public static function resetPageGalleryImages($model, $type, $update)
    {
        $json = 'page' === $type ? $model->body : $model->description;
        $blocks = json_decode($json)->blocks;
        $imageUrls = [];
        foreach ($blocks as $block) {
            if ('gallery' !== $block->type) {
                continue;
            }
            foreach ($block->data->items as $item) {
                $imageUrls[] = $item->url;
            }
        }
        if ($imageUrls) {
            self::resetImages($model, $type, $update, $imageUrls, displayType: 'gallery');
        }
    }

    public static function resetAttachments($model, $type, $update)
    {
        $json = 'page' === $type ? $model->body : $model->description;
        $c = $type . '_id';
        $type2 = 'page' === $type ? 'product' : 'page';
        $c2 = $type2 . '_id';
        $blocks = json_decode($json)->blocks;
        $attachmentUrls = [];
        foreach ($blocks as $block) {
            if ('attaches' === $block->type) {
                $attachmentUrls[] = $block->data->file->urlDb;
            }
        }
        if (!$attachmentUrls) {
            return;
        }
        if ($update) {
            $attachmentsOld = Attachment::where($c, $model->id)->get();
            foreach ($attachmentsOld as $attachmentOld) {
                $key = array_search($attachmentOld->url, $attachmentUrls);
                if (false === $key) {
                    $attachmentOld->{$type . '_id'} = null;
                    $attachmentOld->save();
                } else {
                    unset($attachmentUrls[$key]);
                }
            }
        }
        $attachments = Attachment::where($c, null)
            ->where($c2, null)
            ->whereIn('url', $attachmentUrls)
            ->get();
        foreach ($attachments as $attachment) {
            $key = array_search($attachment->url, $attachmentUrls);
            if (false === $key) {
                continue;
            } else {
                $attachment->{$c} = $model->id;
                $attachment->save();
                unset($attachmentUrls[$key]);
            }
        }
    }
}
