<?php

namespace Modules\Api\V1\Ecommerce\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCommentList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (! $this->reply) {
            return [
                'id'             => $this->id,
                'question'       => $this->comment,
                'question_date'  => $this->created_at->diffForHumans(),
                'user'           => (object) [
                    'id'   => $this->user->id,
                    'name' => $this->user->name,
                ],

            ];
        }

        return [
            'id'             => $this->id,
            'question'       => $this->comment,
            'question_date'  => $this->created_at->diffForHumans(),
            'answer'         => $this->reply,
            'answer_date'    => $this->reply ? $this->updated_at->diffForHumans() : null,
            'user'           => (object) [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'merchant'           => (object) [
                'id'   => $this->merchant->id,
                'name' => $this->merchant->name,
            ],
        ];
    }
}
