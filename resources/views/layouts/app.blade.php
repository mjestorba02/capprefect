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

    <style>
        /* Rotate the arrow when the submenu is shown */
        .menu-module .collapse.show + .submenu-icon,
        .menu-module .collapse.show ~ .submenu-icon {
            transform: rotate(180deg);
            transition: transform 0.3s ease;
        }

        /* Make submenu links look nice */
        .submenu-item {
            display: block;
            padding: 6px 0;
            color: #495057;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .submenu-item.active {
            font-weight: 600;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    {{-- Main Application --}}
    <div id="mainApp" class="app-container">
        {{-- Sidebar --}}
        <nav class="sidebar">
            <div class="sidebar-header text-center p-3">
                <img src="{{ asset('images/logo.jpg') }}" alt="Prefect Logo" class="mx-auto mb-2" style="width: 60px; height: 60px; border-radius: 50%;">
                <h2 class="m-0 fw-bold">Prefect</h2>
                <p class="text-muted small">Disciplinary Action System</p>
            </div>
            <ul class="sidebar-menu list-unstyled">

                <!-- Sanction Management Module -->
                <li class="menu-module">
                    <a class="menu-item d-flex justify-content-between align-items-center" 
                    data-bs-toggle="collapse" 
                    href="#sanctionModule" 
                    role="button" 
                    aria-expanded="false" 
                    aria-controls="sanctionModule">
                        <span><i class="fas fa-gavel me-2"></i> Sanction Management</span>
                        <i class="fas fa-chevron-down submenu-icon"></i>
                    </a>
                    <ul class="collapse list-unstyled ps-4" id="sanctionModule">
                        <li>
                            <a href="{{ route('sanctions.index') }}" class="submenu-item {{ request()->routeIs('sanctions*') ? 'active' : '' }}">
                                Sanctions
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('violation_categories.index') }}" class="submenu-item {{ request()->routeIs('violation_categories*') ? 'active' : '' }}">
                                Violation Category Setup
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('incident_reports.index') }}" class="submenu-item {{ request()->routeIs('incident_reports*') ? 'active' : '' }}">
                                Incident Report Generation
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Behavior Monitoring Module -->
                <li class="menu-module">
                    <a class="menu-item d-flex justify-content-between align-items-center" 
                    data-bs-toggle="collapse" 
                    href="#behaviorModule" 
                    role="button" 
                    aria-expanded="false" 
                    aria-controls="behaviorModule">
                        <span><i class="fas fa-eye me-2"></i> Behavior Monitoring</span>
                        <i class="fas fa-chevron-down submenu-icon"></i>
                    </a>
                    <ul class="collapse list-unstyled ps-4" id="behaviorModule">
                        <li>
                            <a href="{{ route('behaviors.index') }}" class="submenu-item {{ request()->routeIs('behaviors*') ? 'active' : '' }}">
                                Behavior Points Monitoring
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reformation_programs.index') }}" class="submenu-item {{ request()->routeIs('reformation_programs*') ? 'active' : '' }}">
                                Reformation Program Assignment
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Other Modules (no dropdown) -->
                <li>
                    <a href="{{ route('hearings.index') }}" class="menu-item {{ request()->routeIs('hearings*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt me-2"></i> Disciplinary Hearing Schedule
                    </a>
                </li>
                <li>
                    <a href="{{ route('infractions.index') }}" class="menu-item {{ request()->routeIs('infractions*') ? 'active' : '' }}">
                        <i class="fas fa-exclamation-triangle me-2"></i> Infraction Logging
                    </a>
                </li>
                <li>
                    <a href="{{ route('clearance_holds.index') }}" class="menu-item {{ request()->routeIs('clearance_holds*') ? 'active' : '' }}">
                        <i class="fas fa-ban me-2"></i> Clearance Hold Flagging
                    </a>
                </li>
                <li>
                    <a href="{{ route('notifications.parents') }}" class="menu-item {{ request()->routeIs('notifications*') ? 'active' : '' }}">
                        <i class="fas fa-envelope-open-text me-2"></i> Parent Notification Tool
                    </a>
                </li>
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