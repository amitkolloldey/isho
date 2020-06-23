<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     */
    public function index()
    {
        return view('admin.pages.orders');
    }

    /**
     * @return Application|ResponseFactory|Response
     */
    public function orders()
    {
        $orders = Order::orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return response([
            'orders' => $orders,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|Response|View
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.pages.order_show', compact('order'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // Getting The Order
        $order = Order::findOrFail($id);

        $order->delete();

        return response([
            'success' => "Record Deleted Successfully!"
        ]);
    }
}
