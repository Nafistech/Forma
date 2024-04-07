<?php

namespace App\Http\Controllers;

use App\Models\SubmissionData;
use Illuminate\Http\Request;
use App\Models\Submission_data;
use Illuminate\Support\Facades\Validator;

class SubmissiondataController extends Controller
{
       //Kareem Ayman - create Submission Data
       public function store(Request $request )
       {
            // Validation
            $validator = Validator::make($request->all(), [
                'field_id' => ['required', 'string'],
                'submission_id' => ['required'],
                'field_value' => ['required'],
                'field_name' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Efficiently check for existing record using firstOrCreate
            $submissionData = SubmissionData::firstOrCreate([
                'field_id' => $request->field_id,
                'submission_id' => $request->submission_id,
            ], [
                'field_value' => $request->field_value,
                'field_name' => $request->field_name, 
            ]);
            return response()->json(['Submission Data' => $submissionData]);
       }
}
