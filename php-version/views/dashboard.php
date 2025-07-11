<?php
ob_start();
?>

<div class="container" style="padding: 2rem 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-primary);">
                Dashboard
            </h1>
            <p style="font-size: 1.1rem; color: var(--text-secondary);">
                Welcome back, <?php echo htmlspecialchars($user['name']); ?>! Manage your workshops below.
            </p>
        </div>
        
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="<?php echo Paths::getRelativeUrl('change-password'); ?>" class="btn">
                🔑 Change Password
            </a>
            <button onclick="showCreateModal()" class="btn btn-primary">
                ✨ Create Workshop
            </button>
        </div>
    </div>

    <!-- Workshop Stats -->
    <div class="grid grid-3" style="margin-bottom: 3rem;">
        <div class="card" style="text-align: center;">
            <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-dev-dark); margin: 0;" id="total-workshops">0</h3>
            <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">Total Workshops</p>
        </div>
        <div class="card" style="text-align: center;">
            <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-ux-dark); margin: 0;" id="upcoming-workshops">0</h3>
            <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">Upcoming</p>
        </div>
        <div class="card" style="text-align: center;">
            <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-po-dark); margin: 0;" id="this-month">0</h3>
            <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">This Month</p>
        </div>
    </div>

    <!-- Filters -->
    <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; margin-bottom: 2rem;">
        <div class="form-group" style="margin: 0; min-width: 200px;">
            <select id="subject-filter" class="form-select" onchange="applyFilters()">
                <option value="">All Subjects</option>
                <option value="DEV">Development</option>
                <option value="UX">UX Design</option>
                <option value="PO">Professional Skills</option>
                <option value="RESEARCH">Research</option>
                <option value="PORTFOLIO">Portfolio</option>
                <option value="MISC">Miscellaneous</option>
            </select>
        </div>
        
        <div class="form-group" style="margin: 0; min-width: 200px;">
            <input id="date-filter" type="date" class="form-input" onchange="applyFilters()" />
        </div>
        
        <button onclick="clearFilters()" class="btn">
            Clear Filters
        </button>
        
        <div style="margin-left: auto;">
            <input type="text" id="search-input" placeholder="Search workshops..." class="form-input" style="min-width: 250px;" oninput="applyFilters()">
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading" style="text-align: center; padding: 3rem; display: none;">
        <div class="spinner"></div>
        <p style="color: var(--text-secondary); margin-top: 1rem;">Loading workshops...</p>
    </div>

    <!-- Error State -->
    <div id="error" style="text-align: center; padding: 3rem; display: none;">
        <p style="color: var(--color-portfolio-dark);" id="error-message"></p>
        <button onclick="loadWorkshops()" class="btn btn-primary" style="margin-top: 1rem;">
            Try Again
        </button>
    </div>

    <!-- No Workshops -->
    <div id="no-workshops" style="text-align: center; padding: 3rem; display: none;">
        <h3 style="color: var(--text-secondary); margin-bottom: 1rem;">No workshops found</h3>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">
            Create your first workshop to get started!
        </p>
        <button onclick="showCreateModal()" class="btn btn-primary">
            ✨ Create Your First Workshop
        </button>
    </div>

    <!-- Workshops Grid -->
    <div id="workshops-container" style="display: none;">
        <div id="workshops-grid" class="grid grid-2"></div>
    </div>
</div>

<!-- Create/Edit Workshop Modal -->
<div id="workshop-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: var(--bg-primary); border-radius: 0.75rem; padding: 2rem; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; position: relative;">
        <button onclick="closeModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">×</button>
        
        <h2 id="modal-title" style="margin-bottom: 1.5rem; color: var(--text-primary);">Create Workshop</h2>
        
        <form id="workshop-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" id="workshop-id" name="id">
            
            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-input" required maxlength="255">
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-input" required rows="4" maxlength="1000" style="min-height: 100px; resize: vertical;"></textarea>
            </div>
            
            <div class="form-group">
                <label for="subject" class="form-label">Subject</label>
                <select id="subject" name="subject" class="form-select" required>
                    <option value="">Select a subject</option>
                    <option value="DEV">Development</option>
                    <option value="UX">UX Design</option>
                    <option value="PO">Professional Skills</option>
                    <option value="RESEARCH">Research</option>
                    <option value="PORTFOLIO">Portfolio</option>
                    <option value="MISC">Miscellaneous</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="date" class="form-label">Date</label>
                <input type="date" id="date" name="date" class="form-input" required>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="button" onclick="closeModal()" class="btn">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submit-btn">Create Workshop</button>
            </div>
        </form>
    </div>
