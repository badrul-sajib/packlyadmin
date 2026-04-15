<?php

namespace App\Jobs;

use App\Models\Product\Product;
use App\Services\OpenSearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IndexProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $productId;

    public function __construct( int $productId
    ) {
        $this->productId = $productId; 
    }

    public function handle(OpenSearchService $openSearchService): void
    {
        // Ensure index exists (idempotent)
        // $openSearchService->createIndex('products');
        
        $product = Product::with('category')->findOrFail($this->productId);

        $data = [
            'id' => (string) $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'category' => $product->category?->name ?? 'Uncategorized',
            'created_at' => $product->created_at->toIso8601String(),
        ];

        $openSearchService->indexDocument('products', $data['id'], $data);
    }
}
