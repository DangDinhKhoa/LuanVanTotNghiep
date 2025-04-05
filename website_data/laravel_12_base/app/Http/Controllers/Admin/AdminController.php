<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        @session_start();
    }

    public function index()
    {
        // Lọc người dùng có level = 0
        $users = User::where('level', 1)->get();
        $template = 'admin.admin.index';
        return view('admin.layout', compact(
            'template',
            'users'
        ));
    }

    public function getInfo(Request $request, $id)
    {
        $user = User::findOrFail($id); // Tìm người dùng theo ID
        $template = 'admin.admin.info';
        return view('admin.layout', compact(
            'template',
            'user',
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

    public function changeStatus(Request $request, $id)
    {
        $user = User::findOrFail($id); // Tìm người dùng theo ID
        $user->status = $user->status == 1 ? 0 : 1; // Chuyển từ 1 sang 0 hoặc ngược lại
        $user->save(); // Lưu thay đổi
        return redirect()->route('admin_table_get')->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function showAddAdminForm()
    {
        $template = 'admin.admin.add';
        return view('admin.layout', compact(
            'template',
        ));
    }

    public function addAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'phone_number' => 'required|string|regex:/^[0-9]{10,11}$/',
            'address' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:255|confirmed',
        ]);
        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'level' => 1
        ]);
        return redirect()->route('add_admin_get')->with('success', 'Cập nhật thông tin thành công!');
    }
}
