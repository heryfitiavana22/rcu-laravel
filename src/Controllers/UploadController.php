<?php

namespace RCU\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use RCU\Services\RCUService;

class UploadController extends Controller
{
    public function __construct(protected RCUService $rcuService)
    {
    }

    public function uploadStatus(Request $request)
    {
        $query = [
            'fileId' => $request->fileId,
            'chunkCount' => $request->chunkCount,
        ];
        $status = $this->rcuService->uploadStatus($query);
        return Response::json($status);
    }

    public function upload(Request $request)
    {
        $dto = [
            'fileId' => $request->fileId,
            'chunkNumber' => $request->chunkNumber,
            'originalFilename' => $request->originalFilename,
            'chunkCount' => $request->chunkCount,
            'chunkSize' => $request->chunkSize,
            'fileSize' => $request->fileSize,
            'file' => $request->file('file')->get(),
        ];


        $result = $this->rcuService->upload($dto);

        return Response::json($result);
    }
}
