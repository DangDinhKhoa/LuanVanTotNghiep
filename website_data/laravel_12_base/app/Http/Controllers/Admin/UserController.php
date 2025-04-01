<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        @session_start();
    }

    public function index(){
        $users = User::all();
        $template = 'admin.user.index';
        return view('admin.layout', compact(
            'template',
            'users'
        ));
    }

    public function changeStatus(Request $request, $id)
    {
        $user = User::findOrFail($id); // Tìm người dùng theo ID
        $user->status = $user->status == 1 ? 0 : 1; // Chuyển từ 1 sang 0 hoặc ngược lại
        $user->save(); // Lưu thay đổi
        return redirect('/admin/layout')->with('success', 'Cập nhật trạng thái thành công!');
    }
}