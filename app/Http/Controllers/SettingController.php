<?php

namespace App\Http\Controllers;


use App\Models\Setting;
use Illuminate\Http\Request;
use App\Helpers\SettingsUpdate;
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

    //Abdelrhman - Update an Existing Seetings
    public function update(Request $request)
    {
        //validation
        $validator =   Validator::make($request->all(), [
            'page_header' => ['required', 'string'],
            'logo' => ['image', 'mimes:jpg,png,jpeg,svg'],
            // 'page_outro' => ['required'],
            // 'fb_link' => ['required'],
            // 'twitter_link' => ['required'],
            // 'instagram_link' => ['required'],
            // 'Header_Color' => ['required'],
            // 'font_family' => ['required'],
            // 'font_size' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors()
            ], 301);
        }
        //Abdelrhman - Updates all settings
        SettingsUpdate::update('page_header', $request->page_header);
        SettingsUpdate::update('page_outro', $request->page_outro);
        SettingsUpdate::update('fb_link', $request->fb_link);
        SettingsUpdate::update('twitter_link', $request->twitter_link);
        SettingsUpdate::update('instagram_link', $request->instagram_link);
        SettingsUpdate::update('font_family', $request->font_family);
        SettingsUpdate::update('font_size', $request->font_size);
        SettingsUpdate::update('Header_Color', $request->Header_Color);
        // Check if a file image is in the request
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();  // Generate a unique file name
            Storage::putFileAs('public/assets/settings', $file, $fileName);  // Store the file in its path
            // Update the company logo name in settings
            SettingsUpdate::update('logo', $fileName);
        }
        return response()->json(['message' => 'Settings updated successfully']);
    }
}
