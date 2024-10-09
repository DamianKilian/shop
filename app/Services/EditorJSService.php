<?php

namespace App\Services;

class EditorJSService
{
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
                    $doc->appendChild($el);
                    break;
                case 'paragraph':
                    $el = $doc->createElement('p');
                    $text = $doc->createTextNode($block->data->text);
                    $el->appendChild($text);
                    $doc->appendChild($el);
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
                    $doc->appendChild($el);
                    break;
            }
        }
        return html_entity_decode($doc->saveHTML());
    }
}
