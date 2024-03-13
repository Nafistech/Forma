<?php

namespace App\Http\Controllers;

use App\Models\SubmissionData;
use Illuminate\Http\Request;
use App\Models\Submission_data;
use Illuminate\Support\Facades\Validator;

class SubmissiondataController extends Controller
{
       //Abdelrhman - create Submission Data
       public function store(Request $request )
       {
            // Validation
            $validator = Validator::make($request->all(), [
                'field_id' => ['required', 'string'],
                'submission_id' => ['required'],
                'field_value' => ['required', 'json'],
            ]);

            // If validation fails, return the errors
            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()
                ], 422);
            }

           // Create a new submission record
           $submissionData = SubmissionData::create([
               'field_id' => $request->field_id,
               'submission_id' => $request->submission_id,
               'field_value' => $request->field_value
           ]);

           return response()->json(['Submission Data' => $submissionData]);
       }
}
