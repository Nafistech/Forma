<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\GoogleSheets;
use Exception;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\Spreadsheet;
use Google\Service\Drive;
use Google\Service\Drive\Permission;
use Google\Service\Sheets\ValueRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GoogleSheetsController extends Controller
{
    public $client, $service, $documentId, $range;

    public function getClient(){
        $client = new Client();
        $client->setApplicationName('Google Sheets');
        $client->setRedirectUri('http://127.0.0.1:8000/googleSheets');
        $client->setScopes(Sheets::SPREADSHEETS);
        $client->setAuthConfig('credentials.json');
        $client->setAccessType('offline');
        $this->client = $client;
        $this->service = new Sheets($this->client);
        $this->range = 'A:Z';
    }
    public function grantPermission(Request $request , $documentId){

        $validator = Validator::make($request->all(), [
            'email_address' => ['required', 'email']
        ]);
        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors()
            ], 422);
        }

        $this->getClient();
        $driveClient = $this->client;
        $driveClient->setScopes(array(Drive::DRIVE_FILE, Sheets::SPREADSHEETS));
        $driveService = new Drive($driveClient);

        $permission = new Permission([
            'role' => 'writer', // or 'reader'
            'type' => 'user',
            'emailAddress' => $request->email_address,
        ]);
        try {
            $driveService->permissions->create($documentId, $permission);
            return response()->json(['message' => 'permission granted successfully'], 200);
            // Permission sharing successful
          } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
          }
    }
    public function createNewSpreadsheet(Request $request , $formId)
    {
        $this->getClient();

        $validator = Validator::make($request->all(), [
            'email_address' => ['required', 'email']
        ]);
        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors()
            ], 422);
        }
        $form = Form::find($formId);

        // Create a new spreadsheet resource (empty)
        $spreadsheet = new Spreadsheet([
            'properties' => [
                'title' => $form->form_title, // Set your desired title
            ],
        ]);

        try {
            // Send request to create the spreadsheet
            $spreadsheet = $this->service->spreadsheets->create($spreadsheet);
            $this->documentId = $spreadsheet->spreadsheetId;
            $spreadsheetUrl = "https://docs.google.com/spreadsheets/d/" . $this->documentId . "/edit";

            $googleSheet = GoogleSheets::create([
                'document_url' => $spreadsheetUrl,
                'document_id' => $this->documentId,
                'form_id' => $formId,
            ]);
            $form->update([
                'google_sheet_id' => $googleSheet->id
            ]);

            $this->grantPermission($request , $this->documentId);

            return response()->json([
                'message' => 'Success message!',
                'spreadsheetUrl' => $spreadsheetUrl,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function appendSheet(Request $request , $formId){
        $validator = Validator::make($request->all(), [
            'values' => ['required' , 'array']
        ]);
        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors()
            ], 422);
        }
        $this->getClient();
        $form = Form::find($formId);
        if($form->google_sheet_id){
            $formGoogleSheet = GoogleSheets::find($form->google_sheet_id);
            $this->documentId = $formGoogleSheet->document_id;
            $values = [
                $request->values
            ];
            $body = new ValueRange([
                'values' => $values
            ]);
            $params = [
                'valueInputOption' => 'USER_ENTERED'
            ];
            $result = $this->service->spreadsheets_values->append($this->documentId, $this->range,
            $body , $params);
            return response()->json(['message' => 'appended successfully'], 200);
        }
        else{
            return response()->json(['message' => 'the form is not connected with google sheets'], 404);
        }
    }
}
