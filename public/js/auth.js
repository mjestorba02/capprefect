// Authentication Management
class AuthManager {
    constructor() {
        this.currentUser = null;
        this.checkStoredAuth();
    }

    checkStoredAuth() {
        const storedUser = localStorage.getItem('currentUser');
        if (storedUser) {
            this.currentUser = JSON.parse(storedUser);
            this.showMainApp();
        }
    }

    login(username, password) {
        const user = db.authenticate(username, password);
        if (user) {
            this.currentUser = user;
            localStorage.setItem('currentUser', JSON.stringify(user));
            this.showMainApp();
            return true;
        }
        return false;
    }

    logout() {
        this.currentUser = null;
        localStorage.removeItem('currentUser');
        this.showLoginScreen();
    }

    showLoginScreen() {
        document.getElementById('loginScreen').style.display = 'flex';
        document.getElementById('mainApp').style.display = 'none';
    }

    showMainApp() {
        document.getElementById('loginScreen').style.display = 'none';
        document.getElementById('mainApp').style.display = 'flex';
        
        // Update user info in sidebar
        document.getElementById('currentUsername').textContent = this.currentUser.username;
        document.getElementById('currentRole').textContent = this.currentUser.role;
        document.getElementById('welcomeUser').textContent = this.currentUser.username;
        
        // Load dashboard by default
        moduleManager.showModule('dashboard');
    }

    hasPermission(action) {
        if (!this.currentUser) return false;
        
        const permissions = {
            admin: ['all'],
            finance_manager: ['budget', 'revenue', 'expenses', 'payables', 'receivables', 'funds', 'requests', 'reports', 'approve'],
            accountant: ['revenue', 'expenses', 'payables', 'receivables', 'reports'],
            auditor: ['reports', 'view_only']
        };
        
        const userPermissions = permissions[this.currentUser.role] || [];
        return userPermissions.includes('all') || userPermissions.includes(action);
    }

    getCurrentUser() {
        return this.currentUser;
    }
}

// Initialize auth manager
const auth = new AuthManager();

// Login form handler
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    if (auth.login(username, password)) {
        // Clear form
        document.getElementById('username').value = '';
        document.getElementById('password').value = '';
    } else {
        alert('Invalid username or password');
    }
});

// Logout button handler
document.getElementById('logoutBtn').addEventListener('click', function() {
    if (confirm('Are you sure you want to logout?')) {
        auth.logout();
    }
});