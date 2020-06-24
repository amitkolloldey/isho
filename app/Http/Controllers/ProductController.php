<?php

namespace App\Http\Controllers;

use App\Attribute;
use App\Product;
use App\ProductValue;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     */
    public function index()
    {
        return view('admin.pages.products');
    }

    public function products()
    {
        $products = Product::with(['attributes'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return response([
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        $attributes = Attribute::with('values')->get();
        return view('admin.pages.product_create', compact('attributes'));
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
            'name' => 'required|max:255',
            'sku' => 'required',
            'price' => 'required',
            'stock' => 'required',
        ]);
        if ($validator->fails()) {
            return response([
                'validation' => $validator->errors()
            ]);
        }

        // Creating The Product
        $product = Product::create([
            'name' => $request->name,
            'sku' => $request->sku,
            'price' => $request->price,
            'main_image' =>$request->main_image_name,
            'description' => $request->description
        ]);
        foreach ($request->attribute_id as $attribute) {
            $product_attribute = $product->attributes()->create([
                'attribute_id' => $attribute
            ]);
            foreach ($request->attribute_values as $key => $attribute_value) {
                if (isset($attribute_value['sku']) && $attribute_value['price']) {
                    $product_attribute->attribute_product_attribute_values()->create([
                        'attribute_value_id' => $key,
                        'attribute_product_id' => $product_attribute->id,
                        'sku' => $attribute_value['sku'],
                        'price' => $attribute_value['price'],
                        'image' => $attribute_value['image_src']
                    ]);
                }
            }
        }

        $product->stocks()->create([
            'quantity' => $request->stock
        ]);

        return response([
            'success' => 'Successfully Added'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return Application|Factory|Response|View
     */
    public function edit($id)
    {
        $attributes = Attribute::with('values')->get()->toArray();
        $product = Product::findOrFail($id);
        foreach ($product->attributes as $attribute) {
            $attribute_id_list[] = $attribute->id;
        }
        $product_values = ProductValue::whereIn('attribute_product_id', array_unique($attribute_id_list))
            ->get()
            ->keyBy('attribute_value_id')
            ->toArray();
        return view('admin.pages.product_edit', compact('attributes', 'product', 'product_values'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'sku' => 'required',
            'price' => 'required',
            'stock' => 'required'
        ]);
        if ($validator->fails()) {
            return response([
                'validation' => $validator->errors()
            ]);
        }

        // Creating The Product
        $product->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'price' => $request->price,
            'description' => $request->description,
            'main_image' =>$request->main_image_name,
        ]);
        $product->attributes()->delete();
        foreach ($request->attribute_id as $attribute) {
            $product_attribute = $product->attributes()->create([
                'attribute_id' => $attribute
            ]);
            $product_attribute->attribute_product_attribute_values()->delete();
            foreach ($request->attribute_values as $key => $attribute_value) {
                if (isset($attribute_value['sku']) && $attribute_value['price']) {
                    $product_attribute->attribute_product_attribute_values()->create([
                        'attribute_value_id' => $key,
                        'attribute_product_id' => $product_attribute->id,
                        'sku' => $attribute_value['sku'],
                        'price' => $attribute_value['price'],
                        'image' => $attribute_value['image_src']
                    ]);
                }
            }
        }

        $product->stocks()->create([
            'quantity' => $request->stock
        ]);
        return response([
            'success' => 'Successfully Updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return Response
     */
    public function destroy($id)
    {
        // Getting The Product
        $product = Product::findOrFail($id);

        $product->delete();

        return response([
            'success' => "Record Deleted Successfully!"
        ]);
    }

    /**
     * @param Request $request
     * @return Application|Response
     */
    public function search(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'query' => 'required',
        ]);
        if ($validator->fails()) {
            return response([
                'validation' => $validator->errors()
            ]);
        }

        $query = $request->get('query');
        $products = Product::where('name', 'LIKE', "%" . $query . "%")
            ->get();
        return response([
            'products' => $products,
        ]);
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function productFetch(Request $request)
    {
        $query = $request->get('query');
        $products = Product::where('name', 'LIKE', "%" . $query . "%")
            ->take(15)
            ->get('name');

        return response([
            'products' => $products,
        ]);
    }

    public function mainImageUpload(Request $request)
    {
        if ($request->hasFile('main_image')) {
            $image = $request->file('main_image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $image_destination_path = public_path('/products/');
            $image_name = 'products/'.$image_name;
            $image->move($image_destination_path, $image_name);

            return $image_name;
        }
        return null;
    }

    public function attributeImageUpload(Request $request)
    {
        if ($request->hasFile('attribute_image')) {
            $image = $request->file('attribute_image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $image_destination_path = public_path('/product_attrs/');
            $image_name = 'product_attrs/'.$image_name;
            $image->move($image_destination_path, $image_name);

            return $image_name;
        }
        return null;
    }
}
