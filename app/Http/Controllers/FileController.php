<?php

namespace App\Http\Controllers;
use Google\Client;
use App\Models\File;
use Google\Service\Drive;
use Google\Service\Sheets;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\Spreadsheet;

class FileController extends Controller
{
    // public function token()
    // {
    //     $client_id = \config('services.google.client_id');
    //     $client_secret = \config('services.google.client_secret');
    //     $refresh_token = \config('services.google.refresh_token');
    //     $folder_id = \Config('services.google.folder_id');

    //     $response=Http::post('https://oauth.googleapis.com/token',[
    //         'client_id' => $client_id,
    //         'client_secret' => $client_secret,
    //         'refresh_token' => $refresh_token,
    //         'grant_type' => 'refresh_token',
    //     ]);
    //     // $accessToken=json_decode((string)$response->getBody(),true)['access_token'];
    //     // return $accessToken;
    //      // Check if the response is successful
    //     if ($response->successful()) {
    //     $responseData = $response->json();
    //     // Check if the response body contains the access token
    //     if (isset($responseData['access_token'])) {
    //         return $responseData['access_token'];
    //     } else {
    //         // Handle case where access token is missing in response body
    //         throw new \Exception('Access token not found in response body');
    //     }
    //     } else {
    //         // Handle case where the response is not successful
    //     throw new \Exception('Failed to fetch access token: ' . $response->status());
    //     }
    // }

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

    // public function saveToGoogleSheet(Request $request)
    // {
    //     // Initialize Google Client
    //     $client = new Client();
    //     $client->setAuthConfig(storage_path('app/client_secret_542750891480-o3smbc3hntirhvdij4oenl2sm0k5hqng.apps.googleusercontent.com.json')); // Path to your credentials JSON file
    //     $client->addScope(Drive::DRIVE);
    //     $client->addScope(Sheets::SPREADSHEETS);

    //     // Authenticate and authorize the client
    //     $client->setAccessToken($request->session()->get('access_token'));

    //     // Create Google Drive and Google Sheets Service
    //     $driveService = new Drive($client);
    //     $sheetsService = new Sheets($client);

    //     // Create a new Google Sheet
    //     $spreadsheet = new Spreadsheet([
    //         'properties' => [
    //             'title' => 'Responses', // Change the title as needed
    //         ],
    //     ]);
    //     $spreadsheet = $sheetsService->spreadsheets->create($spreadsheet);
    //     $spreadsheetId = $spreadsheet->spreadsheetId;

    //     // Get the range for the data
    //     $range = 'Responses'; // Change the sheet name if needed

    //     // Format data to be inserted into the sheet
    //     $values = [
    //         [$request->input('field1'),
    //          $request->input('field2')
    //          , ] // Format your data accordingly
    //     ];

    //     $body = new ValueRange([
    //         'values' => $values
    //     ]);

    //     // Insert data into the sheet
    //     $result = $sheetsService->spreadsheets_values->update($spreadsheetId, $range, $body, [
    //         'valueInputOption' => 'RAW'
    //     ]);

    //     // Handle the result...

    //     return redirect()->back()->with('success', 'Data saved to Google Sheet successfully.');
    // }


