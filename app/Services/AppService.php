<?php

namespace App\Services;

use App\Models\Footer;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Mail\Logs;
use Illuminate\Support\Facades\Mail;

class AppService
{
    public static function toPennies(string $price)
    {
        return str_replace(".", "", $price);
    }

    public static function logsSend()
    {
        $email = config('my.log_send_emails');
        if (!$email) {
            return;
        }
        if (!filesize(storage_path('logs/laravel-error.log'))) {
            return;
        }
        Mail::to(explode(',', config('my.log_send_emails')))->send(new Logs());
    }

    protected static function footerToHtml($dataKey, $editorJS)
    {
        $html = Footer::whereDataKey($dataKey)->first();
        if (!$html) {
            return '';
        }
        return $editorJS->toHtml($html->value);
    }

    public static function getFooterHtml(EditorJSService $editorJS, $isPreview = false)
    {
        if ($isPreview) {
            return self::footerToHtml('html_preview', $editorJS);
        }
        return cache()->remember('footerHtml', 60, function () use ($editorJS) {
            return self::footerToHtml('html', $editorJS);
        });
    }

    public static function isSqlite($migrationClass): bool
    {
        return 'sqlite' === Schema::connection($migrationClass->getConnection())
            ->getConnection()
            ->getPdo()
            ->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }

    public static function pruneFiles($table)
    {
        $models = DB::table($table)
            ->where('page_id', null)
            ->where('product_id', null)
            ->where('created_at', '<=', now()->subMonth())
            ->get();
        self::deleteFiles($models, 'url', $table);
        if ('files' === $table) {
            self::deleteFiles($models, 'url_thumbnail', $table);
        }
        DB::table($table)->whereIn('id', $models->pluck('id'))->delete();
    }

    protected static function deleteFiles($models, $urlColumn, $table)
    {
        $modelsInUse = DB::table($table)
            ->whereIn($urlColumn, $models->pluck($urlColumn))
            ->where(function (Builder $query) {
                $query->whereNot('page_id', null)
                    ->orWhereNot('product_id', null);
            })
            ->get();
        $modelsInUseUrls = $modelsInUse->pluck($urlColumn)->toArray();
        $publicStorage = Storage::disk('public');
        foreach ($models as $model) {
            if (false !== array_search($model->{$urlColumn}, $modelsInUseUrls)) {
                continue;
            }
            $publicStorage->delete($model->{$urlColumn});
        }
    }

    /**
     * @param string $sett 'DESC_MAIN', 'DESC_CATEGORY', 'DESC_PRODUCT', 'TITLE_MAIN', 'TITLE_CATEGORY', 'TITLE_PRODUCT'
     *
     */
    public static function generateTitleAndDescription($sett, $category = null, $product = null)
    {
        $text = '';
        $settText = sett($sett);
        // passible vars {cat}, {parentCat}, {parentParentCat}, {shopName}, {product}, {price}
        $vars = [
            '{shopName}' => config('app.name'),
            '{cat}' => '',
            '{parentCat}' => '',
            '{parentParentCat}' => '',
            '{product}' => '',
            '{price}' => '',
        ];
        if ($category) {
            $vars['{cat}'] = $category->name;
            if ($category->parent_id) {
                $parentCat = $category->categories[$category->parent_id];
                $vars['{parentCat}'] = $parentCat->name;
                if (null === $product && $parentCat->parent_id) {
                    $parentParentCat = $category->categories[$parentCat->parent_id];
                    $vars['{parentParentCat}'] = $parentParentCat->name;
                }
            }
        }
        if ($product) {
            $vars['{product}'] = $product->title;
            $vars['{price}'] = price($product, false);
        }
        $keys = array_keys($vars);
        $values = array_values($vars);
        $text = str_replace($keys, $values, $settText);
        $text = str_replace(' ,', '', $text);
        return $text;
    }
}
