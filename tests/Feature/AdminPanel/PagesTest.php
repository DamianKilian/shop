<?php

namespace Tests\Feature\AdminPanel;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\PageFile;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_addPage(): void
    {
        $user = User::factory()->create();
        $urlDb1 = 'pages/urlDb1.jpg';
        $urlDb2 = 'pages/urlDb2.jpg';
        PageFile::factory()->count(3)->create();
        PageFile::factory()->create([
            'url' => $urlDb1,
        ]);
        PageFile::factory()->create([
            'url' => $urlDb2,
        ]);
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
                        'url' => env('APP_URL') . '/storage/' . $urlDb1,
                        'urlDb' => $urlDb1,
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
                        'url' => env('APP_URL') . '/storage/' . $urlDb2,
                        'urlDb' => $urlDb2,
                    ),
                ),
            ),),
            'version' => '2.30.6',
        );
        $pageBody = json_encode($pageBodyArray, JSON_UNESCAPED_SLASHES);

        $response = $this->actingAs($user)->postJson('/admin-panel/add-page', [
            'title' => 'title',
            'slug' => 'slug',
            'body' => $pageBody,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('page_files', 5);
        $this->assertEquals(2, PageFile::where('page_id', $response['pageId'])->count());
        $this->assertDatabaseHas('page_files', [
            'url' => $urlDb1,
            'page_id' => $response['pageId'],
        ]);
        $this->assertDatabaseHas('page_files', [
            'url' => $urlDb2,
            'page_id' => $response['pageId'],
        ]);
    }
}