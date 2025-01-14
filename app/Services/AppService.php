<?php

namespace App\Services;

use App\Models\Footer;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AppService
{

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
}
