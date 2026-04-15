<?php

namespace App\Actions;

use App\Models\Review\Review;

class ReviewDetails
{
    public function handle($id)
    {
        return Review::find($id);
    }
}
