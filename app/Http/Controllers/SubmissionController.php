<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    //Abdelrhman - create submission
    public function store(Request $request, $form_id)
    {

        // Create a new submission record
        $submission = Submission::create([
            'form_id' => $form_id,
        ]);

        return response()->json(['submission' => $submission]);
    }

}

