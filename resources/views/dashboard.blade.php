@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- Dashboard Module --}}
    <div id="dashboard-module" class="module active">
        <div class="module-header">
            <h1>Dashboard</h1>
            <p>Welcome back, <span id="welcomeUser">Admin</span></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card revenue">
                <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
                <div class="stat-content">
                    <h3>Total Revenue</h3>
                    <p class="stat-value" id="totalRevenue">₱0</p>
                    <span class="stat-change positive">+12.5% from last month</span>
                </div>
            </div>
            <div class="stat-card expense">
                <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
                <div class="stat-content">
                    <h3>Total Expenses</h3>
                    <p class="stat-value" id="totalExpenses">₱0</p>
                    <span class="stat-change neutral">+8.3% from last month</span>
                </div>
            </div>
            <div class="stat-card profit">
                <div class="stat-icon"><i class="fas fa-peso-sign"></i></div>
                <div class="stat-content">
                    <h3>Net Income</h3>
                    <p class="stat-value" id="netIncome">₱0</p>
                    <span class="stat-change positive">+15.2% from last month</span>
                </div>
            </div>
            <div class="stat-card pending">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-content">
                    <h3>Pending Requests</h3>
                    <p class="stat-value" id="pendingRequests">0</p>
                    <span class="stat-change neutral">Awaiting approval</span>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Recent Transactions</h3>
                <div id="recentTransactions" class="transaction-list"></div>
            </div>
            <div class="dashboard-card">
                <h3>Alerts & Notifications</h3>
                <div id="alertsList" class="alerts-list"></div>
            </div>
        </div>
    </div>
@endsection