</div>

<script>
// Dashboard JavaScript
let allWorkshops = [];
let filteredWorkshops = [];
let currentUser = <?php echo json_encode($user); ?>;
let editingWorkshop = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadWorkshops();
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date').min = today;
});

async function loadWorkshops() {
    showLoading();
    
    try {
        const response = await fetch(`${window.appConfig.apiPath}workshops?teacher_id=${currentUser.id}`);
        const data = await response.json();
        
        if (data.success) {
            allWorkshops = data.data.workshops;
            filteredWorkshops = [...allWorkshops];
            updateStats();
            renderWorkshops();
        } else {
            showError(data.error || 'Failed to load workshops');
        }
    } catch (error) {
        showError('Network error. Please try again.');
    }
}

function updateStats() {
    const total = allWorkshops.length;
    const today = new Date().toISOString().split('T')[0];
    const upcoming = allWorkshops.filter(w => w.date >= today).length;
    
    const thisMonth = allWorkshops.filter(w => {
        const workshopDate = new Date(w.date);
        const now = new Date();
        return workshopDate.getMonth() === now.getMonth() && 
               workshopDate.getFullYear() === now.getFullYear();
    }).length;
    
    document.getElementById('total-workshops').textContent = total;
    document.getElementById('upcoming-workshops').textContent = upcoming;
    document.getElementById('this-month').textContent = thisMonth;
}

function applyFilters() {
    const subject = document.getElementById('subject-filter').value;
    const date = document.getElementById('date-filter').value;
    const search = document.getElementById('search-input').value.toLowerCase();
    
    filteredWorkshops = allWorkshops.filter(workshop => {
        const matchesSubject = !subject || workshop.subject === subject;
        const matchesDate = !date || workshop.date === date;
        const matchesSearch = !search || 
            workshop.title.toLowerCase().includes(search) ||
            workshop.description.toLowerCase().includes(search);
        
        return matchesSubject && matchesDate && matchesSearch;
    });
    
    renderWorkshops();
}

function clearFilters() {
    document.getElementById('subject-filter').value = '';
    document.getElementById('date-filter').value = '';
    document.getElementById('search-input').value = '';
    filteredWorkshops = [...allWorkshops];
    renderWorkshops();
}

