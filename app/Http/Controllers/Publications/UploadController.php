<?php

namespace App\Http\Controllers\Publications;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function upload(): View
    {
        return view('dashboard.upload');
    }

    public function uploadPost(): RedirectResponse
    {
        request()->validate([
            'file' => 'required|max:2048',
        ]);

        $fileName = time().'.'.request()->file->getClientOriginalExtension();
        Storage::putFileAs(
            'public/files/uploads', request()->file, $fileName
        );

        return back()
            ->with('success', 'File uploaded to: <a href='.config('app.url').'/storage/files/uploads/'.$fileName.'>'.config('app.url').'/storage/files/uploads/'.$fileName.'</a>');
    }

    public function manageUploads(): View
    {
        $files = Storage::files('public/files/uploads');

        $files = array_map(function ($file) {
            return str_replace('public/files/uploads/', '', $file);
        }, $files);

        return view('dashboard.uploadmanage', ['files' => $files]);
    }

    public function deletePost($filename): RedirectResponse
    {
        $filePath = 'public/files/uploads/'.$filename;

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);

            return back()->with('success', 'File deleted successfully: '.$filename);
        } else {
            return back()->with('error', 'File not found: '.$filename);
        }
    }
}
