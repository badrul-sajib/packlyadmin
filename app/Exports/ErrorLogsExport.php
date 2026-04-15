<?php

namespace App\Exports;

use App\Models\ErrorLog\ErrorLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ErrorLogsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $statusCode  = $this->request->status_code ?? '';
        $search      = $this->request->message ?? '';
        $source      = $this->request->source ?? '';
        $environment = $this->request->environment ?? '';

        return ErrorLog::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('message', 'like', "%{$search}%")
                        ->orWhere('endpoint', 'like', "%{$search}%")
                        ->orWhere('current_route', 'like', "%{$search}%")
                        ->orWhere('user_agent', 'like', "%{$search}%");
                });
            })
            ->when($statusCode, function ($query) use ($statusCode) {
                $query->where('status_code', $statusCode);
            })
            ->when($source, function ($query) use ($source) {
                $query->where('source', $source);
            })
            ->when($environment, function ($query) use ($environment) {
                $query->where('environment', $environment);
            })
            ->select('id', 'source', 'client_type', 'status_code', 'endpoint', 'message', 'occurrence_count', 'last_occurred_at')
            ->latest();
    }

    public function headings(): array
    {
        return [
            'Id',
            'Source',
            'Client Type',
            'Status',
            'Endpoint',
            'Message',
            'Count',
            'Last Occurred',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->source,
            $row->client_type,
            $row->status_code,
            $row->endpoint,
            $row->message,
            $row->occurrence_count,
            $row->last_occurred_at,
        ];
    }
}
