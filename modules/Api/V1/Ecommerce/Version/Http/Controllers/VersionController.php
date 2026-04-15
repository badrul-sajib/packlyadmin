<?php

namespace Modules\Api\V1\Ecommerce\Version\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function check(Request $request)
    {
        $type = $request->integer('type', 1);

        $versions = [
            1 => [
                "latestVersion" => 13,
                "minSupportedVersion" => 12,
                "forceUpdate" => true,
                "message" => "A new version is required to continue"
            ],
            2 => [
                "latestVersion" => 13,
                "minSupportedVersion" => 12,
                "forceUpdate" => true,
                "message" => "A new version is required to continue"
            ],
        ];
        return $versions[$type]
            ?? response()->json(["error" => "Invalid type"], 400);
    }
}
