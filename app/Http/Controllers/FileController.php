<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Models\File as ModelFile;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'folder_id' => 'required|exists:App\Models\User,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors(),
            ], 400);
        }

        if (!$request->hasFile('file'))
            return response()->json([
                "status" => "error",
                "meessage" => "File is required"
            ], 400);

        DB::beginTransaction();
        try {
            $file = new ModelFile;

            $request_file = $request->file('file');
            $file_path = $request_file->storeAs('uploads', time().'_'.$request_file->getClientOriginalName(), 'local');
            
            $file->folder_id = $request->folder_id;
            $file->file_name = $request_file->getClientOriginalName();
            $file->path = $file_path;
            $file->size = $request_file->getSize();
            $file->save();

            $file->save();
            DB::commit();
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "meessage" => $e->getMessage()
            ], 500);
            DB::rollback();
        }
        
        return response()->json([
            "status" => "success",
            "data" => $file,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $file = ModelFile::findOrFail($id);
            $file_path = storage_path('app/'.$file->path);
            if (File::exists($file_path)) {
                File::delete($file_path);
            }
            $file->delete();
            DB::commit();
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "meessage" => $e->getMessage()
            ], 500);
            DB::rollback();
        }
        
        return response()->json([
            "status" => "success",
            "data" => null,
        ]);
    }

    // public function storage(string $id)
    // {

    //     $user = auth()->user();
    //     $file = ModelFile::findOrFail($id);

    //     $path = storage_path('uploads/' . $file->file_name);

    //     if (!File::exists($path)) {
    //         abort(404);
    //     }

    //     $file = File::get($path);
    //     $type = File::mimeType($path);
    
    //     $response = Response::make($file, 200);
    //     $response->header("Content-Type", $type);
    
    //     return $response;
    // }

    public function storage(string $id, string $user_id)
    {
        $file = ModelFile::findOrFail($id);
        $path = storage_path('app/'.$file->path);
        
        if ($file->folder->user->id != $user_id) {
            abort(404);
        }
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);
    
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
    
        return $response;
    }
}
