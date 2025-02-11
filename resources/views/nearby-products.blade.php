<!DOCTYPE html>
<html>
<head>
    <title>Nearby Products</title>
</head>
<body>
    <h1>Nearby Products</h1>
    <ul>
        @foreach($nearbyProducts as $product)
            <li>{{ $product->name }}</li>
        @endforeach
    </ul>
</body>
</html>
