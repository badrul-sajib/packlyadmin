<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiDebugbar
{
    private array $queryLog = [];

    private $startTime;

    private $startMemory;

    public function handle(Request $request, Closure $next)
    {

        if (! config('app.debug')) {
            return $next($request);
        }

        $this->startDebugging();

        $response = $next($request);

        return $this->addDebugInfo($response);
    }

    private function startDebugging(): void
    {
        $this->startTime   = microtime(true);
        $this->startMemory = memory_get_usage(true);

        DB::enableQueryLog();

        // Listen to all database queries
        DB::listen(function ($query) {
            $this->logQuery($query);
        });
    }

    private function logQuery($query): void
    {
        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        // Find the first occurrence of application code
        $trace = collect($stack)->first(function ($trace) {
            return isset($trace['file'])                     &&
                   str_contains($trace['file'], base_path()) &&
                   ! str_contains($trace['file'], 'vendor');
        });

        $this->queryLog[] = [
            'sql'      => $query->sql,
            'bindings' => $query->bindings,
            'time'     => $query->time,
            'source'   => [
                'file'   => $trace['file']       ?? 'unknown',
                'line'   => $trace['line']       ?? 0,
                'method' => $trace['function']   ?? 'unknown',
            ],
        ];
    }

    private function addDebugInfo($response): mixed
    {
        if (! $response instanceof \Illuminate\Http\JsonResponse) {
            return $response;
        }

        $debugData = $this->collectDebugData();
        $content   = json_decode($response->getContent(), true);

        $content['_debug'] = $debugData;

        $response->setContent(json_encode($content, JSON_PRETTY_PRINT));

        return $response;
    }

    private function collectDebugData(): array
    {
        $queryAnalysis = $this->analyzeQueries();
        $timeSpent     = (microtime(true) - $this->startTime) * 1000;
        $memoryUsed    = memory_get_usage(true) - $this->startMemory;

        return [
            'summary' => [
                'total_queries' => count($this->queryLog),
                'total_time'    => round($timeSpent, 2).'ms',
                'memory_usage'  => $this->formatBytes($memoryUsed),
                'peak_memory'   => $this->formatBytes(memory_get_peak_usage(true)),
            ],
            'query_analysis' => $queryAnalysis,
            'queries'        => $this->formatQueries(),
        ];
    }

    private function analyzeQueries(): array
    {
        $analysis = [
            'types' => [
                'select' => 0,
                'insert' => 0,
                'update' => 0,
                'delete' => 0,
                'other'  => 0,
            ],
            'tables'            => [],
            'slowest_query'     => null,
            'average_time'      => 0,
            'duplicate_queries' => [],
        ];

        $totalTime   = 0;
        $queryHashes = [];

        foreach ($this->queryLog as $query) {
            // Analyze query type
            $type = $this->getQueryType($query['sql']);
            $analysis['types'][$type]++;

            // Track tables
            $tables = $this->extractTables($query['sql']);
            foreach ($tables as $table) {
                if (! isset($analysis['tables'][$table])) {
                    $analysis['tables'][$table] = 0;
                }
                $analysis['tables'][$table]++;
            }

            // Track timing
            $totalTime += $query['time'];
            if (! $analysis['slowest_query'] || $query['time'] > $analysis['slowest_query']['time']) {
                $analysis['slowest_query'] = [
                    'sql'    => $query['sql'],
                    'time'   => $query['time'],
                    'source' => $query['source'],
                ];
            }

            // Check for duplicates
            $queryHash = md5($query['sql']);
            if (! isset($queryHashes[$queryHash])) {
                $queryHashes[$queryHash] = [
                    'count'     => 1,
                    'sql'       => $query['sql'],
                    'locations' => [$query['source']],
                ];
            } else {
                $queryHashes[$queryHash]['count']++;
                $queryHashes[$queryHash]['locations'][] = $query['source'];
            }
        }

        // Calculate average time
        $analysis['average_time'] = count($this->queryLog) > 0
            ? round($totalTime / count($this->queryLog), 2)
            : 0;

        // Find duplicates
        foreach ($queryHashes as $hash => $data) {
            if ($data['count'] > 1) {
                $analysis['duplicate_queries'][] = [
                    'sql'       => $data['sql'],
                    'count'     => $data['count'],
                    'locations' => array_slice($data['locations'], 0, 3), // Limit to first 3 locations
                ];
            }
        }

        return $analysis;
    }

    private function formatQueries(): array
    {
        return array_map(function ($query) {
            return [
                'sql'      => $this->highlightQuery($query['sql']),
                'bindings' => $query['bindings'],
                'time'     => round($query['time'], 2).'ms',
                'source'   => [
                    'file'   => str_replace(base_path(), '', $query['source']['file']),
                    'line'   => $query['source']['line'],
                    'method' => $query['source']['method'],
                ],
            ];
        }, $this->queryLog);
    }

    private function getQueryType(string $sql): string
    {
        $sql = strtolower(trim($sql));
        if (str_starts_with($sql, 'select')) {
            return 'select';
        }
        if (str_starts_with($sql, 'insert')) {
            return 'insert';
        }
        if (str_starts_with($sql, 'update')) {
            return 'update';
        }
        if (str_starts_with($sql, 'delete')) {
            return 'delete';
        }

        return 'other';
    }

    private function extractTables(string $sql): array
    {
        preg_match_all('/\b(?:from|join|update|into)\s+`?(\w+)`?/i', $sql, $matches);

        return array_unique($matches[1]);
    }

    private function highlightQuery(string $sql): string
    {
        $keywords = ['SELECT', 'FROM', 'WHERE', 'JOIN', 'LEFT', 'RIGHT', 'INNER', 'OUTER',
            'GROUP BY', 'ORDER BY', 'LIMIT', 'OFFSET', 'INSERT', 'UPDATE', 'DELETE'];

        $sql = htmlspecialchars($sql);
        foreach ($keywords as $keyword) {
            $sql = str_ireplace(
                $keyword,
                $keyword,
                $sql
            );
        }

        return $sql;
    }

    private function formatBytes($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];
    }
}
