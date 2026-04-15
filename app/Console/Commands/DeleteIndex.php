<?php

namespace App\Console\Commands;

use App\Services\OpenSearchService;
use Illuminate\Console\Command;

class DeleteIndex extends Command
{
    protected $signature = 'opensearch:delete {index=products}';
    protected $description = 'Delete an OpenSearch index';

    public function handle(OpenSearchService $service): void
    {
        $index = $this->argument('index');
        $this->info("Deleting index: {$index}...");

        try {
            $client = $service->getClient();
            if ($client->indices()->exists(['index' => $index])) {
                $client->indices()->delete(['index' => 'products']);
                $this->info("Index deleted successfully.");
            } else {
                $this->warn("Index does not exist.");
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
