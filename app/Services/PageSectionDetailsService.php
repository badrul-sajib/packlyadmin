<?php

namespace App\Services;

use App\Models\Page\PageSectionDetail;

class PageSectionDetailsService
{
    public static function pageSectionWiseDetails($pageSectionId)
    {
        return PageSectionDetail::where('page_section_id', $pageSectionId)->get();
    }

    public static function updateKeyValue($pageSectionId, $key, $value)
    {
        if (file_exists($value)) {
            $value = self::uploadImage($value, 'uploads/siteSettings');
        }

        return PageSectionDetail::where(['page_section_id' => $pageSectionId, 'name' => $key])->update(['value' => $value]);
    }

    public static function updateKeyValueJson($pageSectionId, $services)
    {
        return PageSectionDetail::where(['page_section_id' => $pageSectionId, 'name' => 'services'])->update(['value' => json_encode($services)]);
    }

    public static function uploadImage($file, $destinationPath)
    {
        $fileName = time().'_'.$file->getClientOriginalName();
        $file->move(public_path($destinationPath), $fileName);

        return '/'.$destinationPath.'/'.$fileName;
    }
}
