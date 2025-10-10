// Dashboard Management
class DashboardManager {
    constructor() {
        this.analytics = {};
        this.recentTransactions = [];
    }

    loadDashboard() {
        this.loadAnalytics();
        this.loadRecentTransactions();
        this.loadAlerts();
    }

    loadAnalytics() {
        this.analytics = db.getDashboardAnalytics();
        this.updateAnalyticsDisplay();
    }

    updateAnalyticsDisplay() {
        document.getElementById('totalRevenue').textContent = Utils.formatCurrency(this.analytics.totalRevenue);
        document.getElementById('totalExpenses').textContent = Utils.formatCurrency(this.analytics.totalExpenses);
        document.getElementById('netIncome').textContent = Utils.formatCurrency(this.analytics.netIncome);
        document.getElementById('pendingRequests').textContent = this.analytics.pendingRequests;
    }

    loadRecentTransactions() {
        this.recentTransactions = db.getRecentTransactions();
        this.displayRecentTransactions();
    }

    displayRecentTransactions() {
        const container = document.getElementById('recentTransactions');
        
        if (this.recentTransactions.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-receipt"></i>
                    <p>No recent transactions</p>
                </div>
            `;
            return;
        }

        container.innerHTML = this.recentTransactions.map(transaction => `
            <div class="transaction-item">
                <div class="transaction-icon ${transaction.type}">
                    <i class="fas fa-${transaction.type === 'revenue' ? 'arrow-up' : 'arrow-down'}"></i>
                </div>
                <div class="transaction-details">
                    <h4>${transaction.description || transaction.reference_number}</h4>
                    <p>${Utils.formatDate(transaction.created_at)}</p>
                </div>
                <div class="transaction-amount ${transaction.type === 'revenue' ? 'positive' : 'negative'}">
                    ${transaction.type === 'revenue' ? '+' : '-'}${Utils.formatCurrency(transaction.amount)}
                </div>
            </div>
        `).join('');
    }

    loadAlerts() {
        const alerts = this.generateAlerts();
        this.displayAlerts(alerts);
    }

    generateAlerts() {
        const alerts = [];
        
        // Check for overdue receivables
        const receivables = db.select('accounts_receivable');
        const overdueReceivables = receivables.filter(r => {
            const dueDate = new Date(r.due_date);
            const today = new Date();
            return r.status !== 'paid' && dueDate < today;
        });

        if (overdueReceivables.length > 0) {
            alerts.push({
                type: 'error',
                title: 'Overdue Receivables',
                message: `${overdueReceivables.length} student accounts are overdue`
            });
        }

        // Check for pending requests
        if (this.analytics.pendingRequests > 0) {
            alerts.push({
                type: 'warning',
                title: 'Pending Approvals',
                message: `${this.analytics.pendingRequests} financial requests awaiting approval`
            });
        }

        // Check for low fund balances
        const funds = db.select('funds');
        const lowFunds = funds.filter(f => f.balance < 50000);
        
        if (lowFunds.length > 0) {
            alerts.push({
                type: 'warning',
                title: 'Low Fund Balance',
                message: `${lowFunds.length} funds have low balances`
            });
        }

        // Monthly report reminder
        alerts.push({
            type: 'info',
            title: 'Monthly Report',
            message: 'January financial report is ready for review'
        });

        return alerts;
    }

    displayAlerts(alerts) {
        const container = document.getElementById('alertsList');
        
        if (alerts.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-bell"></i>
                    <p>No alerts at this time</p>
                </div>
            `;
            return;
        }

        container.innerHTML = alerts.map(alert => `
            <div class="alert-item ${alert.type}">
                <div class="alert-icon ${alert.type}">
                    <i class="fas fa-${alert.type === 'error' ? 'exclamation-triangle' : alert.type === 'warning' ? 'exclamation-circle' : 'info-circle'}"></i>
                </div>
                <div class="alert-content">
                    <h4>${alert.title}</h4>
                    <p>${alert.message}</p>
                </div>
            </div>
        `).join('');
    }

    refreshDashboard() {
        this.loadDashboard();
        Utils.showNotification('Dashboard refreshed successfully', 'success');
    }
}

// Initialize dashboard manager
const dashboardManager = new DashboardManager();