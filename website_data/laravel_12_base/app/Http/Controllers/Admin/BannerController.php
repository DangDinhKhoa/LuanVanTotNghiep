<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BannerController extends Controller
{
    public function __construct()
    {
        @session_start();
    }

    public function index()
    {
        // Danh sách ảnh (có thể thay bằng dữ liệu từ database sau)
        $images = [
            ['url' => asset('assets\images\banners\Flux_Dev_An_artistic_representation_of_smart_devices_featuring_0.jpeg'), 'alt' => 'Ảnh 1'],
            ['url' => asset('assets\images\banners\Flux_Dev_An_artistic_representation_of_smart_devices_featuring_1.jpeg'), 'alt' => 'Ảnh 2'],
            ['url' => asset('assets\images\banners\Flux_Dev_An_artistic_representation_of_smart_devices_featuring_2.jpeg'), 'alt' => 'Ảnh 3'],
            ['url' => asset('assets\images\banners\Flux_Dev_An_artistic_representation_of_smart_devices_featuring_3.jpeg'), 'alt' => 'Ảnh 4'],
        ];
        $template = 'admin.banner.index';
        return view('admin.layout', compact(
            'template',
            'images'
        ));
    }
}