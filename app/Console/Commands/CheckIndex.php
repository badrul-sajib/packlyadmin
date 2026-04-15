<?php

namespace App\Console\Commands;

use App\Services\OpenSearchService;
use Illuminate\Console\Command;

class CheckIndex extends Command
{
    protected $signature = 'opensearch:stats {index=products} {--id=}';

    protected $description = 'Check OpenSearch index stats and document existence';

    public function handle(OpenSearchService $service): void
    {
        $index = $this->argument('index');
        $id = $this->option('id');

        $this->info("Checking index: {$index}...");

        try {
            $client = $service->getClient();

            // 1. Check if index exists
            if (! $client->indices()->exists(['index' => $index])) {
                $this->error("Index [{$index}] does not exist!");

                return;
            }
            $this->info('✔ Index exists.');

            // 2. Get Count
            $count = $client->count(['index' => $index]);
            $this->info('✔ Document Count: '.$count['count']);

            // 3. Get Mapping
            $mapping = $client->indices()->getMapping(['index' => $index]);
            $this->info('✔ Mappings retrieved.');

            // 4. Fetch specific document if ID provided
            if ($id) {
                $this->info("Fetching document ID: {$id}...");
                try {
                    $doc = $client->get(['index' => $index, 'id' => $id]);
                    $this->table(['Field', 'Value'], array_map(function ($k, $v) {
                        return [$k, is_array($v) ? json_encode($v) : $v];
                    }, array_keys($doc['_source']), $doc['_source']));
                } catch (\Exception $e) {
                    $this->error('Document not found: '.$e->getMessage());
                }
            } else {
                // Search sample
                $this->info('Fetching sample document...');
                $sample = $client->search([
                    'index' => $index,
                    'size' => 1,
                    'body' => ['query' => ['match_all' => new \stdClass]],
                ]);

                if (! empty($sample['hits']['hits'])) {
                    $source = $sample['hits']['hits'][0]['_source'];
                    $this->table(['Field', 'Value'], array_map(function ($k, $v) {
                        return [$k, is_array($v) ? json_encode($v) : $v];
                    }, array_keys($source), $source));
                } else {
                    $this->warn('Index is empty.');
                }
            }

        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
        }
    }
}
