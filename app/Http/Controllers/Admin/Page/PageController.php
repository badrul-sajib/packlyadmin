<?php

namespace App\Http\Controllers\Admin\Page;

use App\Http\Controllers\Controller;
use App\Models\Page\PageSectionDetail;
use App\Services\PageSectionDetailsService;
use App\Services\PageSectionService;
use App\Services\PageService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = PageService::getPages();

        return view('backend.pages.pages.index', compact('pages'));
    }

    public function showSections($slug)
    {
        $page = PageService::findPageBySlug($slug);

        $pageSections = PageSectionService::pageWiseSection($page->id);

        return view('backend.pages.pages.section.index', compact('pageSections'));
    }

    public function showSectionDetails($slug)
    {
        $pageSection = PageSectionService::findPageSectionBySlug($slug);

        $pageSectionDetails = PageSectionDetailsService::pageSectionWiseDetails($pageSection->id);

        return view('backend.pages.pages.section.details', compact('pageSectionDetails', 'pageSection'));
    }

    public function sectionDetailsUpdate(Request $request, $slug)
    {
        // Find the page section by slug
        $pageSection = PageSectionService::findPageSectionBySlug($slug);
        $data        = $request->except(['_method', '_token']); // Exclude method and token from request data

        // Check if there are services in the request data
        if (isset($data['services']) && count($data['services']) > 0) {
            foreach ($data['services'] as $key => $service) {
                // Check if the service has an image and if the file exists
                if (isset($service['image']) && file_exists($service['image'])) {
                    // Upload the new image
                    $data['services'][$key]['image'] = $this->uploadImage($service['image'], 'uploads/siteSettings/services');
                } else {
                    // If no new image is provided, use the existing image from the database
                    $existingServices                = json_decode(PageSectionDetail::where(['page_section_id' => $pageSection->id, 'name' => 'services'])->first()->value);
                    $data['services'][$key]['image'] = $existingServices[$key]->image;
                }
            }

            // Update the services with the new data
            PageSectionDetailsService::updateKeyValueJson($pageSection->id, $data['services']);
        } else {
            // If there are no services, update other data
            foreach ($data as $key => $value) {
                PageSectionDetailsService::updateKeyValue($pageSection->id, $key, $value);
            }
        }

        // Redirect back with a success message
        return back()->with('success', 'Page Section Updated Successfully');
    }

    public function uploadImage($file, $destinationPath)
    {
        $fileName = time().'_'.$file->getClientOriginalName(); // Create a unique filename
        $file->move(public_path($destinationPath), $fileName); // Move the file to the destination path

        return '/'.$destinationPath.'/'.$fileName; // Return the path of the uploaded file
    }
}
