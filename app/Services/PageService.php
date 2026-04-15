<?php

namespace App\Services;

use App\Models\Page\Page;

class PageService
{
    public static function getPages(): array
    {
        return Page::orderByDesc('id')->get();
    }

    public static function findPageBySlug($slug): ?Page
    {
        return Page::where('slug', $slug)->firstOrFail();
    }
}
