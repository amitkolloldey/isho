<?php

namespace App\Http\Controllers;

use App\Stock;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     */
    public function index()
    {
        return view('admin.pages.stocks');
    }

    public function stocks()
    {
        $stocks = Stock::with('product')->orderBy('created_at', 'desc')
            ->get();
        return response([
            'stocks' => $stocks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        return view('admin.pages.stock_create');
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
            'quantity' => 'required',
            'sku' => 'required|exists:products'
        ]);
        if ($validator->fails()) {
            return response([
                'validation' => $validator->errors()
            ]);
        }

        // Creating The Stock
        $stock = Stock::create([
            'quantity' => $request->quantity,
            'product_id' => get_product_by_sku($request->sku)
        ]);

        return response([
            'success' => 'Successfully Added'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|Response|View
     */
    public function edit($id)
    {
        $stock = Stock::findOrFail($id);
        return view('admin.pages.stock_edit', compact('stock'));
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
        $stock = Stock::findOrFail($id);

        // Validation
        $validator = Validator::make($request->all(), [
            'quantity' => 'required',
            'sku' => 'required|exists:products'
        ]);
        if ($validator->fails()) {
            return response([
                'validation' => $validator->errors()
            ]);
        }
        // Updating The Stock
        $stock->update([
            'quantity' => $request->quantity,
        ]);
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
        // Getting The Stock
        $stock = Stock::findOrFail($id);

        $stock->delete();

        return response([
            'success' => "Record Deleted Successfully!"
        ]);
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function search(Request $request)
    {
        if ( session()->has('stocks')){
             session()->forget('stocks');
             session()->save();
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'sku' => 'required|exists:products'
        ]);
        if ($validator->fails()) {
            return response([
                'validation' => $validator->errors()
            ]);
        }
        $date = Carbon::parse($request->date)->format('Y-m-d 00:00:00');

        $stocks = Stock::with('product')
            ->where('product_id', get_product_by_sku($request->sku))
            ->whereDate('updated_at', '=', $date)
            ->get();

         session()->put('stocks', $stocks);
         session()->save();

        return response([
            'stocks' => $stocks,
        ]);
    }

    public function stockDownload(Request $request)
    {
        $pdf = PDF::loadView('admin.pages.stock_download'
        );
        return $pdf->download('invoice.pdf');
    }
}
