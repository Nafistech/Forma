<?php

namespace App\Http\Controllers;


use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class SettingController extends Controller
{

    //Abdelrhman - Show all Settings
    public function index()
    {
        $settings = Setting::all();

        return response()->json(['setting' => $settings]);
    }

        //Abdelrhman - create / update new Settings
    public function storeOrUpdate(Request $request, $form_id)
    {
        // Find the setting record by form_id
        $setting = Setting::where('form_id', $form_id)->first();

        // If the setting record exists, update it; otherwise, create a new one
        if ($setting) {
            // Update the setting record

            // Validation
            $validator = Validator::make($request->all(), [
                'page_header' => ['required', 'string'],
                'page_outro' => ['required', 'string'],
                'logo' => ['image', 'mimes:jpg,png,jpeg,svg'],
                'fb_link' => ['required', 'url'],
                'instagram_link' => ['required', 'url'],
                'twitter_link' => ['required', 'url'],
                'bg_color' => ['required', 'string'],
                'text_color' => ['required', 'string'],
                'primary_color' => ['required', 'string'],
            ]);

            // If validation fails, return the errors
            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()
                ], 422);
            }

            // Update the setting record
            $setting->update([
                'page_header' => $request->page_header,
                'page_outro' => $request->page_outro,
                'fb_link' => $request->fb_link,
                'instagram_link' => $request->instagram_link,
                'twitter_link' => $request->twitter_link,
                'bg_color' => $request->bg_color,
                'text_color' => $request->text_color,
                'primary_color' => $request->primary_color,
            ]);

            // Check if a file image is in the request and update the logo if present
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $fileName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();  // Generate a unique file name
                Storage::putFileAs('public/assets/settings', $file, $fileName);  // Store the file in its path
                // Update the company logo name in settings
                $setting->update(['logo' => $fileName]);
            }

            // Return a success response
            return response()->json(['message' => 'Settings updated successfully']);
        } else {
            // Validation for creating a new record
            $validator = Validator::make($request->all(), [
                'form_id' => ['required', 'string'],
                'page_header' => ['required', 'string'],
                'page_outro' => ['required', 'string'],
                'logo' => ['image', 'mimes:jpg,png,jpeg,svg'],
                'fb_link' => ['required', 'url'],
                'instagram_link' => ['required', 'url'],
                'twitter_link' => ['required', 'url'],
                'bg_color' => ['required', 'string'],
                'text_color' => ['required', 'string'],
                'primary_color' => ['required', 'string'],
            ]);

            // If validation fails, return the errors
            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()
                ], 422);
            }

            // Create a new setting record
            $setting = Setting::create([
                'form_id' => $request->form_id,
                'page_header' => $request->page_header,
                'page_outro' => $request->page_outro,
                'fb_link' => $request->fb_link,
                'instagram_link' => $request->instagram_link,
                'twitter_link' => $request->twitter_link,
                'bg_color' => $request->bg_color,
                'text_color' => $request->text_color,
                'primary_color' => $request->primary_color,
            ]);

            // Check if a file image is in the request and store the logo if present
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $fileName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();  // Generate a unique file name
                Storage::putFileAs('public/assets/settings', $file, $fileName);  // Store the file in its path
                // Update the company logo name in settings
                $setting->update(['logo' => $fileName]);
            }

            // Return a success response
            return response()->json(['message' => 'Settings created successfully']);
        }
    }

}
