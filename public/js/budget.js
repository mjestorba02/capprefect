// Budget Management
class BudgetManager {
    constructor() {
        this.budgets = [];
        this.editingBudget = null;
    }

    loadBudgets() {
        this.budgets = db.select('budgets');
        this.displayBudgets();
    }

    displayBudgets() {
        const tbody = document.querySelector('#budgetTable tbody');
        
        if (this.budgets.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="empty-state">
                            <i class="fas fa-calculator"></i>
                            <h3>No budgets found</h3>
                            <p>Create your first budget to get started</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = this.budgets.map(budget => {
            const utilization = budget.total_amount > 0 ? (budget.spent_amount / budget.total_amount * 100) : 0;
            const utilizationClass = utilization >= 90 ? 'danger' : utilization >= 70 ? 'warning' : 'success';
            
            return `
                <tr>
                    <td>
                        <div>
                            <strong>${budget.name}</strong>
                            <br><small class="text-muted">${budget.description}</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <span class="text-capitalize">${budget.budget_period}</span>
                            <br><small class="text-muted">${Utils.formatDate(budget.start_date)} - ${Utils.formatDate(budget.end_date)}</small>
                        </div>
                    </td>
                    <td>${Utils.formatCurrency(budget.total_amount)}</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${Math.min(utilization, 100)}%"></div>
                        </div>
                        <div class="progress-text">${utilization.toFixed(1)}% utilized</div>
                    </td>
                    <td>
                        <span class="status-badge status-${budget.status}">${budget.status}</span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn edit" onclick="budgetManager.editBudget(${budget.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete" onclick="budgetManager.deleteBudget(${budget.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    openBudgetModal(budget = null) {
        this.editingBudget = budget;
        const isEdit = budget !== null;
        
        const modalContent = `
            <div class="modal-header">
                <h2>${isEdit ? 'Edit Budget' : 'Create New Budget'}</h2>
            </div>
            <div class="modal-body">
                <form id="budgetForm">
                    <div class="form-group">
                        <label for="budgetName">Budget Name</label>
                        <input type="text" id="budgetName" name="name" value="${budget?.name || ''}" required>
                    </div>
                    <div class="form-group">
                        <label for="budgetDescription">Description</label>
                        <textarea id="budgetDescription" name="description" rows="3">${budget?.description || ''}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="budgetAmount">Total Amount</label>
                        <input type="number" id="budgetAmount" name="total_amount" step="0.01" value="${budget?.total_amount || ''}" required>
                    </div>
                    <div class="form-group">
                        <label for="budgetPeriod">Budget Period</label>
                        <select id="budgetPeriod" name="budget_period" required>
                            <option value="monthly" ${budget?.budget_period === 'monthly' ? 'selected' : ''}>Monthly</option>
                            <option value="quarterly" ${budget?.budget_period === 'quarterly' ? 'selected' : ''}>Quarterly</option>
                            <option value="yearly" ${budget?.budget_period === 'yearly' ? 'selected' : ''}>Yearly</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="budgetStartDate">Start Date</label>
                            <input type="date" id="budgetStartDate" name="start_date" value="${budget?.start_date || ''}" required>
                        </div>
                        <div class="form-group">
                            <label for="budgetEndDate">End Date</label>
                            <input type="date" id="budgetEndDate" name="end_date" value="${budget?.end_date || ''}" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="modalManager.closeModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="budgetManager.saveBudget()">${isEdit ? 'Update' : 'Create'}</button>
            </div>
        `;
        
        modalManager.openModal(modalContent);
    }

    saveBudget() {
        const form = document.getElementById('budgetForm');
        if (!Utils.validateForm(form)) {
            Utils.showNotification('Please fill in all required fields', 'error');
            return;
        }

        const formData = new FormData(form);
        const budgetData = {
            name: formData.get('name'),
            description: formData.get('description'),
            total_amount: parseFloat(formData.get('total_amount')),
            budget_period: formData.get('budget_period'),
            start_date: formData.get('start_date'),
            end_date: formData.get('end_date'),
            status: 'active',
            created_by: auth.getCurrentUser().id
        };

        if (this.editingBudget) {
            db.update('budgets', this.editingBudget.id, budgetData);
            Utils.showNotification('Budget updated successfully', 'success');
        } else {
            budgetData.allocated_amount = 0;
            budgetData.spent_amount = 0;
            db.insert('budgets', budgetData);
            Utils.showNotification('Budget created successfully', 'success');
        }

        modalManager.closeModal();
        this.loadBudgets();
        dashboardManager.loadAnalytics();
    }

    editBudget(id) {
        const budget = this.budgets.find(b => b.id === id);
        if (budget) {
            this.openBudgetModal(budget);
        }
    }

    deleteBudget(id) {
        Utils.confirmAction('Are you sure you want to delete this budget?', () => {
            db.delete('budgets', id);
            Utils.showNotification('Budget deleted successfully', 'success');
            this.loadBudgets();
            dashboardManager.loadAnalytics();
        });
    }
}

// Global function for opening budget modal
function openBudgetModal() {
    budgetManager.openBudgetModal();
}

// Initialize budget manager
const budgetManager = new BudgetManager();