<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function __construct()
    {
        @session_start();
    }

    public function index()
    {
        // Danh sách ảnh (có thể thay bằng dữ liệu từ database sau)
        $images = [
            ['url' => asset('assets\images\banners\Flux_Dev_An_artistic_representation_of_smart_devices_featuring_0.jpeg'), 'alt' => 'Banner 1'],
            ['url' => asset('assets\images\banners\Flux_Dev_An_artistic_representation_of_smart_devices_featuring_1.jpeg'), 'alt' => 'Banner 2'],
            ['url' => asset('assets\images\banners\Flux_Dev_An_artistic_representation_of_smart_devices_featuring_2.jpeg'), 'alt' => 'Banner 3'],
            ['url' => asset('assets\images\banners\Flux_Dev_An_artistic_representation_of_smart_devices_featuring_3.jpeg'), 'alt' => 'Banner 4'],
        ];
        $template = 'user.body.banner';
        return view('user.layout', compact(
            'template',
            'images'
        ));
    }

    public function loadBanner()
    {
        // Danh sách ảnh (có thể thay bằng dữ liệu từ database sau)
        $images = [
            ['url' => asset('assets\images\banners\Flux_Dev_An_artistic_representation_of_smart_devices_featuring_0.jpeg'), 'alt' => 'Banner 1'],
            ['url' => asset('assets\images\banners\Flux_Dev_An_artistic_representation_of_smart_devices_featuring_1.jpeg'), 'alt' => 'Banner 2'],
            ['url' => asset('assets\images\banners\Flux_Dev_An_artistic_representation_of_smart_devices_featuring_2.jpeg'), 'alt' => 'Banner 3'],
            ['url' => asset('assets\images\banners\Flux_Dev_An_artistic_representation_of_smart_devices_featuring_3.jpeg'), 'alt' => 'Banner 4'],
        ];
        $template = 'user.body.banner';
        return view('user.layout', compact(
            'template',
            'images'
        ));
    }
}
