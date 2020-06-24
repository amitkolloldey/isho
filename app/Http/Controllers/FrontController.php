<?php

namespace App\Http\Controllers;

use App\Order;
use App\Product;
use App\ProductValue;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class FrontController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function home()
    {
        $products = Product::orderBy('created_at', 'desc')
            ->paginate(20);
        return view('home', compact('products'));
    }

    /**
     * @param $slug
     * @return Application|Factory|View
     */
    public function product($slug)
    {
        $product_attribute_value_id_list = $product_attribute_value_image_list = [];

        $product = Product::findBySlugOrFail($slug);
        foreach ($product->attributes as $attribute) {
            $product_values = ProductValue::where('attribute_product_id', $attribute->id)
                ->get();
            foreach ($product_values as $product_value) {
                $product_attribute_value_id_list[] = $product_value->attribute_value_id;
                $product_attribute_value_image_list[$product_value->attribute_value_id] = $product_value->image;
            }
        }
        return view('product_show', compact('product', 'product_attribute_value_id_list', 'product_attribute_value_image_list'));
    }

    /**
     * @param Request $request
     * @return Application|Response
     */
    public function productShowPrice(Request $request)
    {
        $product_attribute_id = $request->product_attribute_id;

        $value_id = $request->value_id;

        $product_id = $request->product_id;

        $product = Product::findOrFail($product_id);

        $attribute_price = ProductValue::where('attribute_value_id', $value_id)
            ->where('attribute_product_id', $product_attribute_id)
            ->get('price')
            ->first();

        $total_price = ($product->price + $attribute_price->price);

        if (session()->has('currency') && session()->has('rate')) {
            $currency = session()->get('currency');
            $total_price = ($product->price + $attribute_price->price) * (session()
                    ->get('rate'));
        } else {
            $currency = "BDT";
            $total_price = ($product->price + $attribute_price->price);
        }
        return response([
            'total_price' => $total_price,
            'currency' => $currency,
        ]);
    }

    /**
     * @param Request $request
     * @return Application|Response
     */
    public function search(Request $request)
    {
        $query = $request->get('product_name');
        $products = Product::where('name', 'LIKE', "%" . $query . "%")
            ->paginate(15);
        return view('product_search_results', compact('products'));
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function currencySwitch(Request $request)
    {
        if (session()->has('rate') || session()->has('currency')) {
            session()->forget('rate');
            session()->forget('currency');
            session()->regenerate();
        }
        $key = $request->currency_key;
        $full_key = 'BDT_' . $key;
        $http = new Client;
        try {
            $response = $http->get('https://free.currconv.com/api/v7/convert?q=' . $full_key . '&apiKey=a3ddc833bd6c29c5555f');
            $rate = json_decode((string)$response->getBody(), true)['results'][$full_key]['val'];
            session()->put('rate', $rate);
            session()->put('currency', $key);
        } catch (BadResponseException $e) {
            return response()->json('Something went wrong on the server.', $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @return Application|Factory|RedirectResponse|View
     */
    public function orderCreate(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'total_price' => 'required',
            'quantity' => 'required',
            'currency' => 'required',
            'attribute_value_id' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (session()->has('order_items')) {
            session()->forget('order_items');
            session()->regenerate();
        }
        session()->put('order_items', [
            'product_id' => $request->product_id,
            'total_price' => $request->total_price,
            'quantity' => $request->quantity,
            'currency' => $request->currency,
            'attribute_value_id' => $request->attribute_value_id,
        ]);

        return redirect()->route('order_create_form');
    }

    /**
     * @return Application|Factory|View
     */
    public function showOrderCreateForm()
    {
        if (session()->has('order_items')) {
            $product_id = session()->get('order_items')['product_id'];
            $product = Product::findOrFail($product_id);
            return view('order_create', compact('product'));
        }
        abort(404);
    }

    /**
     * @param Request $request
     * @return Application|Factory|RedirectResponse|View
     */
    public function orderStore(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'product_id' => 'required',
            'total_price' => 'required',
            'quantity' => 'required',
            'currency' => 'required',
            'attribute_name' => 'required',
            'attribute_value' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $order = Order::create([
            'number' => time(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'total_price' => $request->total_price,
            'quantity' => $request->quantity,
            'currency' => $request->currency,
            'attribute_name' => $request->attribute_name,
            'attribute_value' => $request->attribute_value,
        ]);
        $order->products()->create([
            'product_id' => $request->product_id,
        ]);

        $product = Product::findOrFail($request->product_id);

        $old_stock = $product->stocks->last()->quantity;

        $product->stocks()->create([
            'quantity' => $old_stock- ($order->quantity)
        ]);

        session()->forget('order_items');
        session()->regenerate();
        return view('thankyou');
    }
}
