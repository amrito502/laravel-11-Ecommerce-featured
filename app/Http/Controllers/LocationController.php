<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    // public function getLocation(Request $request)
    // {
    //     // ব্যবহারকারীর আইপি অ্যাড্রেস সংগ্রহ করুন
    //     $ipAddress = $request->ip();

    //     // ipinfo.io API ব্যবহার করে লোকেশন ডেটা সংগ্রহ করুন
    //     $response = Http::get("https://ipinfo.io/{$ipAddress}?token=cdbf05c6ad9c84");
    //     $locationData = $response->json();

    //     // লোকেশন ডেটা লগ করুন (ডিবাগিং এর জন্য)
    //     Log::info('Location Data:', $locationData);

    //     // 'loc' কীটি আছে কিনা চেক করুন
    //     if (isset($locationData['loc'])) {
    //         return [
    //             'latitude' => explode(',', $locationData['loc'])[0],
    //             'longitude' => explode(',', $locationData['loc'])[1],
    //         ];
    //     } else {
    //         // ডিফল্ট লোকেশন রিটার্ন করুন (যদি 'loc' কীটি না থাকে)
    //         return [
    //             'latitude' => '23.8103', // ঢাকার ডিফল্ট অক্ষাংশ
    //             'longitude' => '90.4125', // ঢাকার ডিফল্ট দ্রাঘিমাংশ
    //         ];
    //     }
    // }

    public function getLocation(Request $request)
    {
        // ব্যবহারকারীর আইপি অ্যাড্রেস সংগ্রহ করুন
        $ipAddress = $request->ip();

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
