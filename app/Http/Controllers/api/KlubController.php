<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Klub;
use Illuminate\Http\Request;
use Validator;
use Storage;

class KlubController extends Controller
{
    public function index()
    {
        $klub = Klub::latest()->get();
        return response()->json ([
            'success' => true,
            'massage' => 'daftar klub sepak bola',
            'data' => $klub,
        ], 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nama_klub' => 'required|unique:klubs',
            'logo' => 'required|image|max:2048',
            'id_liga' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'data' => $validate->errors(),
            ], 422);
        }

        try {
            $path = $request->file('logo')->store('public/klub'); //menyimpan gambar
            $klub = new Klub;
            $klub->nama_klub = $request->nama_klub;
            $klub->logo = $path;
            $klub->id_liga = $request->id_liga;
            $klub->save();

            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Dibuat',
                'data' => $klub,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $klub = Klub::findOrFail($id);
             return response()->json([
                'success'=>true,
                'message'=>'detail klub',
                'data'=>$klub,
            ], 200);
        }  catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'data tidak ada',
                'errors'=>$e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'nama_klub' => 'required',
            'logo' => 'nullable|images|max:2048',
            'id_liga' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success'=>false,
                'message'=>'validasi gagal',
                'errors'=>$validate->errors(),
            ], 422);
        }

        try {
            $klub = Klub::findOrFail($id);
            if ($request->hasFile('logo')) {
                # delete logo/foto
                Storage::delete([$klub->logo]);
                $path = $request->file('logo')->store('public/logo');
                $klub->logo = $path;
            }
            $klub->nama_klub = $request->nama_klub;
            $klub->id_liga = $request->id_liga;
            $klub->save();
            return response()->json([
                'success' => true,
                'message' => 'Data klub berhasil diperbaharui',
                'data' => $klub,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $klub = Klub::findOrFail($id);
            Storage::delete($klub->logo);
            $klub->delete();
             return response()->json([
                'success'=>true,
                'message'=>'data '. $klub->nama_klub . ' berhasil terhapus',
            ], 200);
        }  catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'data tidak ada',
                'errors'=>$e->getMessage(),
            ], 404);
        }
    }
}
