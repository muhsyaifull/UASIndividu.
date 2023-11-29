<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tutorial;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TutorialController extends Controller
{
    public function index(){
        return view('page.admin.tutorial.indexTutorial');
    }

    public function addTutorialPage(){
        return view('page.admin.tutorial.addTutorial');
    }

    public function editTutorialPage(){
        return view('page.admin.tutorial.editTutorial');
    }

    public function showDetail($id){
        $data = Tutorial::findOrFail($id);
        $resource = [];
        $resource['id'] = $data->id;
        $resource['user_id'] = $data->user_id;
        $resource['judul_tutorial'] = $data->judul_tutorial;
        $resource['deskripsi'] = $data->deskripsi;
        $resource['bahan'] = $data->bahan;
        $resource['alat'] = $data->alat;
        $resource['langkah_tutorial'] = $data->langkah_tutorial;
        $resource['foto'] = $data->foto;


        return response()->json([
            'data' => $resource,
            'response' => 200
        ]);
    }

    public function getDataTutorial(){
        $dataTutorial = Tutorial::select(['id', 'user_id', 'judul_tutorial', 'deskripsi', 'bahan', 'alat', 'langkah_tutorial', 'foto']);
        return DataTables::of($dataTutorial)
        ->addColumn('user_name', function ($tutorial) {
            return $tutorial->user->name;
        })
        ->addColumn('action', function ($tutorial) {
            // Tambahkan tombol aksi sesuai kebutuhan Anda
            return '<a href="'.route('tutorial.editPage', 'id='.$tutorial->id).'" class="btn btn-info">Detail</a><a class="hapusData btn btn-danger" data-id="'.$tutorial->id.'" data-url="'.route('tutorial.delete',$tutorial->id).'">Hapus</a>';
        })
        ->make(true);
    }

    public function store(Request $request){
        try{
            $validatedData = $request->validate([
                'user_id' =>'required',
                'judul_tutorial' => 'required',
                'deskripsi'=> 'required',
                'bahan'=> 'required',
                'alat'=> 'required',
                'langkah_tutorial'=> 'required',
                'foto'=> 'image|mimes:jpg,png,jpeg,gif,svg, webp|max:1024',
            ]);
        }catch(ValidationException $e){
            $response = [];
            $response['message'] = 'Validasi gagal';
            $response['errors'] = $e->errors();
            dd($response);
        }
        $thumbnailName = NULL;
        if ($request->hasFile('foto')){
            $thumbnailName = Str::random(20) . '.webp';
            $webpImageData = Image::make($request->foto);
            $webpImageData->encode('webp');
            $webpImageData->resize(200, 250);
            Storage::put('public/assets/fotos/' . $thumbnailName, (string) $webpImageData);
        }
        $validatedData['foto'] = $thumbnailName;
        $tutorial = Tutorial::create($validatedData);
        return redirect()->route('tutorial.tambah')->with('status', 'data telah berhasil disimpan di database');
    }

    public function editTutorial($id, Request $request){
        // dd($request);
        //fungsi untuk edit
        $dataTutorial = Tutorial::findOrFail($id);
        $this->validate($request, [
            'user_id' =>'required',
            'judul_tutorial' => 'required',
            'deskripsi'=> 'required',
            'bahan'=> 'required',
            'alat'=> 'required',
            'langkah_tutorial'=> 'required',
            'foto'=> 'image|mimes:jpg,png,jpeg,gif,svg, webp|max:1024',
        ]);
        $thumbnailName = $dataTutorial->foto;
        if ($request->hasFile('foto')) {
            # delete old img
            if ($thumbnailName && file_exists(public_path().$thumbnailName)) {
                unlink(public_path().$thumbnailName);
            }
            $thumbnailName = Str::random(20) . '.webp';
            $webpImageData = Image::make($request->foto);
            $webpImageData->encode('webp');
            $webpImageData->resize(200, 250);
            Storage::put('public/assets/fotos/' . $thumbnailName, (string) $webpImageData);
        }
        $dataTutorial->update([
            'user_id' => $request->user_id,
            'judul_tutorial' => $request->judul_tutorial,
            'deskripsi'=> $request->deskripsi,
            'bahan'=> $request->bahan,
            'alat'=> $request->alat,
            'langkah_tutorial'=> $request->langkah_tutorial,
            'foto'=> $thumbnailName,
        ]);
        return redirect()->route('tutorial.editPage')->with('status', 'Data telah tersimpan di database');
    }

    public function deleteTutorial($id){
        //fungsi untuk delete
        try{
            $destroy = Tutorial::findOrFail($id);
            $destroy->delete();

            return response()->json([
                'message'   => 'Tutorial deleted successfully',
                'status'    => 200
            ]);;
        }catch(\Exception){
            return response()->json([
                'message'   => 'Not Found',
                'status'    => 404
            ]);
        }
    }
}
