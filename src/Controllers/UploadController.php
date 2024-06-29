<?php

namespace Heryfitiavana\RCU\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Heryfitiavana\RCU\Services\RCUService;

class UploadController extends Controller
{
    public function __construct(protected RCUService $rcuService)
    {
    }

    public function uploadStatus(Request $request)
    {
        $query = $request->validate([
            'fileId' => 'required|string|min:1',
            'chunkCount' => 'required|integer',
        ]);

        $status = $this->rcuService->uploadStatus($query);
        return Response::json($status);
    }

    public function upload(Request $request)
    {
        $dto = $request->validate([
            'fileId' => 'required|string|min:1',
            'chunkNumber' => 'required|integer',
            'originalFilename' => 'required|integer',
            'chunkCount' => 'required|integer',
            'chunkSize' => 'required|integer',
            'fileSize' => 'required|integer',
            'file' => 'required|file',
        ]);

        $result = $this->rcuService->upload($dto);

        return Response::json($result);
    }
}
