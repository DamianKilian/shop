<?php

namespace Tests\Feature\AdminPanel;

use App\Models\Footer;
use App\Services\AppService;
use App\Services\EditorJSService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class FooterTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer(): void
    {
        $response = $this->actingAs(parent::getAdmin())->get('/admin-panel/footer');

        $response->assertStatus(200);
    }

    public function test_getFooterHtml_returns_empty_str_when_no_html_in_db(): void
    {
        $editorjsTest = new EditorJSService();
        Footer::where('data_key', 'html')->delete();
        assertEquals('', AppService::getFooterHtml($editorjsTest));
    }

    public function test_getFooterHtml_empty_string(): void
    {
        $editorjsTest = new EditorJSService();
        $this->createDbFooterHtml(html: '');

        assertEquals('', AppService::getFooterHtml($editorjsTest));
    }

    public function test_getFooterHtml(): void
    {
        $editorjsTest = new EditorJSService();
        $this->createDbFooterHtml(html: '<p>some html</p>');

        assertEquals("<p>some html</p>\n", AppService::getFooterHtml($editorjsTest));
    }

    public function test_getFooter(): void
    {
        $footer = $this->createDbFooterHtml(html: '<p>some html</p>');

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/get-footer', []);

        $response
            ->assertStatus(200)
            ->assertJson([
                'footerHtml' => $footer->value,
            ]);
    }

    public function test_saveFooter(): void
    {
        Footer::where('data_key', 'html')->delete();
        $html = '<p>some html</p>';
        $footerHtml = '{"time":1734943438785,"blocks":[{"id":"Ez3nl30PG2","type":"raw","data":{"html":"' . $html . '"}}],"version":"2.30.6"}';

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/save-footer', [
            'dataKey' => 'html',
            'footerHtml' => $footerHtml
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('footer', [
            'data_key' => 'html',
            'value' => $footerHtml,
        ]);
    }

    public function test_saveFooter_edit(): void
    {
        $this->createDbFooterHtml(html: '<p>some html</p>');
        $html = '<p>some html2</p>';
        $newFooterHtml = '{"time":1734943438785,"blocks":[{"id":"Ez3nl30PG2","type":"raw","data":{"html":"' . $html . '"}}],"version":"2.30.6"}';

        $response = $this->actingAs(parent::getAdmin())->postJson('/admin-panel/save-footer', [
            'dataKey' => 'html',
            'footerHtml' => $newFooterHtml
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('footer', [
            'data_key' => 'html',
            'value' => $newFooterHtml,
        ]);
        $this->assertEquals(1, Footer::whereDataKey('html')->count());
    }

    protected function createDbFooterHtml($html)
    {
        return Footer::create([
            'data_key' => 'html',
            'value' => '{"time":1734943438785,"blocks":[{"id":"Ez3nl30PG2","type":"raw","data":{"html":"' . $html . '"}}],"version":"2.30.6"}'
        ]);
    }
}
