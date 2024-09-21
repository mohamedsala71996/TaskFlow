<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardDetail;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CardAttachmentController extends Controller
{

    protected function logAction($card_id, $action)
    {
        CardDetail::create([
            'user_id' => Auth::id(),
            'desc' => $action,
            'card_id' => $card_id,
        ]);
    }

    // card side edits
    public function updatePhoto(Request $request, $card_id)
    {
        $card = Card::find($card_id);
        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => "Card not found",
            ], 404);
        }
        // Validate the request
        $request->validate([
            'photo' => 'nullable', // Validation rule for the photo
        ]);

        // If there's an existing photo, delete it
        if ($request->photo) {
            if ($card->photo) {
                Storage::disk('public')->delete($card->photo);
            }
            // Store the new photo
            $photoPath = $request->file('photo')->store('card-cover', 'public');
            // Update the card's photo path
            $card->photo = $photoPath ?? null;
        }
        // $card->color = $request->color ?? null;
        $card->save();

        $this->logAction($card_id, ' updated the attachment photo of this card.');

        return response()->json([
            'success' => true,
            'message' => "data updated successfully",
            'photo_url' =>  $card->photo ? Storage::url($card->photo) : null,
            // 'color' => $card->color,
        ], 200);
    }
    public function deletePhoto($card_id)
    {
        $card = Card::find($card_id);

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => "Card not found",
            ], 404);
        }

        if (!$card->photo) {
            return response()->json([
                'success' => false,
                'message' => "No photo to delete",
            ], 404);
        }

        // Delete the photo
        Storage::disk('public')->delete($card->photo);

        // Remove the photo path from the card
        $card->photo = null;
        $card->save();

        $this->logAction($card_id, ' Deleted the attachment photo of this card.');

        return response()->json([
            'success' => true,
            'message' => "Photo deleted successfully",
        ], 200);
    }
    public function updateDescPhoto(Request $request, $card_id)
    {
        $card = Card::find($card_id);
        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => "Card not found",
            ], 404);
        }
        // Validate the request
        $request->validate([
            'description_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);

        // If there's an existing photo, delete it
        if ($card->description_photo) {
            Storage::disk('public')->delete($card->description_photo);
        }

        // Store the new photo
        $photoPath = $request->file('description_photo')->store('card-description-photo', 'public');

        // Update the card's photo path
        $card->description_photo = $photoPath;
        $card->save();
        $user = auth()->user();
        $logMessage = "updated the description photo for this card.";
        $card->details()->create([
            'user_id' => $user->id,
            'desc' => $logMessage,
            'card_id' => $card->id,
        ]);
        return response()->json([
            'success' => true,
            'message' => "Description photo updated successfully",
            'photo_url' => Storage::url($card->description_photo),
        ], 200);
    }
    public function deleteDescPhoto($card_id)
    {
        $card = Card::find($card_id);

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => "Card not found",
            ], 404);
        }

        if (!$card->description_photo) {
            return response()->json([
                'success' => false,
                'message' => "No photo to delete",
            ], 404);
        }

        // Delete the photo
        Storage::disk('public')->delete($card->description_photo);

        // Remove the photo path from the card
        $card->description_photo = null;
        $card->save();

        $user = auth()->user();
        $logMessage = "deleted the description photo from this card.";
        $card->details()->create([
            'user_id' => $user->id,
            'desc' => $logMessage,
            'card_id' => $card->id,
        ]);
        return response()->json([
            'success' => true,
            'message' => "Description photo deleted successfully",
        ], 200);
    }


