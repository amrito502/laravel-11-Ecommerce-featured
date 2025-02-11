<!DOCTYPE html>
<html>
<head>
    <title>Search Products by Distance</title>
</head>
<body>
    <h1>Search Products by Distance</h1>
    <form action="{{ route('products.search') }}" method="GET">
        <label for="distance">Select Distance (in km):</label>
        <select name="distance" id="distance" required>
            <option value="10">10 km</option>
            <option value="20">20 km</option>
            <option value="50">50 km</option>
            <option value="100">100 km</option>
            <option value="300">300 km</option>
            <option value="500">500 km</option>
            <option value="500000">500000 km</option>
            <option value="5000000">5000000 km</option>
            <option value="500000000">500000000 km</option>
        </select>
        <br>
        <button type="submit">Search</button>
    </form>

    @if (isset($nearbyProducts))
        <h2>Nearby Products (Within {{ $distance }} km):</h2>
        <ul>
            @foreach($nearbyProducts as $product)
                <li>
                    <strong>{{ $product->name }}</strong><br>
                    Latitude: {{ $product->latitude }}, Longitude: {{ $product->longitude }}
                </li>
            @endforeach
        </ul>
    @endif
</body>
</html>
