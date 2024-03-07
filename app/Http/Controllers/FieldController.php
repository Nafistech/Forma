<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FieldController extends Controller
{
     //Abdelrhman - create a new field
     public function store(Request $request)
     {
         // Validate form
         $validator = Validator::make($request->all(), [
                'form_id' => ['required', 'string'],
                'field_label' => ['required', 'string'],
                'field_type' => ['required', 'string'],
                'field_header' => ['nullable', 'string'],
                'more_options' => ['nullable', 'json'], // Validate as JSON
                'isRequired' => ['required', 'boolean'],
                'field_placeholder' => ['nullable', 'string'],
                'field_instructions' => ['nullable', 'string'],
                'field_order' => ['required', 'integer'],
                'value' => ['required', 'string'],
         ]);

         if ($validator->fails()) {
             return response()->json([
                 "errors" => $validator->errors()
             ], 422);
         }

         // Generate a random field_id
         $field_id = Str::random(10);

         // Create a new form with the generated form_id
         $field = Field::create([
             'field_id' => $field_id,
             'form_id' => $request->form_id,
             'field_label' => $request->field_label,
             'field_type' => $request->field_type,
             'field_header' => $request->field_header,
             'more_options' => $request->more_options,
             'isRequired' => $request->isRequired,
             'field_placeholder' => $request->field_placeholder,
             'field_instructions' => $request->field_instructions,
             'field_order' => $request->field_order,
             'value' => $request->value
         ]);

         return response()->json(['message' => 'Filed created successfully']);
     }

     //Abdelrhman - update the field by its id
     public function update(Request  $request, $id)
     {
             //validate field
         $validator=   Validator::make($request->all(),[
            'field_label' => ['required', 'string'],
            'field_type' => ['required', 'string'],
            'field_header' => ['nullable', 'string'],
            'more_options' => ['nullable', 'json'], // Validate as JSON
            'isRequired' => ['required', 'boolean'],
            'field_placeholder' => ['nullable', 'string'],
            'field_instructions' => ['nullable', 'string'],
            'field_order' => ['required', 'integer'],
            'value' => ['required', 'string'],
         ]);
         if($validator->fails())
         {
             return response()->json([
                 "errors"=>$validator->errors()
             ],301);
         }

         //Check if the form exists
         $form =Field::find($id);
         if ($form == null) {
             return response()->json([
                 "msg"=>"Field not Found"
             ],301);
         }
         //update the form
         $form->update([
            'field_label' => $request->field_label,
            'field_type' => $request->field_type,
            'field_header' => $request->field_header,
            'more_options' => $request->more_options,
            'isRequired' => $request->isRequired,
            'field_placeholder' => $request->field_placeholder,
            'field_instructions' => $request->field_instructions,
            'field_order' => $request->field_order,
            'value' => $request->value
         ]);

         return response()->json(['message' => 'Field Updated successfully']);
     }

      //Abdelrhman - delete the form by its id
      public function destroy($id)
      {
          //Check if the duration exists
          $duration =Field::find($id);
          if ($duration == null) {
              return response()->json([
                  "msg"=>"Field not Found"
              ],301);
          }
            //delete duration
          $duration->delete();
          return response()->json(['message' => 'Field Deleted successfully']);
      }
}
