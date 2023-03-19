<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Storage; // or use Illuminate\Support\Facades\Storage;
class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the latest 10 images from the database table
        $images = Image::orderBy('created_at', 'desc')->take(10)->get();

        // Return a success message with images data
        return response()->json([
            'success' => true,
            'message' => 'Images fetched successfully',
            'data' => $images,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the image
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'caption' => 'required|string|max:255',
        ]);

        // Get the image binary/blob
        $image = $request->file('image');

        // Generate a unique name for the image
        $imageName = time().'.'.$image->extension();

        // Upload the image to S3
        Storage::disk('s3')->put($imageName, file_get_contents($image));

        // Get the image url from S3
        $imageUrl = Storage::disk('s3')->url($imageName);

        // Get the caption from request
        $caption = $request->input('caption');

        // Get the current date and time
        $now = now();

        // Save image url, caption, and creation date to database
        Image::create([
            'url' => $imageUrl,
            'caption' => $caption,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Return a success message with image information
        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully.',
            'data' => [
                'url' => $imageUrl,
                'caption' => $caption,
                'created_at' => $now->toDateTimeString(),
            ]

        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        //
    }
}
