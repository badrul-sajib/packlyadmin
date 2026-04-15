<?php

namespace App\Media;

use Exception;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasMedia
{
    public string $disk_name = 'public';

    public function getDiskName(): string
    {
        return config('filesystems.default') ??  $this->disk_name;
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    /**
     * @throws Exception
     */
    public function addMedia($file, string $collectionName, array $attributes = []): void
    {
        // Check if the model instance is saved
        if (! $this->exists) {
            throw new Exception('Cannot attach media to an unsaved model instance.');
        }

        try {

            $extension = empty($file->getClientOriginalExtension()) ? $file->extension() : $file->getClientOriginalExtension();
            $filePath  = 'media/'.now()->format('Ymd_His').'_'.Str::uuid().'.'.$extension;
            $result    = Storage::disk($this->getDiskName())->put($filePath, file_get_contents($file), 'public');

            if (! $result) {
                throw new Exception('Media upload failed.');
            }

            // create the media item
            $this->media()->create(array_merge(
                $attributes,
                [
                    'collection_name' => $collectionName,
                    'model_id'        => $this->id,
                    'model_type'      => static::class,
                    'file_path'       => $filePath,
                    'file_type'       => $file->getClientMimeType(),
                ]
            ));

        } catch (Exception $e) {
            Log::error($e);

            throw new Exception('Media upload failed: '.$e->getMessage());
        }
    }

    public function getMedia(?string $collectionName = null): array
    {
        $query = collect($this->media);
        if ($collectionName) {
            $query->where('collection_name', $collectionName);
        }

        return $query->toArray();
    }

    public function getMediaIdAndUrl(?string $collectionName = null): array
    {
        $query      = collect($this->media);
        $media      = $query->filter(function ($media) use ($collectionName) {
            return $media->collection_name == $collectionName;
        })->map(function ($media) {
            return [
                'id'  => $media->id,
                'url' => $media->full_url,
            ];
        })->values()->toArray();

        return $media ?? [];
    }

    /**
     * @throws Exception
     */
    public function getUrl($collectionName = null, $baseUrl = null): array
    {
        try {

            $isMerchant = request()->is('api/v1/merchant/*');
            $query      = collect($this->media);
            $media      = $query->filter(function ($media) use ($collectionName) {
                return $media->collection_name == $collectionName;
            })->map(function ($media) use ($baseUrl, $isMerchant) {
                $url = '';
                if ($baseUrl && $this->getDiskName() == 'public') {
                    $url = $media ? $baseUrl.'/storage/'.$media->file_path : '';
                } else {
                    $url = Storage::disk($this->getDiskName())->url($media->file_path);
                }

                if ($isMerchant) {
                    return $url;
                }

                return [
                    'id'  => $media->id,
                    'url' => $url,
                ];
            })->values()->toArray();

            return $media ?? [];
        } catch (Exception $exception) {
            Log::error($exception);

            throw new Exception('Failed to retrieve media URL: '.$exception->getMessage());
        }
    }

    public function getFirstUrl($collectionName = null, $base_url = null): string
    {
        try {
            if ($this->media->isEmpty()) {
                return '';
            }

            $media = collect($this->media)->where('collection_name', $collectionName)->first();

            if (! $media) {
                return '';
            }

            if ($base_url  && $this->getDiskName() == 'public') {
                return $media ? $base_url.'/storage/'.$media->file_path : '';
            }

            // Generate and return the full URL for the media
            return Storage::disk($this->getDiskName())->url($media->file_path);
        } catch (Exception $exception) {
            Log::error($exception);

            return $exception->getMessage();
        }
    }

    public function deleteMedia($mediaId = null): bool
    {
        if ($mediaId) {
            $media = $this->media()->findOrFail($mediaId);
        } else {
            $media = $this->media()->first();
        }
        if ($media && $media->file_path) {
            Storage::disk($this->getDiskName())->delete($media->file_path);
            $media->delete();

            return true;
        }

        return false;
    }

    protected static function booted(): void
    {
        static::deleting(function ($model) {
            $model->media->each(function ($media) {
                $media->delete();
            });
        });
    }

    public function deleteMediaCollection(string $collectionName): void
    {
        $mediaItems = $this->media()->where('collection_name', $collectionName)->get();

        foreach ($mediaItems as $media) {
            if ($media->file_path) {
                Storage::disk($this->getDiskName())->delete($media->file_path);
            }

            $media->delete();
        }
    }
}
