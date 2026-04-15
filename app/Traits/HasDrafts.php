<?php

namespace App\Traits;

use App\Models\Draft\Draft;
use App\Models\Draft\DraftChange;
use Illuminate\Support\Facades\DB;

trait HasDrafts
{

    public function drafts()
    {
        return $this->morphMany(Draft::class, 'model');
    }

    public function draft()
    {
        return $this->morphOne(Draft::class, 'model')->where('status', 'pending');
    }

    public function saveAsDraft(array $attributes, array $options = []): Draft
    {

        $draft = Draft::firstOrCreate(
            [
                'model_type' => get_class($this),
                'model_id'   => $this->id,
                'status'     => 'pending',
            ],
            ['created_by'   => auth()->id()]
        );

        foreach ($attributes as $field => $value) {
            $hasOptionValue = array_key_exists($field, $options);
            $optionValue = $hasOptionValue ? $options[$field] : null;
            DraftChange::updateOrCreate(
                [
                    'draft_id' => $draft->id,
                    'field'    => $field,
                ],
                [
                    'old_value' =>  $hasOptionValue ? $optionValue : ($this->$field ?? null),
                    'new_value' => $value,
                ]
            );
        }
        return $draft;
    }

    public function setDraftStatus($status = 'approved')
    {
        $this->draft->update(['status' => $status]);
    }
}
