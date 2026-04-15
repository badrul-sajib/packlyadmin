<?php

namespace Modules\Api\V1\Merchant\General\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SellWithUsController extends Controller
{
    /**
     * Get all SellWithUs page data
     */
    public function index(): JsonResponse
    {
        $sections = [
            'header'                     => $this->getSectionData('header'),
            'hero'                       => $this->getSectionData('hero'),
            'features'                   => $this->getSectionData('features'),
            'steps'                      => $this->getSectionData('steps'),
            'banner'                     => $this->getSectionData('banner'),
            'testimonials'               => $this->getSectionData('testimonials'),
            'frequently_asked_questions' => $this->getSectionData('frequently_asked_questions'),
        ];

        return response()->json([
            'success' => true,
            'data'    => $sections,
        ]);
    }

    /**
     * Helper method to structure section data
     */
    protected function getSectionData(string $sectionSlug): array
    {
        return [
            'title'    => getSellWithUsData($sectionSlug, 'title'),
            'subtitle' => getSellWithUsData($sectionSlug, 'subtitle'),
            'data'     => $this->formatData(getSellWithUsData($sectionSlug, 'data')),
            'items'    => getSellWithUsData($sectionSlug, 'items'),
        ];
    }

    /**
     * Format data array to key-value pairs
     */
    protected function formatData(?array $data): array
    {
        if (empty($data)) {
            return [];
        }

        $formatted = [];
        foreach ($data as $item) {
            if (empty($item['name'])) {
                continue;
            }
            $formatted[$item['name']] = $item['value'] ?? null;
        }

        return $formatted;
    }
}
