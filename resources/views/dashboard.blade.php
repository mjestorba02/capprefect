@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div id="dashboard-module" class="module active">
    <div class="module-header">
        <h1>Disciplinary Dashboard</h1>
        <p>Welcome back, <span id="welcomeUser">Admin</span></p>
    </div>

    <!-- Top Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-content">
                <h3>Total Students</h3>
                <p class="stat-value">{{ $totalStudents }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-gavel"></i></div>
            <div class="stat-content">
                <h3>Active Sanctions</h3>
                <p class="stat-value">{{ $activeSanctions }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-balance-scale"></i></div>
            <div class="stat-content">
                <h3>Pending Hearings</h3>
                <p class="stat-value">{{ $pendingHearings }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-content">
                <h3>Unresolved Infractions</h3>
                <p class="stat-value">{{ $unresolvedInfractions }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
            <div class="stat-content">
                <h3>Behavior Reports</h3>
                <p class="stat-value">{{ $behaviorReports }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-user-lock"></i></div>
            <div class="stat-content">
                <h3>Students on Hold</h3>
                <p class="stat-value">{{ $clearanceHolds }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-hands-helping"></i></div>
            <div class="stat-content">
                <h3>Active Programs</h3>
                <p class="stat-value">{{ $activePrograms }}</p>
            </div>
        </div>
    </div>

    <!-- Activity & Alerts -->
    <div class="dashboard-grid mt-6">
        <!-- Recent Activities -->
        <div class="dashboard-card">
            <h3>Recent Activities</h3>
            <ul class="transaction-list">
                @forelse($recentActivity as $activity)
                    <li class="border-b py-2">
                        <strong>{{ $activity['type'] }}</strong> - 
                        {{ $activity['desc'] }}
                        <br>
                        <small class="text-gray-500">{{ \Carbon\Carbon::parse($activity['date'])->format('M d, Y') }}</small>
                    </li>
                @empty
                    <li>No recent activity found.</li>
                @endforelse
            </ul>
        </div>

        <!-- Alerts & Notifications -->
        <div class="dashboard-card">
            <h3>Alerts & Notifications</h3>
            <div class="alerts-list">
                @if($pendingHearings > 0)
                    <div class="alert alert-warning flex items-center space-x-2">
                        <i class="fas fa-balance-scale text-yellow-500"></i>
                        <span>{{ $pendingHearings }} hearing(s) pending.</span>
                    </div>
                @endif

                @if($unresolvedInfractions > 0)
                    <div class="alert alert-danger flex items-center space-x-2">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        <span>{{ $unresolvedInfractions }} unresolved infraction(s).</span>
                    </div>
                @endif

                @if($clearanceHolds > 0)
                    <div class="alert alert-info flex items-center space-x-2">
                        <i class="fas fa-user-lock text-blue-500"></i>
                        <span>{{ $clearanceHolds }} student(s) on clearance hold.</span>
                    </div>
                @endif

                @if($activePrograms == 0)
                    <div class="alert alert-neutral flex items-center space-x-2">
                        <i class="fas fa-dove text-gray-500"></i>
                        <span>No active reformation programs at the moment.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .stat-icon {
        font-size: 1.8rem;
        color: #4f46e5;
        background: #eef2ff;
        padding: 0.8rem;
        border-radius: 10px;
    }
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .dashboard-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .alert {
        @apply rounded-lg p-3 mb-2 flex items-center text-sm font-medium shadow-sm;
    }
    .alert-warning {
        @apply bg-yellow-100 text-yellow-800 border border-yellow-300;
    }
    .alert-danger {
        @apply bg-red-100 text-red-800 border border-red-300;
    }
    .alert-info {
        @apply bg-blue-100 text-blue-800 border border-blue-300;
    }
    .alert-neutral {
        @apply bg-gray-100 text-gray-700 border border-gray-300;
    }
</style>

<!-- Font Awesome (if not yet included globally) -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection