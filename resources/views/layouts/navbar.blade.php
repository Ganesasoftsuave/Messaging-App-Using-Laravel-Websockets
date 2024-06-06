<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-5">
    <div class="container">
        @auth
            <a class="navbar-brand" href="#">Hello {{ Auth::user()->name }}</a>
        @endauth
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register.user') }}">Register</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false" style="position: relative;"
                            onclick="updateNotificationCount()">
                            <img src="{{ asset('assets/image.png') }}" alt=""
                                style="max-height: 40px; max-width: 40px;">
                            <span class="badge bg-danger" id="notification-count"
                                style="position: absolute; top: -5px; right: -5px;">{{ $notificationCount > 0 ? $notificationCount : '' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown"
                            id="notification-list">
                            <li class="dropdown-header">Messages</li>
                            <li class="dropdown-item">No new notifications</li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('signout') }}">Logout</a>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>