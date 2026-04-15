<?php

namespace App\Console\Commands;

use App\Jobs\IndexProduct;
use App\Models\Product\Product;
use Illuminate\Console\Command;

class ReindexProducts extends Command
{
    protected $signature = 'opensearch:reindex';
    protected $description = 'Reindex all products to OpenSearch';

    public function handle(): void
    {
        $this->info('Starting reindex...');
        
        Product::query()
            ->select('id') // only first 100
            ->chunkById(100, function ($products) {
                    foreach ($products as $p) {
                        IndexProduct::dispatch($p->id);
                        $this->info('Dipsatched 100.');
                    }
            });

        $this->info('Reindex complete.');
    }
}
