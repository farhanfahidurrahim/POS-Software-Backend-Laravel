<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Customer\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;

class CustomerController extends BaseController
{
    public function customerList()
    {
        $customer = Customer::latest()->get();

        // return $this->sendResponse(CustomerResource::collection($customer), 'Customer retrieved successfully!');
        return response()->json([
            'data' => $customer,
        ]);
    }

    public function index()
    {
        $customer = Customer::latest()->paginate(20);

        // return $this->sendResponse(CustomerResource::collection($customer), 'Customer retrieved successfully!');
        return response()->json([
            'data' => $customer,
        ]);
    }


    public function create()
    {
        //
    }

    //Without Pathao city,zone,area Name

    // public function store(CustomerRequest $request)
    // {
    //     $customer = new Customer();
    //     $customer->name = $request->name;
    //     $customer->email = $request->email;
    //     $customer->phone_number = $request->phone_number;
    //     $customer->city_id = $request->city_id;
    //     $customer->zone_id = $request->zone_id;
    //     $customer->area_id = $request->area_id;
    //     $customer->location = $request->location;
    //     $customer->save();

    //     $customerResource = new CustomerResource($customer); // Create CustomerResource instance with the created customer

    //     return $this->sendResponse($customerResource, 'Customer Created Successfully!');
    // }

    // With Pathao city,zone,area Name
    public function store(CustomerRequest $request)
    {

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone_number = $request->phone_number;
        $customer->location = $request->location;
        // $customer->city_id = $request->city_id;
        // $customer->city_name = $request->city_name;
        // $customer->zone_id = $request->zone_id;
        // $customer->zone_name = $request->zone_name;
        // $customer->area_id = $request->area_id;
        // $customer->area_name = $request->area_name;
        $customer->save();

        // $customerResource = new CustomerResource($customer);
        return response()->json([
            'message' => 'Customer created successfully',
            'data' => new CustomerResource($customer),
        ], 200);
    }


    public function show($id)
    {
        $customer = Customer::where('id', $id)->get();

        return $this->sendResponse(CustomerResource::collection($customer), 'Customer retrieved successfully!');
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|email|unique:customers,email,' . $id,
            'phone_number' => [
                'required',
                'regex:/^(\+?88)?01[3-9]\d{8}$/',
                'unique:customers,phone_number,' . $id,
            ],
            'location' => 'required|string',
        ], [
            'phone_number.regex' => 'Invalid Bangladeshi phone number.',
        ]);

        $customer = Customer::find($id);

        if (!$customer) {
            return $this->sendError('Supplier not found!');
        }

        $customer->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone_number' => $validatedData['phone_number'],
            'location' => $validatedData['location'],
        ]);

        return $this->sendResponse($customer, 'Customer Updated Successfully!');
    }


    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return $this->sendResponse([], 'Customer Deleted Successfully!');
    }

    // Customer Search Based On Phone Number
    public function customerSearchPhoneNumber(Request $request, $phoneNumber)
    {
        if (!empty($phoneNumber)) {

            $customer = Customer::where("phone_number", "like", "%{$phoneNumber}%")->get();
            if ($customer->isEmpty()) {
                return response()->json([
                    'message' => "No Searching Data Found!"
                ], 404);
            }
            return CustomerResource::collection($customer);
        }
    }
}
