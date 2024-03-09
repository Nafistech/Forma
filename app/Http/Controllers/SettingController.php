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
                'page_header' => ['nullable', 'string'],
                'page_outro' => ['nullable', 'string'],
                'logo' => ['image', 'mimes:jpg,png,jpeg,svg'],
                'fb_link' => ['nullable', 'url'],
                'instagram_link' => ['nullable', 'url'],
                'twitter_link' => ['nullable', 'url'],
                'bg_color' => ['nullable', 'string'],
                'text_color' => ['nullable', 'string'],
                'primary_color' => ['nullable', 'string'],
            ]);

            // If validation fails, return the errors
            if ($validator->fails()) {
                return response()->json([
                    "errors" => $validator->errors()
                ], 422);
            }

            if($request->setting_type == "general") {
                $setting->update([
                    'bg_color' => $request->bg_color,
                    'text_color' => $request->text_color,
                    'primary_color' => $request->primary_color,
                ]);
            }
            if($request->setting_type == "welcomePage") {
                $setting->update([
                    'page_header' => $request->page_header,
                ]);
            }
            if($request->setting_type == "endingPage") {
                $setting->update([
                    'page_outro' => $request->page_outro,
                    'fb_link' => $request->fb_link,
                    'instagram_link' => $request->instagram_link,
                    'twitter_link' => $request->twitter_link,
                ]);
            }

            // Check if a file image is in the request and update the logo if present
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $img_path = 'images/logos';
                $file_name = $file->getClientOriginalName();
                $path = $file->move($img_path,$file_name);
                $url = url(asset($path));
                $setting->update(['logo' => $url]);
            }

            // Return a success response
            return response()->json(['settings' => $setting]);
        } else {
            // Validation for creating a new record
            $validator = Validator::make($request->all(), [
                'form_id' => ['nullable', 'string'],
                'page_header' => ['nullable', 'string'],
                'page_outro' => ['nullable', 'string'],
                'logo' => ['image', 'mimes:jpg,png,jpeg,svg'],
                'fb_link' => ['nullable', 'url'],
                'instagram_link' => ['nullable', 'url'],
                'twitter_link' => ['nullable', 'url'],
                'bg_color' => ['nullable', 'string'],
                'text_color' => ['nullable', 'string'],
                'primary_color' => ['nullable', 'string'],
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
                $img_path = 'images/logos';
                $file_name = $file->getClientOriginalName();
                $path = $file->move($img_path,$file_name);
                $url = url(asset($path));
                $setting->update(['logo' => $url]);
            }

            // Return a success response
            return response()->json(['settings' => $setting]);
        }
    }

}
