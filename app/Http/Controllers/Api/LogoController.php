<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Logo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\HttpFoundation\Response;
use Cloudinary;

class LogoController extends Controller
{
    public function index()
    {
        $encryptedId = Auth::user()->getAuthIdentifier();
    
        $logos = Logo::where('id_user', $encryptedId)->get();    
    
    return response()->success($logos, 'Logos found!');
    }

    public function show($id)
    {
        try {
            $logo = Logo::findOrFail($id);
            return response()->success($logo , 'Logo found!');

        } catch (\Throwable $th) {
            return response()->json(['error' => 'Logo not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image',
        ]);
    
        if ($validator->fails()) {
            return response()->json([$validator->errors()], Response::HTTP_BAD_REQUEST);
        }
    
        $encryptedId = Auth::user()->getAuthIdentifier();
        $uploadedFile = $request->file('image');
        try {
            $image = Cloudinary::upload($uploadedFile->getRealPath());
            $logo = new Logo;
            $logo->urlImg = $image->getSecurePath();
            $logo->publicId = $image->getPublicId();
            $logo->status = true;
            $logo->id_user = $encryptedId;
            $logo->save();
    
            return response()->success([$logo], 'The logo has been added successfully!');
    
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    

    public function update(Request $request,$id)
    {
        $uploadedFile = $request->file('image');
         try { 
             $logo = logo::findOrFail($id); 
             if($uploadedFile){
                $destroy = Cloudinary::destroy($logo->publicId);
                $image = Cloudinary::upload($uploadedFile->getRealPath());
                $logo->urlImg = $image->getSecurePath();
                $logo->publicId = $image->getPublicId();      
            }            
            $logo->fill($request->only(['status']));
            $logo->save();
            return response()->success($logo, 'Data updated!');
         } catch (\Throwable $th) {
            return response()->error($th->getMessage());
        } 
    }      
    
}
