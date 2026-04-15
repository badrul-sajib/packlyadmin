<?php

namespace App\Services;

use App\Enums\BaseStatus;
use App\Enums\EPageLabel;
use App\Models\Page\EPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EPageService
{
    public function getEPagesMenu(): array
    {
        $result =  EPage::where('status', BaseStatus::ACTIVE->value)
            ->orderBy('serial_no')
            ->get()
            ->groupBy('label')
            ->map(function ($e_pages) {
                return $e_pages->map(function ($e_page) {
                    return [
                        'id'    => $e_page->id,
                        'slug'  => $e_page->slug,
                        'title' => $e_page->title,
                    ];
                });
            });

        return [
            'pages'  => $result,
            'labels' => EPageLabel::all(),
        ];
    }

    public function getEPages($request): LengthAwarePaginator|array
    {
        $search  = $request->search;
        $perPage = $request->input('perPage', 10);
        $page    = $request->input('page', 1);

        return EPage::query()
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->paginate($perPage, ['*'], 'page', $page)->withQueryString();
    }

    public function store(array $data): EPage
    {
        return EPage::create($data);
    }

    public function update(array $data, $id)
    {
        $e_page = $this->getById($id);

        return $e_page->update($data);
    }

    public function getById(int $id): ?EPage
    {
        return EPage::find($id);
    }

    public function getBySlug(string $slug): ?EPage
    {
        return EPage::where('slug', $slug)->first();
    }

    public function delete(int $id): void
    {
        $e_page = $this->getById($id);
        $e_page->delete();
    }
}
