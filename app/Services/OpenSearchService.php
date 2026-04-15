<?php

namespace App\Services;

class OpenSearchService
{
    public function createIndex(string $indexName): void
    {
        // Static mode: OpenSearch indexing is disabled
    }

    public function indexDocument(string $index, string $id, array $body): void
    {
        // Static mode: OpenSearch indexing is disabled
    }

    public function search(string $index, string $query, array $filters = []): array
    {
        // Static mode: returns empty result set
        return [
            'hits' => [
                'total' => ['value' => 0],
                'hits'  => [],
            ],
        ];
    }

    public function getDocument(string $index, string $id): ?array
    {
        // Static mode: no documents
        return null;
    }
}
