<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
     // ব্যবহারকারী তৈরি করার ফর্ম দেখান
     public function create()
     {
         return view('users.create');
     }

     // ব্যবহারকারী ডেটা সংরক্ষণ করুন
     public function store(Request $request)
     {
         // ডেটা ভ্যালিডেশন
         $request->validate([
             'name' => 'required|string|max:255',
             'email' => 'required|email|unique:users,email',
             'password' => 'required|string|min:8',
             'latitude' => 'nullable|string',
             'longitude' => 'nullable|string',
         ]);

         // ব্যবহারকারী তৈরি করুন
         $user = User::create([
             'name' => $request->name,
             'email' => $request->email,
             'password' => bcrypt($request->password),
             'latitude' => $request->latitude,
             'longitude' => $request->longitude,
         ]);

         return redirect()->route('users.create')->with('success', 'User created successfully!');
     }
}
