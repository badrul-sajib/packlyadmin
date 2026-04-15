<?php

namespace App\Services\Merchant;

use App\Enums\MerchantVerificationStatus;
use App\Media\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MerchantVerificationService
{
    public function submit(array $data)
    {
        $merchant            = Auth::user()->merchant;
        $data['is_verified'] = MerchantVerificationStatus::PROCESSING;
        $merchant->update($data);

        if (isset($data['delete_trade_license_ids'])) {
            $medias = Media::whereIn('id', $data['delete_trade_license_ids'])->get();
            foreach ($medias as $media) {
                Storage::disk(Media::$disk_name)->delete($media->file_path);
                $media->delete();
            }
        }

        if (isset($data['delete_bank_statement_ids'])) {
            $medias = Media::whereIn('id', $data['delete_bank_statement_ids'])->get();
            foreach ($medias as $media) {
                Storage::disk(Media::$disk_name)->delete($media->file_path);
                $media->delete();
            }
        }

    }
}