public function storeFiles(Request $request)
{
    // Validate the request
    $validated = $request->validate([
        'card_id' => 'required|exists:cards,id', // Check if card exists
        'files' => 'required|array', // Ensure 'files' is an array
        'files.*' => 'file|max:30000', // Validate each file in the array
    ]);

    // Find the card by its ID
    $card = Card::find($validated['card_id']);

    // Check if the card was found
    if (!$card) {
        return response()->json([
            'success' => false,
            'message' => 'Card not found.',
        ], 404);
    }

    // Initialize arrays for storing file information
    $uploadedFileNames = [];
    $files_path = [];

    // Check if the request contains files
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            $randomNumber = mt_rand(10000, 99999);
            $randomNumber2 = mt_rand(10000, 99999);
            $uniqueName = $randomNumber . '_' . $randomNumber2 . '_' . $originalName;
            $path = $file->storeAs('card_files', $uniqueName, 'public');
            
            // Store the file path in the database associated with the card
            $card->files()->create(['file_path' => $path]);
            $files_path[] = $path;
            $uploadedFileNames[] = $uniqueName;
        }
    }

    // Log the uploaded file information
    $user = auth()->user();
    $fileNamesString = implode(', ', $uploadedFileNames);
    $logMessage = "Uploaded files: ($fileNamesString).";

    // Create a log entry in the card details
    $card->details()->create([
        'user_id' => $user->id,
        'desc' => $logMessage,
        'card_id' => $card->id,
    ]);

    // Return a successful response with the uploaded file paths
    return response()->json([
        'success' => true,
        'message' => 'Files uploaded successfully.',
        'data' => $files_path,
    ], 201);
}


public function overrideFiles(Request $request)
{
    // Validate the request
    $validated = $request->validate([
        'card_id' => 'required|exists:cards,id',
        'files.*' => 'file|max:30000', // Validate each file in the array
    ]);

    // Find the card by its ID
    $card = Card::find($validated['card_id']);

    // Check if there are existing files and delete them
    if ($card->files()->count() > 0) {
        foreach ($card->files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path); // Delete the file from storage
            }
            $file->delete(); // Delete the file record from the database
        }
    }

    $uploadedFileNames = [];
    $files_path = [];

    // Check if the request contains new files to upload
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            $randomNumber = mt_rand(10000, 99999);
            $randomNumber2 = mt_rand(10000, 99999);
            $uniqueName = $randomNumber . '_' . $randomNumber2 . '_' . $originalName;
            $path = $file->storeAs('card_files', $uniqueName, 'public'); // Store the file
            $card->files()->create(['file_path' => $path]); // Create a new record in the files table
            $files_path[] = $path;
            $uploadedFileNames[] = $uniqueName;
        }
    }

    // Log the upload
    $user = auth()->user();
    $fileNamesString = implode(', ', $uploadedFileNames);
    $logMessage = "Uploaded files: ($fileNamesString).";

    // Create a log entry in the card details
    $card->details()->create([
        'user_id' => $user->id,
        'desc' => $logMessage,
        'card_id' => $card->id,
    ]);

    // Return a successful response with the new file paths
    return response()->json([
        'success' => true,
        'message' => 'Files uploaded successfully and previous files deleted.',
        'data' => $files_path,
    ], 201);
}

    public function deleteFile($id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found.',
            ], 404);
        }

        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $card = $file->card;

        $originalFileName = basename($file->file_path);

        $file->delete();

        $user = auth()->user();
        $logMessage = "Deleted file: ($originalFileName).";
        $card->details()->create([
            'user_id' => $user->id,
            'desc' => $logMessage,
            'card_id' => $card->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully.',
        ], 200);
    }
    
        public function deleteCardFiles($card_id)
    {
        // Find the card by ID
        $card = Card::find($card_id);

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Card not found.',
            ], 404);
        }

        // Iterate through each file associated with the card
        foreach ($card->files as $file) {
            // Check if the file exists in storage and delete it
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            // Delete the file record from the database
            $file->delete();
        }

        // Log the deletion of all files
        $user = auth()->user();
        $logMessage = "Deleted all files from this card.";
        $card->details()->create([
            'user_id' => $user->id,
            'desc' => $logMessage,
            'card_id' => $card->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'All files for the card have been deleted successfully.',
        ], 200);
    }
}
