<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\ThumbnailTemplate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Cloudinary;

class TemplateController extends Controller
{   

    public function index()
    {
        try {
            $templates = Template::with('thumbnail')->get();

            $response = [
                'status' => 'success',
                'message' => 'Templates found!',
                'data' => [
                    'templates' => $templates,
                ],
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $template = Template::with('thumbnail')->findOrFail($id);
    
            $response = [
                'status' => 'success',
                'message' => 'Template found!',
                'data' => [
                    'template' => $template,
                ],
            ];
    
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    
    
    public function store(Request $request)
    {
        $uploadedFile = $request->file('image');
        $uploadedThumbnail = $request->file('thumbnail'); 

        try {
            $image = Cloudinary::upload($uploadedFile->getRealPath());
            $template = new Template;
            $template->urlImg = $image->getSecurePath();
            $template->publicId = $image->getPublicId();
            $template->name = $request->name;
            $template->status = true;
            $template->save();

            if ($uploadedThumbnail) {
                $thumbnailImage = Cloudinary::upload($uploadedThumbnail->getRealPath());
                $thumbnailTemplate = new ThumbnailTemplate;
                $thumbnailTemplate->urlImg = $thumbnailImage->getSecurePath();
                $thumbnailTemplate->publicId = $thumbnailImage->getPublicId();
                $thumbnailTemplate->template_id = $template->_id; 
                $thumbnailTemplate->save();
            }

            $template->thumbnail = $thumbnailTemplate ?? null;

            return response()->json([
                'status' => 'success',
                'message' => 'Data saved!',
                'data' => $template, 
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
