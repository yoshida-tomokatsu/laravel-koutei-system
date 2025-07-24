<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class ApiAuthController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|string',
                'password' => 'required|string'
            ]);
            
            $user = User::where('user_id', $validated['user_id'])->first();
            
            if (!$user || !Hash::check($validated['password'], $user->password_hash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ユーザーIDまたはパスワードが正しくありません'
                ], 401);
            }
            
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'このアカウントは無効化されています'
                ], 401);
            }
            
            // ログイン時間の更新
            $user->update(['last_login' => now()]);
            
            // トークンの生成
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'user_id' => $user->user_id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'permissions' => $user->permissions,
                        'last_login' => $user->last_login
                    ],
                    'token' => $token
                ],
                'message' => 'ログインに成功しました'
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '入力データに問題があります',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ログインに失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Handle a logout request to the application.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user) {
                // 現在のトークンを削除
                $user->currentAccessToken()->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'ログアウトしました'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'ログインしていません'
            ], 401);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ログアウトに失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'ログインしていません'
                ], 401);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'user_id' => $user->user_id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'permissions' => $user->permissions,
                        'last_login' => $user->last_login,
                        'is_admin' => $user->isAdmin(),
                        'is_employee' => $user->isEmployee()
                    ]
                ],
                'message' => 'ユーザー情報を取得しました'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ユーザー情報の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Refresh the user's token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'ログインしていません'
                ], 401);
            }
            
            // 現在のトークンを削除
            $user->currentAccessToken()->delete();
            
            // 新しいトークンを生成
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token
                ],
                'message' => 'トークンを更新しました'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'トークンの更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 