<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <div class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <button class="btn btn-primary">{{ Auth::user()->username }}</button>
                <a class="nav-link" href="{{ route('info_admin_get', Auth::user()->id) }}" id="drop2"  aria-expanded="false">
                    <img src="../assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                </a>
            </div>
        </div>
    </nav>
</header>