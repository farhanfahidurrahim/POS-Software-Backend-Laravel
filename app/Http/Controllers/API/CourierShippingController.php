<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourierShippingRequest;
use App\Http\Resources\CourierShippingResource;
use App\Models\CourierShipping;
use Illuminate\Http\Request;

class CourierShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courier_shippings = CourierShipping::all();
        if ($courier_shippings->isEmpty()) {
            return response()->json(['message' => 'No Courier shipping found'], 200);
        }
        return CourierShippingResource::collection($courier_shippings);
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
    public function store(CourierShippingRequest $request)
    {
        try {
            $courier_shipping = CourierShipping::create([
                'name' => $request->name,
                'status' => $request->status,
            ]);

            return response()->json(['message' => 'Courier shipping created successfully', 'data' => new CourierShippingResource($courier_shipping)], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Courier shipping creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $courier_shipping = CourierShipping::find($id);
        if (!$courier_shipping) {
            return response()->json(['message' => 'Courier shipping not found'], 404);
        }

        return new CourierShippingResource($courier_shipping);
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
            'name' => ['required', 'string', 'max:255', 'unique:courier_shippings,name,' . $id],
        ]);

        try {
            $courier_shipping = CourierShipping::find($id);
            if (!$courier_shipping) {
                return response()->json(['message' => 'Courier shipping not found'], 404);
            }
            $courier_shipping->update([
                'name' => $request->name,
                'status' => $request->status,
            ]);

            return response()->json(['message' => 'Courier shipping updated successfully', 'data' => new CourierShippingResource($courier_shipping)], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Courier shipping creation failed',
                'error' => $e->getMessage(),
            ], 500);
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
        $courier_shipping = CourierShipping::find($id);
        if (!$courier_shipping) {
            return response()->json(['message' => 'Courier shipping not found'], 404);
        }
        $courier_shipping->delete();

        return response()->json(['message' => 'Courier shipping deleted successfully'], 200);
    }
}
