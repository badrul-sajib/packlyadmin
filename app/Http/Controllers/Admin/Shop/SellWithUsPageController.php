<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Sell\SellWithUsPage;
use App\Services\FileUploadService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SellWithUsPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sell-with-us-list')->only('index');
        $this->middleware('permission:sell-with-us-update')->only('update', 'destroy');
    }

    public function index(): View
    {
        $sections = SellWithUsPage::all();

        return view('backend.pages.sell_with_us.index', compact('sections'));
    }

    public function update(Request $request, SellWithUsPage $section): RedirectResponse
    {
        $request->validate([
            'title'           => 'required|string|max:60',
            'subtitle'        => 'nullable|string|max:150',
            'data'            => 'nullable|array',
            'data.*.name'     => 'required|string',
            'data.*.type'     => 'required|in:text,textarea,file,password',
            'data.*.value'    => 'nullable',
            'items'           => 'nullable|array',
            'items.*'         => 'array',
            'items.*.*.name'  => 'required|string',
            'items.*.*.type'  => 'required|in:text,textarea,file,password',
            'items.*.*.value' => 'nullable',
        ]);

        // Get current data and items from the section
        $data         = $request->input('data', []);
        $existingData = $section->data ?? [];

        // Handle file uploads for data
        foreach ($data as $index => $item) {
            if ($item['type'] === 'file') {

                $oldFile = $existingData[$index]['value'] ?? null;
                // Handle file upload
                $storedPath = FileUploadService::handleUpload(
                    request: $request,
                    fieldName: "data.$index.value",
                    oldFilePath: $oldFile,
                    folder: '/uploads'
                );

                $data[$index]['value'] = $storedPath;
            }
        }

        // Handle file uploads for items
        $items         = $request->input('items', []);
        $existingItems = $section->items ?? [];

        foreach ($items as $itemIndex => $itemGroup) {
            foreach ($itemGroup as $subItemIndex => $subItem) {
                if ($subItem['type'] === 'file') {

                    $oldFile =  $existingItems[$itemIndex][$subItemIndex]['value'] ?? null;
                    // Handle file upload
                    $storedPath = FileUploadService::handleUpload(
                        request: $request,
                        fieldName: "items.$itemIndex.$subItemIndex.value",
                        oldFilePath: $oldFile,
                        folder: 'uploads'
                    );

                    $items[$itemIndex][$subItemIndex]['value'] = $storedPath;

                }
            }
        }

        $section->update([
            'data'     => $data,
            'title'    => $request->input('title', $section->title),
            'subtitle' => $request->input('subtitle', $section->subtitle),
            'items'    => $items,
        ]);

        return redirect()->route('admin.sell-with-us.index')->with('success', 'Data and Items updated successfully');
    }

    public function destroy(SellWithUsPage $section, $itemIndex): RedirectResponse
    {
        $items = $section->items;

        if (! is_numeric($itemIndex) || ! isset($items[$itemIndex])) {
            return redirect()->route('admin.sell-with-us.index')->with('error', 'Invalid item index');
        }

        unset($items[$itemIndex]);
        $section->update(['items' => array_values($items)]);

        return redirect()->route('admin.sell-with-us.index')->with('success', 'Item group deleted successfully');
    }
}
