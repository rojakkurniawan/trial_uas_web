<?php

namespace App\Http\Controllers;

use App\Models\ImageCrud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Resources\ImageResource;

class ImageCrudController extends Controller
{
    public function create(Request $request)
{
    $request->validate([
        'category' => 'required',
        'images.*' => 'required|image|mimes:jpeg,png,jpg,gif' 
    ]);

    $results = [];
    foreach ($request->file('images') as $file) {
        $filename = $file->store('posts', 'public');
        $filename = str_replace('posts/', '', $filename);

        $images = new ImageCrud();
        $images->category = $request->category;
        $images->image = $filename;
        $result = $images->save();
        $results[] = $result;
    }

    if (in_array(false, $results, true)) {
        return response()->json(['success' => false,
            'message' => 'Images not uploaded failed'
    ]);
    }

    return response()->json(['success' => true,
        'message' => 'Images uploaded successfully'

]);
}



    public function get()
    {
        $images=ImageCrud::orderBy('id','DESC')->get();
        return ImageResource::collection($images);
    }

    public function edit($id)
    {
        $images=ImageCrud::findOrFail($id);
        return response()->json($images);
    }

    public function update(Request $request, $id)
{
    $images = ImageCrud::findOrFail($id);
    
    $oldFilename = $images->image; // Simpan nama file lama
    $destination = storage_path('app/public/posts/' . $oldFilename);

    $filename = $oldFilename; // Gunakan $oldFilename sebagai default

    if ($request->hasFile('new_image')) {
        // Jika ada unggahan file baru, hapus file lama
        if (File::exists($destination)) {
            File::delete($destination);
        }

        // Simpan file yang baru diunggah
        $filename = $request->file('new_image')->store('posts', 'public');
        $filename = str_replace('posts/', '', $filename);
    }

    // Perbarui nilai category dan image
    $images->category = $request->category;
    $images->image = $filename;

    // Simpan perubahan ke dalam database
    $result = $images->save();

    if ($result) {
        return response()->json([
            'success' => true,
            'message' => 'Image updated successfully'
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Image not updated'
        ]);
    }
}



    public function delete($id)
    {
        $image = ImageCrud::findOrFail($id);

        $filename = $image->image;
        $result = $image->delete();

        if ($result) {
            $destination = storage_path('app/public/posts/' . $filename);

            if (File::exists($destination)) {
                File::delete($destination); 
            }

            return response()->json(['success' => true,
                'message' => 'Image deleted successfully']);
        } else {
            return response()->json(['success' => false,
                'message' => 'Image not deleted'
        ]);
        }
    }


    public function getImage($filename)
    {
        $path = storage_path('app/public/posts/' . $filename);
    
        if (!file_exists($path)) {
            abort(404);
        }
    
        return response()->file($path);
    }
    
    
}