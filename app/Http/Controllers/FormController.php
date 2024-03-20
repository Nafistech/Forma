<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class FormController extends Controller
{
        //Abdelrhman - show all forms that created by userId
        public function index()
        {
            // Extract the access token from the request headers
            $access_token = request()->header("authorization-token");

            // Check if the access token is provided
            if ($access_token !== null) {
                // Find the user associated with the access token
                $user = User::where("access_token", $access_token)->first();

                // Check if the user is found
                if ($user !== null) {
                    // Retrieve the user ID
                    $user_id = $user->id;

                    // Retrieve all forms created by the user ID
                    $forms = Form::with('sheet')->where('user_id', $user_id)->get();

                    // Return the forms associated with the user
                    return response()->json(['forms' => $forms]);
                } else {
                    // Access token is not associated with any user
                    return response()->json(['error' => 'Invalid access token'], 401);
                }
            } else {
                // Access token is not provided
                return response()->json(['error' => 'Access token not provided'], 401);
            }
        }

        //Abdelrhman - create a new form
        public function store(Request $request)
        {

            $access_token=$request->header("authorization-token");
            if ($access_token !==null) {
            $user=User::where("access_token",$access_token)->first();
            $user_id=$user->id;
            }
            // Validate form
            $validator = Validator::make($request->all(), [
                'form_title' => ['required', 'string'],
                'form_description' => ['nullable', 'string'],
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
                'user_id' => $user_id,
            ]);

            return response()->json(['form' => $form]);
        }


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
             $settings = $form->settings;

             return response()->json([
                 'form' => $form,
                 'fields' => $fields,
                 'settings' => $settings,
             ]);
        }
         public function showWithSubmissions($form_id)
        {
             // Find the form by its ID
             $form = Form::find($form_id);

             // Check if the form exists
             if (!$form) {
                 return response()->json(['message' => 'Form not found'], 404);
             }

             $fields = $form->fields()->get();
             $submissions = Submission::where('form_id' , $form_id)->with('submissionData.field')->get();

             return response()->json([
                 'form' => $form,
                 'fields' => $fields,
                 'submissions' => $submissions,
             ]);
        }


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
            $form = Form::find($id);
            if ($form) {
                $form->delete();
                return response()->json(['message' => 'Form Deleted successfully']);
            }
            else{
                return response()->json([
                    "msg"=>"Form not Found"
                ],404);
            }
        }
        public function resetForm($id)
        {
            $form = Form::find($id);
            if ($form) {
                $form->resetForm();
                return response()->json(['message' => 'Form Deleted successfully']);
            }
            else{
                return response()->json([
                    "msg"=>"Form not Found"
                ],404);
            }
        }
}
