<?php

namespace App\Services;

use App\Enums\BaseStatus;
use App\Models\Promotion\PromotionAndService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PromotionServiceService
{
    public function getPromotionServicesMenu(): array
    {
        $result =  PromotionAndService::where('status', BaseStatus::ACTIVE->value)
            ->orderBy('id')
            ->get()
            ->groupBy('type')
            ->map(function ($promotionServices) {
                return $promotionServices->map(function ($promotionService) {
                    return [
                        'id'          => $promotionService->id,
                        'title'       => $promotionService->title,
                        'image'       => $promotionService->image,
                        'description' => $promotionService->description,
                        'type'        => $promotionService->type,
                    ];
                });
            });

        return [
            'items'  => $result,
        ];
    }

    public function getPromotionServices($request): LengthAwarePaginator|array
    {
        $search  = $request->search;
        $perPage = $request->input('perPage', 10);
        $page    = $request->input('page', 1);

        return PromotionAndService::query()
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->paginate($perPage, ['*'], 'page', $page)->withQueryString();
    }

    public function store(array $data): PromotionAndService
    {
        $promotion = PromotionAndService::create([
            'title'       => $data['title'],
            'description' => $data['description'],
            'status'      => $data['status'],
            'type'        => $data['type'],
        ]);
        $promotion->image = $data['image'];
        $promotion->save();

        return $promotion;
    }

    public function update(array $data, int $id)
    {
        $promotionService = $this->getById($id);

        return $promotionService->update($data);
    }

    public function getById(int $id): PromotionAndService
    {
        return PromotionAndService::find($id);
    }

    public function getBySlug(int $slug): PromotionAndService
    {
        return PromotionAndService::where('slug', $slug)->first();
    }

    public function delete(int $id)
    {
        $promotionService = $this->getById($id);
        $promotionService->delete();
    }
}