    public function saveToGoogleSheet(Request $request)
    {
        // Initialize Google Client
        $client = new Client();
        $client->setAuthConfig(storage_path('app/client_secret_542750891480-o3smbc3hntirhvdij4oenl2sm0k5hqng.apps.googleusercontent.com'));
        $client->addScope(Drive::DRIVE);
        $client->addScope(Sheets::SPREADSHEETS);

        // Authenticate and authorize the client
        $client->setAccessToken($request->session()->get('access_token'));

        // Create Google Sheets Service
        $sheetsService = new Sheets($client);

        // Create a new Google Sheet
        $spreadsheet = new Spreadsheet([
            'properties' => [
                'title' => 'Responses', // Change the title as needed
            ],
        ]);
        $spreadsheet = $sheetsService->spreadsheets->create($spreadsheet);
        $spreadsheetId = $spreadsheet->spreadsheetId;

        // Get the range for the data
        $range = 'Responses'; // Change the sheet name if needed

        // Format data to be inserted into the sheet
        $values = [
            [$request->input('field1'), $request->input('field2')], // Format your data accordingly
        ];

        $body = new ValueRange([
            'values' => $values
        ]);

        // Insert data into the sheet
        $result = $sheetsService->spreadsheets_values->update($spreadsheetId, $range, $body, [
            'valueInputOption' => 'RAW'
        ]);

        // Handle the result...

        return redirect()->back()->with('success', 'Data saved to Google Sheet successfully.');
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
        // public function store(Request $request)
        // {
        //     $validation=$request->validate([
        //         'file' => 'file|required',
        //         'file_name' => 'required',
        //     ]);

        //     $accessToken=$this->token();
        //     $name = $request->file->getClientOriginalName();
        //     $mime = $request->file->getClientMimeType();
        //         $path=$request->file->getRealPath();

        //     $response=Http::withToken($accessToken)
        //     ->attach('data',file_get_contents($path),$name)
        //     ->post('https://www.googleapis.com/upload/drive/v3/files',
        //     [
        //         'name' => $name,
        //     ],
        //     [
        //         'content_type' => 'application/octet-stream',
        //     ]
        //     );

        //         if ($response->successful()) {
        //             $file_id=json_decode($response->body())->id;
        //             $uploadedfile = new File;
        //             $uploadedfile->file_name=$request->file_name;
        //             $uploadedfile->file_id=$file_id;
        //             $uploadedfile->save();
        //             return response ('File upload successful');
        //         }else {
        //             return response ('Failed to upload file');
        //         }
        // }


        // public function store(Request $request)
        // {
        //     // Validate the request
        //     $validation = $request->validate([
        //         'file' => 'file|required',
        //         'file_name' => 'required',
        //     ]);

        //     // Get access token
        //     $accessToken = $this->token();

        //     // Upload file to Google Drive
        //     $name = $request->file->getClientOriginalName();
        //     $path = $request->file->getRealPath();
        //     $driveResponse = Http::withToken($accessToken)
        //         ->attach('data', file_get_contents($path), $name)
        //         ->post('https://www.googleapis.com/upload/drive/v3/files', ['name' => $name], ['content_type' => 'application/octet-stream']);

        //     // Check if file upload to Google Drive was successful
        //     if ($driveResponse->successful()) {
        //         // Get file ID from Google Drive response
        //         $fileId = json_decode($driveResponse->body())->id;

        //         // Save response to Google Sheets
        //         $client = new Client();
        //         $client->setAuthConfig(storage_path('app/client_secret_542750891480-o3smbc3hntirhvdij4oenl2sm0k5hqng.apps.googleusercontent.com.json'));
        //         $client->addScope(Drive::DRIVE);
        //         $client->addScope(Sheets::SPREADSHEETS);
        //         $client->setAccessToken($request->session()->get('access_token'));

        //         // Create Google Sheets Service
        //         $sheetsService = new Sheets($client);

        //         // Get spreadsheet ID
        //         $spreadsheetId = '1CEWqjz2PSisWZyM52kcC_7h-0-h-d-q0Es1CHk4v6sA/edit#gid=0'; // Update with your spreadsheet ID

        //         // Define the range for the data
        //         $range = 'Sheet1'; // Change the sheet name if needed

        //         // Format data to be inserted into the sheet
        //         $values = [
        //             [$request->input('field1'), $request->input('field2')],
        //         ];

        //         // Create value range object
        //         $body = new \Google\Service\Sheets\ValueRange([
        //             'values' => $values,
        //         ]);

        //         // Insert data into the sheet
        //         $sheetsResponse = $sheetsService->spreadsheets_values->append($spreadsheetId, $range, $body, [
        //             'valueInputOption' => 'RAW',
        //         ]);

        //         // Check if data insertion to Google Sheets was successful
        //         if ($sheetsResponse->getStatus() === 'OK') {
        //             // Save file ID to your database or perform any other necessary actions
        //             $uploadedFile = new File;
        //             $uploadedFile->file_name = $request->file_name;
        //             $uploadedFile->file_id = $fileId;
        //             $uploadedFile->save();

        //             return response('File uploaded to Google Drive and data saved to Google Sheets successfully');
        //         } else {
        //             // Handle error
        //             return response('Failed to save data to Google Sheets');
        //         }
        //     } else {
        //         // Handle error
        //         return response('Failed to upload file to Google Drive');
        //     }
        // }

        public function store(Request $request)
        {
            try {
                // Validate the request
                $validation = $request->validate([
                    'file' => 'file|required',
                    'file_name' => 'required',
                    // Add more validation rules as needed
                ]);

                // Get access token
                $accessToken = $this->token();

                // Upload file to Google Drive
                $name = $request->file->getClientOriginalName();
                $path = $request->file->getRealPath();
                $driveResponse = Http::withToken($accessToken)
                    ->attach('data', file_get_contents($path), $name)
                    ->post('https://www.googleapis.com/upload/drive/v3/files', ['name' => $name], ['content_type' => 'application/octet-stream']);

                if ($driveResponse->successful()) {
                    // Get file ID from Google Drive response
                    $fileId = json_decode($driveResponse->body())->id;

                    // Save response to Google Sheets
                    $client = new \Google\Client();
                    $client->setAuthConfig(storage_path('app/client_secret_542750891480-o3smbc3hntirhvdij4oenl2sm0k5hqng.apps.googleusercontent.com.json'));
                    $client->addScope(Drive::DRIVE);
                    $client->addScope(Sheets::SPREADSHEETS);
                    $client->setAccessToken($request->session()->get('access_token'));

                    // Create Google Sheets Service
                    $sheetsService = new Sheets($client);

                    // Get spreadsheet ID
                    $spreadsheetId = 'YOUR_SPREADSHEET_ID'; // Update with your spreadsheet ID

                    // Define the range for the data
                    $range = 'Sheet1'; // Change the sheet name if needed

                    // Format data to be inserted into the sheet
                    $values = [
                        [$request->input('field1'), $request->input('field2')],
                    ];

                    // Create value range object
                    $body = new ValueRange([
                        'values' => $values,
                    ]);

                    // Insert data into the sheet
                    $sheetsResponse = $sheetsService->spreadsheets_values->append($spreadsheetId, $range, $body, [
                        'valueInputOption' => 'RAW',
                    ]);

                    if ($sheetsResponse->getStatus() === 'OK') {
                        // Save file ID to your database or perform any other necessary actions
                        $uploadedFile = new File;
                        $uploadedFile->file_name = $request->file_name;
                        $uploadedFile->file_id = $fileId;
                        $uploadedFile->save();

                        return response('File uploaded to Google Drive and data saved to Google Sheets successfully');
                    } else {
                        // Handle error
                        Log::error('Failed to save data to Google Sheets: ' . $sheetsResponse->getBody());
                        return response('Failed to save data to Google Sheets');
                    }
                } else {
                    // Handle error
                    Log::error('Failed to upload file to Google Drive: ' . $driveResponse->getBody());
                    return response('Failed to upload file to Google Drive');
                }
            } catch (\Exception $e) {
                // Handle any other exceptions
                Log::error('An error occurred: ' . $e->getMessage());
                return response('An error occurred. Please try again later.');
            }
        }


    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        //
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
