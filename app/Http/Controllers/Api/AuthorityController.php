<?php

namespace App\Http\Controllers\Api;

use App\Models\Authority;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Cloudinary;

class AuthorityController extends Controller
{
    public function index()
    {
        $encryptedId = Auth::user()->getAuthIdentifier();
        $authorities = Authority::where('id_user', $encryptedId)->get();
    
        return response()->success($authorities, 'Authorities found!');
    }
    
    public function show($id)
    {
        try {
            $encryptedId = Auth::user()->getAuthIdentifier();
            $authority = Authority::where('id_user', $encryptedId)
                ->findOrFail($id);
    
            return response()->success($authority, 'Authority found!');
    
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Authority not found'], Response::HTTP_NOT_FOUND);
        }
    }
    

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'image',
            'authorityName' => 'required|string|min:1', 
            'position' => 'required|string|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }
    
        $uploadedFile = $request->file('image');
        $imageData = [];
    
        if ($uploadedFile) {
            try {
                $image = Cloudinary::upload($uploadedFile->getRealPath());
                $imageData = [
                    'urlImg' => $image->getSecurePath(),
                    'publicId' => $image->getPublicId(),
                ];
            } catch (\Throwable $th) {
                return response()->json(['error' => 'Error uploading image'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $imageData = [
                'urlImg' => null,
                'publicId' => null, 
            ];
        }
    
        $encryptedId = Auth::user()->getAuthIdentifier();
        $signatureData = [
            'authorityName' => $request->authorityName,
            'position' => $request->position,
            'status' => true,
            'id_user' => $encryptedId,
        ];
    
        $signatureData = array_merge($signatureData, $imageData);
    
        try {
            $authority = Authority::create($signatureData);

            return response()->success($authority, 'The Authority has been added successfully!');
    
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'image',
            'authorityName' => 'required|string|min:1', 
            'position' => 'required|string|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }
    
        try {
            $encryptedId = Auth::user()->getAuthIdentifier();
            $authority = Authority::where('id_user', $encryptedId)
                ->findOrFail($id);
    
            if ($request->hasFile('image')) {
                $uploadedFile = $request->file('image');
                $image = Cloudinary::upload($uploadedFile->getRealPath());
    
                if ($authority->publicId) {
                    Cloudinary::destroy($authority->publicId);
                }
    
                $authority->update([
                    'urlImg' => $image->getSecurePath(),
                    'publicId' => $image->getPublicId(),
                    'authorityName' => $request->authorityName,
                    'position' => $request->position,
                ]);
            } else {
                $authority->update([
                    'authorityName' => $request->authorityName,
                    'position' => $request->position,
                ]);
            }
    
            return response()->success($authority, 'Data updated!');
        } catch (\Throwable $th) {
            return response()->error($th->getMessage());
        } 
    }
       
    
}