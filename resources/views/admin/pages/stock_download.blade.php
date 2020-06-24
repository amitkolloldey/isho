<table class="table">
    <thead>
    <tr>
        <th>Product Sku</th>
        <th>Quantity</th>
        <th>Updated On</th>
    </tr>
    </thead>
    <tbody>
    @foreach(session()->get('stocks') as $stock)
    <tr>
        <td>{{get_sku_by_product_id($stock->product_id)}}</td>
        <td>{{$stock->quantity}}</td>
        <td>{{$stock->updated_at}}</td>
    </tr>
    @endforeach
    </tbody>
</table>
