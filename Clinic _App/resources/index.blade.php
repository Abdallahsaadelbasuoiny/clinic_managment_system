@foreach ($products as $product)
    <div>
        <div>{{ $product->ProductName }}</div>
        <div>{{ $product->UnitPrice }}</div>
        <div>{{ $product->UnitsInStock }}</div>
        <hr>
    </div>
    @endforeach
