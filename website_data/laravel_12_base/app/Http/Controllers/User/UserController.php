<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        @session_start();
    }

    public function index()
    {
        $template = 'admin.user.index';
        return view('user.layout');
    }

    public function getInfo(Request $request, $id)
    {
        $user = User::findOrFail($id); // Tìm người dùng theo ID
        $template = 'admin.info.index';
        return view('admin.layout', compact(
            'template',
            'user'
        ));
    }

    public function updateInfo(Request $request, $id)
    {
        $user = User::findOrFail($id); // Tìm người dùng theo ID
        if ($user->email != $request->email) {
            $request->validate([
                'email' => 'required|string|email|max:255|unique:users',
            ]);
            $user->email = $request->email;
        }
        if (!empty($request->password)) {
            $request->validate([
                'password' => 'required|string|min:8|max:255',
            ]);
            $user->password = Hash::make($request->password);
        }
        $request->validate([
            'username' => 'required|string|max:255',
            'phone_number' => 'nullable|regex:/^[0-9]{10}$/',
            'address' => 'max:255',
        ]);
        $now = date('Y-m-d H:i:s');
        $user->username = $request->username;
        $user->phone_number = $request->phone_number;
        $user->address = $request->address;
        $user->updated_at = $now;
        $user->created_at = $user->create_at;
        $user->save();
        return redirect()->route('info_admin_get', ['id' => $id])->with('success', 'Cập nhật thông tin thành công!');
    }
}
