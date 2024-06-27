<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting = Setting::take(1)->first();
        return new SettingResource($setting);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $setting = Setting::find($id);
        if (!$setting) {
            return response()->json(['message' => 'Setting not found'], 404);
        }
        return new SettingResource($setting);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'company_name' => 'required',
            'company_phone' => [
                'required',
                'regex:/^(\+?88)?01[3-9]\d{8}$/',
            ],
            'company_address' => 'required',
            'charge_in_dhaka' => 'required',
            'charge_out_dhaka' => 'required',
        ], [
            'company_phone.regex' => 'Invalid Bangladeshi phone number.',
        ]);

        try {
            $setting = Setting::find($id);
            if (!$setting) {
                return response()->json(['message' => 'Setting not found!'], 404);
            }
            $setting->update($request->except('company_logo'));
            if ($request->hasFile('company_logo')) {
                $image = $request->file('company_logo');
                $filename = time() . uniqid() . "." . $image->extension();
                $location = public_path('images/setting');
                $image->move($location, $filename);
                $setting->company_logo = $filename;
            }
            $setting->save();

            return response()->json(['message' => 'Setting updated successfully!', 'data' => new SettingResource($setting)], 200);
        } catch (\Exception $e) {
            // Handle the exception here
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
