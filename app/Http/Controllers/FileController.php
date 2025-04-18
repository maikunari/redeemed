<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FileController extends Controller
{
    public function index()
    {
        return Inertia::render('Files/Index', [
            'files' => File::with('media')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,zip,mp3,jpg,png|max:20480',
        ]);

        $file = File::create(['title' => $request->title]);
        $file->addMedia($request->file('file'))
             ->toMediaCollection('files');

        return redirect()->route('files.index')->with('success', 'File uploaded.');
    }

    public function destroy(File $file)
    {
        $file->delete();
        return redirect()->route('files.index')->with('success', 'File deleted.');
    }
}
