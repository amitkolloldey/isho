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
        @if(isset($stock->product))
            <tr>
                <td>{{ $stock->product->sku}}</td>
                <td>{{$stock->quantity}}</td>
                <td>{{$stock->updated_at}}</td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
