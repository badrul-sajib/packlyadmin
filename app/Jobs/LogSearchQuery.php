<?php

namespace App\Jobs;

use App\Models\Search\Search;
use App\Models\User\SearchUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSearchQuery implements ShouldQueue
{
    use Queueable;

    public $tries = 5;

    public $timeout = 60;

    public $delay = 5;

    public $backoff = 2;

    public $search;

    public $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($search, $userId)
    {
        $this->search = $search;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $search = Search::updateOrCreate(
                ['search' => $this->search],
                ['count' => DB::raw('count + 1')]
            );

            SearchUser::create([
                'search_id' => $search->id,
                'user_id'   => $this->userId,
            ]);

        }catch (\Exception $e){
            report($e);
        }
    }
}
