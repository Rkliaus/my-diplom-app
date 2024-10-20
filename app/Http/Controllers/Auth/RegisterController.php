<?php

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\User;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Validator;
// class RegisterController extends Controller
// {
//     public function register(Request $request)
//     {
//         // Валидация запроса
//         $validator = Validator::make($request->all(), [
//             'name' => 'required|string|max:255',
//             'email' => 'required|string|email|max:255|unique:users',
//             'password' => 'required|string|min:8|confirmed',
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'errors' => $validator->errors()
//             ], 422);
//         }

//         // Создание пользователя
//         $user = User::create([
//             'name' => $request->name,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//         ]);

//         // Аутентификация пользователя
//         auth()->login($user);

//         return response()->json(['message' => 'Регистрация успешна'], 201);
//     }
// }
