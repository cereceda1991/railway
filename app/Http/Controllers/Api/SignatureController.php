<?php

namespace App\Http\Controllers\Api;

use App\Models\Signature;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Cloudinary;

class SignatureController extends Controller
{
    public function index()
    {
        $perPage = 50; 
        $encryptedId = Auth::user()->getAuthIdentifier();
        $signatures = Signature::where('id_user', $encryptedId)
            ->paginate($perPage);
    
        $response = [
            'status' => 'success',
            'message' => 'Signatures found!',
            'data' => [
                'signatures' => $signatures->items(),
                'currentPage' => $signatures->currentPage(),
                'perPage' => $signatures->perPage(),
                'totalPages' => $signatures->lastPage(),
                'totalCount' => $signatures->total(),
            ],
        ];
    
        return response()->json($response, Response::HTTP_OK);
    }
    
    public function show($id)
    {
        try {
            $encryptedId = Auth::user()->getAuthIdentifier();
            $signature = Signature::where('id_user', $encryptedId)
                ->findOrFail($id);
    
            return response()->success($signature, 'Signature found!');
    
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Signature not found'], Response::HTTP_NOT_FOUND);
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
            $signature = Signature::create($signatureData);
    
            return response()->json(['message' => 'The Signature has been added successfully!', 'signature' => $signature], Response::HTTP_CREATED);
    
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
            $signature = Signature::where('id_user', $encryptedId)
                ->findOrFail($id);
    
            if ($request->hasFile('image')) {
                $uploadedFile = $request->file('image');
                $image = Cloudinary::upload($uploadedFile->getRealPath());
    
                if ($signature->publicId) {
                    Cloudinary::destroy($signature->publicId);
                }
    
                $signature->update([
                    'urlImg' => $image->getSecurePath(),
                    'publicId' => $image->getPublicId(),
                    'autorityName' => $request->autorityName,
                    'position' => $request->position,
                ]);
            } else {
                $signature->update([
                    'autorityName' => $request->autorityName,
                    'position' => $request->position,
                ]);
            }
    
            return response()->success($signature, 'Data updated!');
        } catch (\Throwable $th) {
            return response()->error($th->getMessage());
        } 
    }
       
    
}