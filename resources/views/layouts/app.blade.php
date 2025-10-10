<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prefect - @yield('title', 'Dashboard')</title>

    {{-- CSS Styles --}}
    <link rel="stylesheet" href="{{ asset('styles/main.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/modals.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    {{-- Main Application --}}
    <div id="mainApp" class="app-container">
        {{-- Sidebar --}}
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>Prefect</h2>
                <p>Student Management System</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="{{ route('dashboard') }}" class="menu-item"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li>
                    <a href="{{ route('sanctions.index') }}" 
                    class="menu-item {{ request()->routeIs('sanctions*') ? 'active' : '' }}">
                    <i class="fas fa-gavel"></i> Sanction Management
                    </a>
                </li>
                <li>
                    <a href="{{ route('violation_categories.index') }}" 
                    class="menu-item {{ request()->routeIs('violation_categories*') ? 'active' : '' }}">
                        <i class="fas fa-layer-group"></i> Violation Category Setup
                    </a>
                </li>
                <li>
                    <a href="{{ route('incident_reports.index') }}" 
                    class="menu-item {{ request()->routeIs('incident_reports*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> Incident Reports
                    </a>
                </li>
                <li>
                    <a href="{{ route('behaviors.index') }}" 
                    class="menu-item {{ request()->routeIs('behaviors*') ? 'active' : '' }}">
                    <i class="fas fa-eye"></i> Behavior Monitoring
                    </a>
                </li>
                <li>
                    <a href="{{ route('reformation_programs.index') }}" 
                    class="menu-item {{ request()->routeIs('reformation_programs*') ? 'active' : '' }}">
                        <i class="fas fa-hands-helping"></i> Reformation Programs
                    </a>
                </li>
                <li><a href="#" class="menu-item"><i class="fas fa-calendar-alt"></i> Disciplinary Hearing Schedule</a></li>
                <li>
                    <a href="{{ route('infractions.index') }}" 
                    class="menu-item {{ request()->routeIs('infractions*') ? 'active' : '' }}">
                        <i class="fas fa-exclamation-triangle"></i> Infraction Logging
                    </a>
                </li>
                <li>
                    <a href="{{ route('clearance_holds.index') }}" 
                    class="menu-item {{ request()->routeIs('clearance_holds*') ? 'active' : '' }}">
                        <i class="fas fa-ban"></i> Clearance Hold Flagging
                    </a>
                </li>
                <li><a href="#" class="menu-item"><i class="fas fa-envelope-open-text"></i> Parent Notification Tool</a></li>
            </ul>

            <div class="sidebar-footer">
                <div class="user-info">
                    <i class="fas fa-user"></i>
                    <div>
                        <p>{{ Auth::user()->username }}</p>
                        <p>{{ Auth::user()->role }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </nav>

        {{-- Main Content --}}
        <main class="main-content">
            <div id="content-area">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Modals --}}
    <div id="modalOverlay" class="modal-overlay">
        <div id="modalContent" class="modal-content"></div>
    </div>

    {{-- JS Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/database.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/budget.js') }}"></script>
    <script src="{{ asset('js/revenue.js') }}"></script>
    <script src="{{ asset('js/expenses.js') }}"></script>
    <script src="{{ asset('js/payables.js') }}"></script>
    <script src="{{ asset('js/receivables.js') }}"></script>
    <script src="{{ asset('js/funds.js') }}"></script>
    <script src="{{ asset('js/requests.js') }}"></script>
    <script src="{{ asset('js/reports.js') }}"></script>
    <script src="{{ asset('js/modals.js') }}"></script>
</body>
</html>