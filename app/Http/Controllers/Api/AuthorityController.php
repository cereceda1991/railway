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
        $perPage = 50; 
        $encryptedId = Auth::user()->getAuthIdentifier();
        $authorities = Authority::where('id_user', $encryptedId)
            ->paginate($perPage);
    
        $response = [
            'status' => 'success',
            'message' => 'Authorities found!',
            'data' => [
                'authorities' => $authorities->items(),
                'currentPage' => $authorities->currentPage(),
                'perPage' => $authorities->perPage(),
                'totalPages' => $authorities->lastPage(),
                'totalCount' => $authorities->total(),
            ],
        ];
    
        return response()->json($response, Response::HTTP_OK);
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
            'autorityName' => 'required|string|min:1', 
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
            'autorityName' => $request->autorityName,
            'position' => $request->position,
            'id_user' => $encryptedId,
        ];
    
        $signatureData = array_merge($signatureData, $imageData);
    
        try {
            $authority = Authority::create($signatureData);
    
            return response()->json(['message' => 'The Authority has been added successfully!', 'authority' => $authority], Response::HTTP_CREATED);
    
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'image',
            'autorityName' => 'required|string|min:1', 
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
                    'autorityName' => $request->autorityName,
                    'position' => $request->position,
                ]);
            } else {
                $authority->update([
                    'autorityName' => $request->autorityName,
                    'position' => $request->position,
                ]);
            }
    
            return response()->success($authority, 'Data updated!');
        } catch (\Throwable $th) {
            return response()->error($th->getMessage());
        } 
    }
       
    
}