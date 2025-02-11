<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function showNearbyProducts(Request $request)
    {
        // ব্যবহারকারীর লোকেশন সংগ্রহ করুন
        $locationController = new LocationController();
        $userLocation       = $locationController->getLocation($request);

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
        $latTo   = deg2rad($lat2);
        $lonTo   = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2) // ✅ Closing parenthesis added
        ));

        return $angle * $earthRadius;
    }

    // পণ্য তৈরি করার ফর্ম দেখান
    public function create()
    {
        $users = User::all(); // সব ব্যবহারকারী পান
        return view('products.create', compact('users'));
    }

    // পণ্য ডেটা সংরক্ষণ করুন
    public function store(Request $request)
    {
        // ডেটা ভ্যালিডেশন
        $request->validate([
            'name'      => 'required|string|max:255',
            'latitude'  => 'nullable|string',
            'longitude' => 'nullable|string',
            'user_id'   => 'required|exists:users,id',
        ]);

        // পণ্য তৈরি করুন
        $product = Product::create([
            'name'      => $request->name,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'user_id'   => $request->user_id,
        ]);

        return redirect()->route('products.create')->with('success', 'Product created successfully!');
    }

    public function showSearchForm()
    {
        return view('products.search-products');
    }

    // পণ্য সার্চ করুন
    public function search(Request $request)
    {
        // ব্যবহারকারীর লোকেশন সংগ্রহ করুন
        $userLocation = $this->getLocation($request);
        $userLat      = $userLocation['latitude'];
        $userLon      = $userLocation['longitude'];

        // ফর্ম থেকে দূরত্ব সংগ্রহ করুন
        $distance = $request->input('distance');

        // সব পণ্য সংগ্রহ করুন
        $products = Product::all();

        // কাছাকাছি পণ্য ফিল্টার করুন
        $nearbyProducts = [];
        foreach ($products as $product) {
            $productDistance = $this->calculateDistance(
                $userLat,
                $userLon,
                $product->latitude,
                $product->longitude
            );

            // যদি পণ্যটি নির্বাচিত দূরত্বের মধ্যে থাকে
            if ($productDistance <= $distance) {
                $nearbyProducts[] = $product;
            }
        }

        return view('products.search-products', [
            'nearbyProducts' => $nearbyProducts,
            'distance'       => $distance,
        ]);
    }

    // //   দূরত্ব হিসাব করার ফাংশন
    //   private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    //   {
    //       $earthRadius = 6371; // পৃথিবীর ব্যাসার্ধ (কিলোমিটারে)

    //       $latFrom = deg2rad($lat1);
    //       $lonFrom = deg2rad($lon1);
    //       $latTo = deg2rad($lat2);
    //       $lonTo = deg2rad($lon2);

    //       $latDelta = $latTo - $latFrom;
    //       $lonDelta = $lonTo - $lonFrom;

    //       $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    //           cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    //       return $angle * $earthRadius;
    //   }

    // ব্যবহারকারীর লোকেশন সংগ্রহ করার ফাংশন
    private function getLocation(Request $request)
    {
        // ব্যবহারকারীর আইপি অ্যাড্রেস সংগ্রহ করুন
        $ipAddress = $request->ip();

        // লোকালহোস্টের জন্য ডিফল্ট লোকেশন সেট করুন
        if ($ipAddress === '127.0.0.1') {
            return [
                'latitude'  => '23.8103', // ঢাকার ডিফল্ট অক্ষাংশ
                'longitude' => '90.4125', // ঢাকার ডিফল্ট দ্রাঘিমাংশ
            ];
        }

        // ip-api.com API ব্যবহার করে লোকেশন ডেটা সংগ্রহ করুন
        $response     = Http::get("http://ip-api.com/json/{$ipAddress}");
        $locationData = $response->json();

        // লোকেশন ডেটা লগ করুন (ডিবাগিং এর জন্য)
        Log::info('Location Data:', $locationData);

        // 'lat' এবং 'lon' কীটি আছে কিনা চেক করুন
        if (isset($locationData['lat']) && isset($locationData['lon'])) {
            return [
                'latitude'  => $locationData['lat'],
                'longitude' => $locationData['lon'],
            ];
        } else {
            // ডিফল্ট লোকেশন রিটার্ন করুন (যদি 'lat' বা 'lon' কীটি না থাকে)
            return [
                'latitude'  => '23.8103', // ঢাকার ডিফল্ট অক্ষাংশ
                'longitude' => '90.4125', // ঢাকার ডিফল্ট দ্রাঘিমাংশ
            ];
        }
    }
}
