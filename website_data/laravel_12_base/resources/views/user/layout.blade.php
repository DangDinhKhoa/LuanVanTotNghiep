<!doctype html>
<html lang="en">

<head>
    @include('user.components.head')
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar -->
        @include('user.components.sidebar')
        <!--  Main wrapper -->
        <div class="body-wrapper" style="width: 75%; height: 100%;position: fixed;right: 0; overflow-y: auto">
            <!--  Header -->
            @include('user.components.nav')
            <!-- Body -->
            @include($template)
        </div>
    </div>
    @include('user.components.script')
</body>

</html>