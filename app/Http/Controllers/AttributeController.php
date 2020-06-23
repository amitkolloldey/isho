<?php

namespace App\Http\Controllers;

use App\Attribute;
use App\AttributeValue;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     */
    public function index()
    {
        return view('admin.pages.attributes');
    }

    /**
     * @return Application|ResponseFactory|Response
     */
    public function attributes()
    {
        $attributes = Attribute::orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return response([
            'attributes' => $attributes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        return view('admin.pages.attribute_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response([
                'validation' => $validator->errors()
            ]);
        }

        // Creating The Attribute
        $attribute = Attribute::create([
            'name' => $request->name,
        ]);
        foreach ($request->value as $attr_value) {
            if (isset($attr_value['value'])) {
                $attribute->values()->create([
                    'value' => $attr_value['value']
                ]);
            }
        }
        return response([
            'success' => 'Successfully Added'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|Response|View
     */
    public function edit($id)
    {
        $attribute = Attribute::findOrFail($id);
        return view('admin.pages.attribute_edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $attribute = Attribute::findOrFail($id);
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response([
                'validation' => $validator->errors()
            ]);
        }
        // Updating The Attribute
        $attribute->update([
            'name' => $request->name,
        ]);
        // Checking If The Add Option Request Exists
        if ($request->value) {
            // If Old Records Exist then Update It Otherwise Create
            if (count($attribute->values)) {
                // Looping Through Add Option Requests Array
                foreach ($request->value as $value) {
                    // Checking if the Value ID exists
                    if (isset($value['id'])) {
                        // Getting the value by the Value ID
                        $value = AttributeValue::findOrFail($value['id']);
                        // Updating The Value Only
                        $value->update([
                            'value' => $value['value'],
                        ]);
                    } else {
                        // Creating Value For new Value Field
                        $attribute->values()
                            ->create([
                                'attribute_id' => $attribute->id,
                                'value' => $value['value'],
                            ]);
                    }
                }
            } else {
                // Looping Through Add Option Requests Array If No Record Exists
                foreach ($request->value as $value) {
                    // Creating new Values
                    $attribute->values()->create([
                        'attribute_id' => $attribute->id,
                        'value' => $value['value'],
                    ]);
                }
            }
        }

        return response([
            'success' => 'Successfully Updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        // Getting The Attribute
        $attribute = Attribute::findOrFail($id);

        $attribute->delete();

        return response([
            'success' => "Record Deleted Successfully!"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function valueDestroy($id)
    {
        // Getting The Value
        $value = AttributeValue::findOrFail($id);

        $value->delete();

        return response([
            'success' => "Record Deleted Successfully!"
        ]);
    }
}
