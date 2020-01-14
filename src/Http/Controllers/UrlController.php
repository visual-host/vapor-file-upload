<?php

namespace VisualHost\VaporFileUpload\Http\Controllers;

use App\Http\Controllers\Controller;

class UrlController extends Controller
{
    public function getBucketUrl()
    {
        $bucket = env('AWS_BUCKET');
        $region = env('AWS_DEFAULT_REGION');

        return response()->json([
            'url' => "https://$bucket.s3.$region.amazonaws.com"
        ]);
    }
}
