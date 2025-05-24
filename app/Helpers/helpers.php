<?php
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

if (!function_exists('storeFileWithCustomName')) {
    function storeFileWithCustomName($file, $directory)
    {
       // Ensure the directory exists
       $path = storage_path("app/public/$directory");
       if (!is_dir($path)) {
           mkdir($path, 0755, true);
       }
       // Generate a custom filename: random 4 digits + timestamp + file extension
       $filename = rand(1000, 9999) . '_' . time() . '.' . $file->getClientOriginalExtension();
    //    dd($filename);
       // Store the file in the specified directory and return its path
       $file->storeAs($directory, $filename, 'public');
       return 'storage/' . $directory . '/' . $filename;
    }
}
if (!function_exists('storageFileUnlink')) {
    function storageFileUnlink($image_path)
    {
        // Remove 'storage/' prefix if present
        $path = str_replace('storage/', '', $image_path);

        // Delete the file from the public disk
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return true;
    }
}




