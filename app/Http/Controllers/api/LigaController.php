<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Liga;
use Illuminate\Http\Request;
use Validator;

class LigaController extends Controller
{
    public function index()
    {
        $liga = Liga::latest()->get();
        $res = [
            'success' => true,
            'massage' => 'daftar liga sepak bola',
            'data' => $liga,
        ];
        return response()->json($res, 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nama_liga' => 'required|unique:ligas',
            'negara' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success'=>false,
                'message'=>'validasi gagal',
                'errors'=>$validate->errors(),
            ], 422);
        }

        try {
            $liga = new Liga;
            $liga->nama_liga = $request->nama_liga;
            $liga->negara = $request->negara;
            $liga->save();
            return response()->json([
                'success'=>true,
                'message'=>'data berhasil dibuat',
                'data'=>$liga,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'terjadi kesalahan',
                'errors'=>$e->getMessage(),
            ], 500);
        }
    }

    public function show ($id)
    {
        try {
            $liga = Liga::findOrFail($id);
             return response()->json([
                'success'=>true,
                'message'=>'detail liga',
                'data'=>$liga,
            ]);
        }  catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'data tidak ada',
                'errors'=>$e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validate = validator::make($request->all(), [
            'nama_liga' => 'required',
            'negara' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validasi gagal',
                'errors' => $validate->errors(),
            ], 422);

        }
        try {
            $liga = Liga::findOrFail($id);
            $liga->nama_liga = $request->nama_liga;
            $liga->negara = $request->negara;
            $liga->save();

            return response()->json([
                'success' => true,
                'message' => 'Data liga berhasil diperbarui',
                'data' => $liga,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $liga = Liga::findOrFail($id);
            $liga->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data ' . $liga->nama_liga . ' berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'data tidak ada',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

}
