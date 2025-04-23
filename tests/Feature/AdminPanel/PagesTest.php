<?php

namespace Tests\Feature\AdminPanel;

use App\Models\Attachment;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\File;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_addPage_edit(): void
    {
        $urlDbOld = 'pages/urlDbOld.jpg';
        $urlDbNew = 'pages/urlDbNew.jpg';
        $urlDbRemoved = 'pages/urlDbRemoved.jpg';
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
                        'url' => config('app.url') . '/storage/' . $urlDbOld,
                        'urlDb' => $urlDbOld,
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
                        'url' => config('app.url') . '/storage/' . $urlDbRemoved,
                        'urlDb' => $urlDbRemoved,
                    ),
                ),
            ),),
            'version' => '2.30.6',
        );
        $pageBody = json_encode($pageBodyArray, JSON_UNESCAPED_SLASHES);
        $page = Page::factory()->create([
            'body' => $pageBody,
        ]);
        File::factory()->count(3)->create();
        File::factory()->create([
            'url' => $urlDbOld,
            'display_type' => 'image',
            'page_id' => $page->id,
        ]);
        File::factory()->create([
            'url' => $urlDbNew,
            'display_type' => 'image',
        ]);
        File::factory()->create([
            'url' => $urlDbRemoved,
            'display_type' => 'image',
            'page_id' => $page->id,
        ]);
        $pageBodyArrayNew = array(
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
                        'url' => config('app.url') . '/storage/' . $urlDbOld,
                        'urlDb' => $urlDbOld,
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
                        'url' => config('app.url') . '/storage/' . $urlDbNew,
                        'urlDb' => $urlDbNew,
                    ),
                ),
            ),),
            'version' => '2.30.6',
        );
        $pageBodyNew = json_encode($pageBodyArrayNew, JSON_UNESCAPED_SLASHES);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/add-page', [
            'pageId' => $page->id,
            'title' => 'titleNew',
            'slug' => 'slugNew',
            'body' => $pageBodyNew,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('files', 6);
        $this->assertEquals(2, File::where('page_id', $page->id)->count());
        $this->assertDatabaseHas('files', [
            'url' => $urlDbOld,
            'page_id' => $page->id,
        ]);
        $this->assertDatabaseHas('files', [
            'url' => $urlDbNew,
            'page_id' => $page->id,
        ]);
        $this->assertDatabaseMissing('files', [
            'url' => $urlDbRemoved,
            'page_id' => $page->id,
        ]);
    }

    public function test_addPage(): void
    {
        $urlDb1 = 'pages/urlDb1.jpg';
        $urlDb2 = 'pages/urlDb2.jpg';
        File::factory()->count(3)->create();
        File::factory()->create([
            'url' => $urlDb1,
            'display_type' => 'image',
        ]);
        File::factory()->create([
            'url' => $urlDb2,
            'display_type' => 'image',
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
                        'url' => config('app.url') . '/storage/' . $urlDb1,
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
                        'url' => config('app.url') . '/storage/' . $urlDb2,
                        'urlDb' => $urlDb2,
                    ),
                ),
            ),),
            'version' => '2.30.6',
        );
        $pageBody = json_encode($pageBodyArray, JSON_UNESCAPED_SLASHES);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/add-page', [
            'title' => 'title',
            'slug' => 'slug',
            'body' => $pageBody,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('files', 5);
        $this->assertEquals(2, File::where('page_id', $response['pageId'])->count());
        $this->assertDatabaseHas('files', [
            'url' => $urlDb1,
            'page_id' => $response['pageId'],
        ]);
        $this->assertDatabaseHas('files', [
            'url' => $urlDb2,
            'page_id' => $response['pageId'],
        ]);
    }

    public function test_addPage_preview(): void
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
            ),),
            'version' => '2.30.6',
        );
        $pageBody = json_encode($pageBodyArray, JSON_UNESCAPED_SLASHES);

        $response = $this->actingAs(parent::getAdmin())->post('/admin-panel/add-page', [
            'title' => 'title',
            'preview' => 'true',
            'body' => $pageBody,
        ]);
        $previewPage = Page::whereSlug(config('my.preview_slug'))->first();

        $response
            ->assertStatus(200)
            ->assertJson([
                'previewUrl' => route('home', ['slug' => config('my.preview_slug')]),
            ]);
        $this->assertEquals($previewPage->body_prod, $pageBody);
        $this->assertEquals($previewPage->title, 'title');
    }

    public function test_deletePage(): void
    {
        $page = Page::factory()->create();
        File::factory()->count(3)->create();
        File::factory()->create([
            'page_id' => $page->id,
        ]);
        File::factory()->create([
            'page_id' => $page->id,
        ]);
        Attachment::factory()->count(3)->create();
        Attachment::factory()->create([
            'page_id' => $page->id,
        ]);
        Attachment::factory()->create([
            'page_id' => $page->id,
        ]);
        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/delete-page', [
            'pageId' => $page->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('files', 5);
        $this->assertDatabaseCount('attachments', 5);
        $this->assertEquals(5, File::where('page_id', null)->count());
        $this->assertEquals(5, Attachment::where('page_id', null)->count());
    }

    public function test_getPages(): void
    {
        $page = Page::factory()->count(2)->create();
        $pageNotActive = Page::factory()->create([
            'active' => false,
        ]);
        $pageActive = Page::factory()->create([
            'active' => true,
        ]);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/get-pages');

        $response->assertStatus(200);
        $this->assertEquals(5, count($response['pages']));
    }

    public function test_toggleActive(): void
    {
        
        Page::factory()->count(2)->create();
        $page = Page::factory()->create([
            'active' => false,
        ]);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/toggle-active', [
            'pageId' => $page->id,
            'active' => true,
        ]);
        $page = Page::whereId($page->id)->first();

        $response->assertStatus(200);
        $this->assertTrue(1 === $page->active);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/toggle-active', [
            'pageId' => $page->id,
            'active' => false,
        ]);
        $page = Page::whereId($page->id)->first();

        $this->assertTrue(0 === $page->active);
    }

    public function test_applyChanges(): void
    {
        
        Page::factory()->count(2)->create();
        $page = Page::factory()->create([
            'body' => 'body',
            'body_prod' => 'body_prod',
        ]);

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/apply-changes', [
            'pageId' => $page->id,
        ]);
        $page = Page::whereId($page->id)->first();

        $response->assertStatus(200);
        $this->assertTrue('body' === $page->body_prod);
    }
}
