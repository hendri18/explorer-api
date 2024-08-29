<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Folder::with('subFolders', 'files')->get();
        return response()->json([
            "status" => "success",
            "data" => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors(),
            ], 400);
        }

        DB::beginTransaction();
        try {
            $folder = new Folder;
            $folder->name = $request->name;
            $folder->user_id = auth()->user()->id;
            if ($request->parent_id)
                $folder->parent_id = $request->parent_id;
            $folder->save();
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
            "data" => $folder,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Folder::with('subFolders')->where('id', $id)->first();
        return response()->json([
            "status" => "success",
            "data" => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validator->errors(),
            ], 400);
        }

        DB::beginTransaction();
        try {
            $folder = Folder::findOrFail($id);
            $folder->name = $request->name;
            $folder->save();
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
            "data" => $folder,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $folder = Folder::findOrFail($id)->delete();
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
}
