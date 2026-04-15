<?php

namespace App\Services;

use App\Models\Page\PageSection;

class PageSectionService
{
    public static function pageWiseSection($pageId)
    {
        return PageSection::where('page_id', $pageId)->get();
    }

    public static function findPageSectionBySlug($slug): ?PageSection
    {
        return PageSection::where('slug', $slug)->firstOrFail();
    }
}
