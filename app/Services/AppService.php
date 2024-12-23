<?php

namespace App\Services;

use App\Models\Footer;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AppService
{
    public static function getFooterHtml(EditorJSService $editorJS)
    {
        return cache()->remember('footerHtml', 60, function () use ($editorJS) {
            $html = Footer::whereDataKey('html')->first();
            if (!$html) {
                return '';
            }
            return $editorJS->toHtml($html->value);
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
        $usedModels = DB::table($table)
            ->whereIn('url', $models->pluck('url'))
            ->where(function (Builder $query) {
                $query->whereNot('page_id', null)
                    ->orWhereNot('product_id', null);
            })
            ->get();
        $usedModelUrls = $usedModels->pluck('url')->toArray();
        $publicStorage = Storage::disk('public');
        foreach ($models as $model) {
            if (false !== array_search($model->url, $usedModelUrls)) {
                continue;
            }
            $publicStorage->delete($model->url);
            if ('files' === $table) {
                $urlExplode = explode('/', $model->url);
                $publicStorage->delete(env('THUMBNAILS_FOLDER') . '/' . end($urlExplode));
            }
        }
        DB::table($table)->whereIn('id', $models->pluck('id'))->delete();
    }
}