function renderWorkshops() {
    hideAllStates();
    
    if (allWorkshops.length === 0) {
        document.getElementById('no-workshops').style.display = 'block';
        return;
    }
    
    if (filteredWorkshops.length === 0) {
        document.getElementById('workshops-container').style.display = 'block';
        document.getElementById('workshops-grid').innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                <p style="color: var(--text-secondary); font-size: 1.1rem;">
                    No workshops match your filters.
                </p>
                <button onclick="clearFilters()" class="btn btn-primary" style="margin-top: 1rem;">
                    Clear Filters
                </button>
            </div>
        `;
        return;
    }
    
    const grid = document.getElementById('workshops-grid');
    grid.innerHTML = '';
    
    filteredWorkshops.forEach(workshop => {
        const card = createWorkshopCard(workshop);
        grid.appendChild(card);
    });
    
    document.getElementById('workshops-container').style.display = 'block';
}

function createWorkshopCard(workshop) {
    const card = document.createElement('div');
    card.className = `card subject-${workshop.subject.toLowerCase()}`;
    
    const subjectColors = {
        'DEV': { bg: 'var(--color-dev-dark)', text: 'white' },
        'UX': { bg: 'var(--color-ux-dark)', text: 'white' },
        'PO': { bg: 'var(--color-po-dark)', text: 'white' },
        'RESEARCH': { bg: 'var(--color-research-dark)', text: 'var(--color-research-text)' },
        'PORTFOLIO': { bg: 'var(--color-portfolio-dark)', text: 'white' },
        'MISC': { bg: 'var(--color-misc-dark)', text: 'var(--color-misc-text)' }
    };
    
    const colors = subjectColors[workshop.subject] || subjectColors['MISC'];
    const isUpcoming = workshop.date >= new Date().toISOString().split('T')[0];
    
    card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
            <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; background-color: ${colors.bg}; color: ${colors.text};">
                ${workshop.subject}
            </span>
            <div style="display: flex; gap: 0.5rem;">
                <button onclick="editWorkshop('${workshop.id}')" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" title="Edit">
                    ✏️
                </button>
                <button onclick="deleteWorkshop('${workshop.id}')" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" title="Delete">
                    🗑️
                </button>
            </div>
        </div>
        
        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-primary);">
            ${workshop.title}
        </h3>
        
        <p style="color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.5;">
            ${workshop.description}
        </p>
        
        <div style="display: flex; justify-content: space-between; align-items: center; color: var(--text-muted); font-size: 0.875rem;">
            <span>📅 ${formatDate(workshop.date)}</span>
            <span style="color: ${isUpcoming ? 'var(--color-po-dark)' : 'var(--text-muted)'}; font-weight: 500;">
                ${isUpcoming ? '🟢 Upcoming' : '⚪ Past'}
            </span>
        </div>
    `;
    
    return card;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function showCreateModal() {
    editingWorkshop = null;
    document.getElementById('modal-title').textContent = 'Create Workshop';
    document.getElementById('submit-btn').textContent = 'Create Workshop';
    document.getElementById('workshop-form').reset();
    document.getElementById('workshop-id').value = '';
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date').min = today;
    
    document.getElementById('workshop-modal').style.display = 'flex';
}

function editWorkshop(id) {
    editingWorkshop = allWorkshops.find(w => w.id === id);
    if (!editingWorkshop) return;
    
    document.getElementById('modal-title').textContent = 'Edit Workshop';
    document.getElementById('submit-btn').textContent = 'Update Workshop';
    
    document.getElementById('workshop-id').value = editingWorkshop.id;
    document.getElementById('title').value = editingWorkshop.title;
    document.getElementById('description').value = editingWorkshop.description;
    document.getElementById('subject').value = editingWorkshop.subject;
    document.getElementById('date').value = editingWorkshop.date;
    
    // Don't restrict past dates when editing
    document.getElementById('date').min = '';
    
    document.getElementById('workshop-modal').style.display = 'flex';
}

async function deleteWorkshop(id) {
    if (!confirm('Are you sure you want to delete this workshop?')) {
        return;
    }
    
    try {
        const response = await fetch(`${window.appConfig.apiPath}workshops/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': window.csrfToken
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            allWorkshops = allWorkshops.filter(w => w.id !== id);
            filteredWorkshops = filteredWorkshops.filter(w => w.id !== id);
            updateStats();
            renderWorkshops();
            alert('Workshop deleted successfully!');
        } else {
            alert(data.error || 'Failed to delete workshop');
        }
    } catch (error) {
        alert('Network error. Please try again.');
    }
}

function closeModal() {
    document.getElementById('workshop-modal').style.display = 'none';
    editingWorkshop = null;
}

// Form submission
document.getElementById('workshop-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    const isEditing = !!editingWorkshop;
    const url = isEditing ? `${window.appConfig.apiPath}workshops/${editingWorkshop.id}` : `${window.appConfig.apiPath}workshops`;
    const method = isEditing ? 'PUT' : 'POST';
    
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': window.csrfToken
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeModal();
            loadWorkshops(); // Reload to get updated data
            alert(isEditing ? 'Workshop updated successfully!' : 'Workshop created successfully!');
        } else {
            alert(result.error || 'Failed to save workshop');
        }
    } catch (error) {
        alert('Network error. Please try again.');
    }
});

function showLoading() {
    hideAllStates();
    document.getElementById('loading').style.display = 'block';
}

function showError(message) {
    hideAllStates();
    document.getElementById('error-message').textContent = message;
    document.getElementById('error').style.display = 'block';
}

function hideAllStates() {
    document.getElementById('loading').style.display = 'none';
    document.getElementById('error').style.display = 'none';
    document.getElementById('no-workshops').style.display = 'none';
    document.getElementById('workshops-container').style.display = 'none';
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Close modal on background click
document.getElementById('workshop-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>