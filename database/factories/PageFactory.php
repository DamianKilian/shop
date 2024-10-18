<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    public function definition(): array
    {
        $pageBodyArray = array(
            'time' => 1729269060460,
            'blocks' => array(0 => array(
                'id' => 'gM2YmfoYJC',
                'type' => 'paragraph',
                'data' => array('text' => 'aaaa',),
            ), 1 => array(
                'id' => 'yJ7a1OpjJo',
                'type' => 'paragraph',
                'data' => array('text' => 'bbbb',),
            ), 2 => array(
                'id' => 'knvHiCRklt',
                'type' => 'paragraph',
                'data' => array('text' => 'cccc',),
            ), 3 => array(
                'id' => 'AMFSAziZvQ',
                'type' => 'image',
                'data' => array(
                    'caption' => 'dddd',
                    'withBorder' => false,
                    'withBackground' => false,
                    'stretched' => false,
                    'file' => array(
                        'url' => 'http://localhost:8080/storage/pages/Ohg9PuiEHYqDUVNlsWT9OXA0pvbuTbZfXlyMhgpc.jpg',
                        'urlDb' => 'pages/Ohg9PuiEHYqDUVNlsWT9OXA0pvbuTbZfXlyMhgpc.jpg',
                    ),
                ),
            ), 4 => array(
                'id' => 'Z0bBpnqCkU',
                'type' => 'image',
                'data' => array(
                    'caption' => 'dddd2',
                    'withBorder' => false,
                    'withBackground' => false,
                    'stretched' => false,
                    'file' => array(
                        'url' => 'http://localhost:8080/storage/pages/1dv9n7O5lo2yRuAYlKjCFg8nU2JHChA53cAAJpeO.jpg',
                        'urlDb' => 'pages/1dv9n7O5lo2yRuAYlKjCFg8nU2JHChA53cAAJpeO.jpg',
                    ),
                ),
            ),),
            'version' => '2.30.6',
        );
        $pageBody = json_encode($pageBodyArray, JSON_UNESCAPED_SLASHES);
        return [
            'title' => 'title',
            'body' => $pageBody,
            'slug' => 'slug',
        ];
    }
}
