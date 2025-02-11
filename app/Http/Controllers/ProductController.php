<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function showNearbyProducts(Request $request)
    {
        // ব্যবহারকারীর লোকেশন সংগ্রহ করুন
        $locationController = new LocationController();
        $userLocation = $locationController->getLocation($request);

        // সব পণ্য সংগ্রহ করুন
        $products = Product::all();

        // কাছাকাছি পণ্য ফিল্টার করুন
        $nearbyProducts = [];
        foreach ($products as $product) {
            $distance = $this->calculateDistance(
                $userLocation['latitude'],
                $userLocation['longitude'],
                $product->latitude,
                $product->longitude
            );

            // যদি দূরত্ব ১০ কিলোমিটারের মধ্যে হয়
            if ($distance <= 10) {
                $nearbyProducts[] = $product;
            }
        }

        return view('nearby-products', compact('nearbyProducts'));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // কিলোমিটারে

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)  // ✅ Closing parenthesis added
        ));

        return $angle * $earthRadius;
    }



    // পণ্য তৈরি করার ফর্ম দেখান
    public function create()
    {
        $users = User::all(); // সব ব্যবহারকারী পান
        return view( 'products.create', compact('users'));
    }


      // পণ্য ডেটা সংরক্ষণ করুন
      public function store(Request $request)
      {
          // ডেটা ভ্যালিডেশন
          $request->validate([
              'name' => 'required|string|max:255',
              'latitude' => 'nullable|string',
              'longitude' => 'nullable|string',
              'user_id' => 'required|exists:users,id',
          ]);

          // পণ্য তৈরি করুন
          $product = Product::create([
              'name' => $request->name,
              'latitude' => $request->latitude,
              'longitude' => $request->longitude,
              'user_id' => $request->user_id,
          ]);

          return redirect()->route('products.create')->with('success', 'Product created successfully!');
      }
}
