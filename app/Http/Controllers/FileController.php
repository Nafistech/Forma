<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;;

class FileController extends Controller
{
    //Abdelrhman - Access Token
    public function generateAccessTokenFromRefreshToken($refreshToken)
    {
        try {
            $googleClientId = config('services.google.client_id');
            $googleClientSecret = config('services.google.client_secret');

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'refresh_token',
                'client_id' => $googleClientId,
                'client_secret' => $googleClientSecret,
                'refresh_token' => $refreshToken,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'];
                $refreshToken = $data['refresh_token'] ?? $refreshToken;
                return ['access_token' => $accessToken, 'refresh_token' => $refreshToken];
            } else {
                $error = $response->json();
                logger()->error('Failed to obtain access and refresh tokens. Error: ' . json_encode($error));
                return null;
            }
        } catch (\Exception $e) {
            logger()->error('Exception occurred while generating access token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $files=File::all();

        return view('create',compact('files'));
    }



// function createGoogleDriveFolder($accessToken)
//  {
//     // Define the folder name
//     $folderName = 'Folder';

//     // Google Drive API endpoint for creating a folder
//     $url = 'https://www.googleapis.com/drive/v3/files';

//     // Folder metadata
//     $folderMetadata = [
//         'name' => $folderName,
//         'mimeType' => 'application/vnd.google-apps.folder'
//     ];

//     // Guzzle HTTP client
//     $client = new Client();

//     // Send a POST request to create the folder
//     $response = $client->post($url, [
//         'headers' => [
//             'Authorization' => 'Bearer ' . $accessToken,
//             'Content-Type' => 'application/json'
//         ],
//         'json' => $folderMetadata
//     ]);

//     // Check if the request was successful
//     if ($response->getStatusCode() == 200) {
//         // Folder created successfully
//         $responseData = json_decode($response->getBody(), true);
//         $folderId = $responseData['id'];

//         // Set folder permissions to 'Anyone with the link'
//         $permissions = [
//             'role' => 'reader',
//             'type' => 'anyone'
//         ];

//         // Google Drive API endpoint for setting folder permissions
//         $permissionsUrl = "https://www.googleapis.com/drive/v3/files/{$folderId}/permissions";

//         // Send a POST request to set folder permissions
//         $client->post($permissionsUrl, [
//             'headers' => [
//                 'Authorization' => 'Bearer ' . $accessToken,
//                 'Content-Type' => 'application/json'
//             ],
//             'json' => $permissions
//         ]);

//         // Return the folder ID
//         return $folderId;
//     } else {
//         // Failed to create folder
//         return null;
//     }
// }

     public function store(Request $request)
    {
        $validation = $request->validate([
            'file' => 'file|required',
            'file_name' => 'required',
        ]);

        $access_token = request()->header("access_token");
        $user = User::where("access_token", $access_token)->first();

        $refreshToken = $user->google_refresh_token;
        $tokens = $this->generateAccessTokenFromRefreshToken($refreshToken);

        // Check if access token and refresh token were obtained successfully
        if (!$tokens || !isset($tokens['access_token'])) {
            return response()->json(['message' => 'Failed to obtain access token'], 500);
        }

        // Retrieve the file information
        $name = $request->file->getClientOriginalName();
        $path = $request->file->getRealPath();

        // Upload the file to Google Drive
        $response = Http::withToken($tokens['access_token'])
            ->attach('data', file_get_contents($path), $name)
            ->post('https://www.googleapis.com/upload/drive/v3/files', [
                'name' => $name,
                'uploadType' => 'media', // Specify upload type as 'media'
            ]);

        // Check if the file upload was successful
        if ($response->successful()) {
            $file_id = json_decode($response->body())->id;

            // Update the file permissions to allow access for anyone with the link
            $permissionResponse = Http::withToken($tokens['access_token'])
                ->post("https://www.googleapis.com/drive/v3/files/{$file_id}/permissions", [
                    'role' => 'reader',
                    'type' => 'anyone',
                ]);

            if ($permissionResponse->successful()) {
                // File permissions updated successfully
                $uploadedfile = new File;
                $uploadedfile->file_name = $request->file_name;
                $uploadedfile->name = $name;
                $uploadedfile->fileid = $file_id;
                $uploadedfile->save();

                // Construct the download link for the uploaded file
                $apiKey = 'AIzaSyBP7SOMa9dcdXVWb1d6V3jcjMJRh0yWUjo'; // Replace with your actual API key
                $downloadLink = "https://www.googleapis.com/drive/v3/files/{$file_id}?alt=media&key={$apiKey}";

                return response()->json(['message' => 'File upload successful', 'download_link' => $downloadLink]);
            } else {
                // Failed to update file permissions
                return response()->json(['message' => 'Failed to update file permissions'], 500);
            }
        } else {
            // Failed to upload file
            return response()->json(['message' => 'Failed to upload file'], 500);
        }
    }



        //Abdelrhman - download the file id from google drive and download it to downloads file
        // public function show(File $file)
        // {
        //     $accessToken = $this->token();
        //     $response = Http::withHeaders([
        //         'Authorization' => 'Bearer ' . $accessToken,
        //     ])->get("https://www.googleapis.com/drive/v3/files/{$file->fileid}?alt=media");

        //     if ($response->successful()) {
        //         $ext = pathinfo($file->name, PATHINFO_EXTENSION);
        //         $filePath = '/downloads/' . $file->file_name . '.' . $ext;

        //         // Save the file content to storage
        //         Storage::put($filePath, $response->body());

        //         // Return a download response
        //         return Storage::download($filePath);
        //     } else {
        //         // Handle the case where the request fails
        //         return response()->json(['error' => 'Failed to download file'], $response->status());
        //     }
        // }

}
