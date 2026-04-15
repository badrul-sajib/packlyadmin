<?php

namespace App\Services;

use App\Models\Order\Reason;

class ReasonService
{
    public function createReason($data)
    {
        return Reason::create($data);
    }

    public function updateReason($id, $data)
    {
        return Reason::find($id)->update($data);
    }

    public function deleteReason($id)
    {
        return Reason::find($id)->delete();
    }

    public function getReason($id)
    {
        return Reason::find($id);
    }
}
