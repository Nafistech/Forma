<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
        //Abdelrhman - show all forms that created by userId
        public function index($userId)
        {
            // Retrieve all forms created by the user ID
            $forms = Form::where('user_id', $userId)->get();

            // Check if any forms are found for the user
            if ($forms->isEmpty()) {
                return response()->json(['message' => 'No forms found for this user'], 404);
            }

            return response()->json(['forms' => $forms]);
        }

        //Abdelrhman - create a new form
        public function store(Request $request)
        {
            // Validate form
            $validator = Validator::make($request->all(), [
                'form_title' => ['required', 'string'],
                'form_description' => ['nullable', 'string'],
                'user_id' => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()
                ], 422); // Changed status code to 422 for validation errors
            }

            // Generate a random form_id
            $form_id = Str::random(10);

            // Create a new form with the generated form_id
            $form = Form::create([
                'form_id' => $form_id,
                'form_title' => $request->form_title,
                'form_description' => $request->form_description,
                'user_id' => $request->user_id,
            ]);

            return response()->json(['message' => 'Form created successfully']);
        }

         //Abdelrhman - Show the form by its id
         public function show($form_id)
        {
             // Find the form by its ID
             $form = Form::find($form_id);

             // Check if the form exists
             if (!$form) {
                 return response()->json(['message' => 'Form not found'], 404);
             }

             // Retrieve related fields, settings, and submissions
             $fields = $form->fields()->get();
             $settings = $form->settings()->get();
             $submissions = $form->submissions()->get();

             return response()->json([
                 'form' => $form,
                 'fields' => $fields,
                 'settings' => $settings,
                 'submissions' => $submissions,
             ]);
        }

         //Abdelrhman - update the form by its id
         public function update(Request  $request, $id)
        {
                //validate form
            $validator=   Validator::make($request->all(),[
                    'form_title' => ['required', 'string'],
                    'form_description' => ['nullable', 'string'],
            ]);
            if($validator->fails())
            {
                return response()->json([
                    "errors"=>$validator->errors()
                ],301);
            }

            //Check if the form exists
            $form =Form::find($id);
            if ($form == null) {
                return response()->json([
                    "msg"=>"Form not Found"
                ],301);
            }
            //update the form
            $form->update([
                'form_title' => $request->form_title,
                'form_description' => $request->form_description,
            ]);

            return response()->json(['message' => 'Form Updated successfully']);
        }

        //Abdelrhman - delete the form by its id
        public function destroy($id)
        {
            //Check if the duration exists
            $duration =Form::find($id);
            if ($duration == null) {
                return response()->json([
                    "msg"=>"Form not Found"
                ],301);
            }
              //delete duration
            $duration->delete();
            return response()->json(['message' => 'Form Deleted successfully']);
        }
}
