<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;



class FileController extends Controller
{
    //Abdelrhman - Access Token
    public function token()
    {
        try {
            $client_id = config('services.google.client_id');
            $client_secret = config('services.google.client_secret');
            $refresh_token = config('services.google.refresh_token');

            $response = Http::post('https://oauth2.googleapis.com/token', [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token',
            ]);

            if ($response->successful()) {
                $accessToken = $response->json()['access_token'];
                return $accessToken;
            } else {
                // Log the response body and status code
                logger()->error('Failed to fetch access token: '.$response->status());
                logger()->error('Response body: '.$response->body());
                return null;
            }
        } catch (\Exception $e) {
            // Handle any exceptions
            logger()->error('Exception occurred while fetching access token: '.$e->getMessage());
            return null;
        }
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $files=File::all();

        return view('create',compact('files'));
    }

    /**
     * Store a newly created resource in storage.
     */
    //Abdelrhman - Store the file upolad into google drive storage
        public function store(Request $request)
        {
            $validation=$request->validate([
                'file' => 'file|required',
                'file_name' => 'required',
            ]);

            $accessToken=$this->token();
            $name = $request->file->getClientOriginalName();
            $mime = $request->file->getClientMimeType();
                $path=$request->file->getRealPath();

            $response=Http::withToken($accessToken)
            ->attach('data',file_get_contents($path),$name)
            ->post('https://www.googleapis.com/upload/drive/v3/files',
            [
                'name' => $name,
            ],
            [
                'content_type' => 'application/octet-stream',
            ]
            );

                if ($response->successful()) {
                    $file_id=json_decode($response->body())->id;
                    $uploadedfile = new File;
                    $uploadedfile->file_name=$request->file_name;
                    $uploadedfile->name=$name;
                    $uploadedfile->fileid=$file_id;
                    $uploadedfile->save();


                  $downloadLink = "https://www.googleapis.com/drive/v3/files/{$file_id}?alt=media";

            return response()->json(['message' => 'File upload successful', 'download_link' => $downloadLink]);
            } else {
                return response()->json(['message' => 'Failed to upload file'], 500);
            }
         }

        //Abdelrhman - download the file id from google drive and download it to downloads file
        public function show(File $file)
        {
            $accessToken = $this->token();
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get("https://www.googleapis.com/drive/v3/files/{$file->fileid}?alt=media");

            if ($response->successful()) {
                $ext = pathinfo($file->name, PATHINFO_EXTENSION);
                $filePath = '/downloads/' . $file->file_name . '.' . $ext;

                // Save the file content to storage
                Storage::put($filePath, $response->body());

                // Return a download response
                return Storage::download($filePath);
            } else {
                // Handle the case where the request fails
                return response()->json(['error' => 'Failed to download file'], $response->status());
            }
        }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file)
    {
        //
    }

}
