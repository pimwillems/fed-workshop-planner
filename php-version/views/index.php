<?php
ob_start();
?>

<div class="container" style="padding: 2rem 1rem;">
    <div style="text-align: center; margin-bottom: 3rem;">
        <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-primary);">
            FED Workshop Schedule
        </h1>
        <p style="font-size: 1.1rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
            Browse upcoming FED workshops across different subjects. All workshops are planned by day to give teachers flexibility during their teaching time.
        </p>
    </div>

    <!-- View Toggle and Filters -->
    <div style="margin-bottom: 2rem;">
        <!-- View Toggle Buttons -->
        <div style="display: flex; justify-content: center; margin-bottom: 2rem;">
            <div style="display: inline-flex; background: var(--bg-secondary); border-radius: 0.5rem; padding: 0.25rem; gap: 0.25rem;">
                <button 
                    id="tiles-view-btn"
                    class="btn btn-primary"
                    style="border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.875rem;"
                    onclick="switchView('tiles')"
                >
                    📋 Workshop Tiles
                </button>
                <button 
                    id="calendar-view-btn"
                    class="btn"
                    style="border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.875rem;"
                    onclick="switchView('calendar')"
                >
                    📅 Calendar View
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; align-items: center;">
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

    <!-- No Results -->
    <div id="no-results" style="text-align: center; padding: 3rem; display: none;">
        <p style="color: var(--text-secondary); font-size: 1.1rem;">
            No workshops found matching your criteria.
        </p>
        <button onclick="clearFilters()" class="btn btn-primary" style="margin-top: 1rem;">
            View All Workshops
        </button>
    </div>

    <!-- Workshop Tiles View -->
    <div id="tiles-view" style="display: block;">
        <div id="workshops-grid" class="grid grid-2"></div>
    </div>

    <!-- Calendar View -->
    <div id="calendar-view" style="display: none;">
        <div style="background: var(--bg-secondary); border-radius: 0.75rem; padding: 1.5rem; overflow-x: auto;">
            <!-- Calendar Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <button onclick="changeMonth(-1)" class="btn" style="padding: 0.5rem;">
                    ← Previous
                </button>
                <h3 id="calendar-month" style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin: 0;">
                </h3>
                <button onclick="changeMonth(1)" class="btn" style="padding: 0.5rem;">
                    Next →
                </button>
            </div>

            <!-- Days of week header -->
            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; margin-bottom: 1px;">
                <div style="background: var(--bg-primary); padding: 0.75rem; text-align: center; font-weight: 600; font-size: 0.875rem; color: var(--text-muted);">Sun</div>
                <div style="background: var(--bg-primary); padding: 0.75rem; text-align: center; font-weight: 600; font-size: 0.875rem; color: var(--text-muted);">Mon</div>
                <div style="background: var(--bg-primary); padding: 0.75rem; text-align: center; font-weight: 600; font-size: 0.875rem; color: var(--text-muted);">Tue</div>
                <div style="background: var(--bg-primary); padding: 0.75rem; text-align: center; font-weight: 600; font-size: 0.875rem; color: var(--text-muted);">Wed</div>
                <div style="background: var(--bg-primary); padding: 0.75rem; text-align: center; font-weight: 600; font-size: 0.875rem; color: var(--text-muted);">Thu</div>
                <div style="background: var(--bg-primary); padding: 0.75rem; text-align: center; font-weight: 600; font-size: 0.875rem; color: var(--text-muted);">Fri</div>
                <div style="background: var(--bg-primary); padding: 0.75rem; text-align: center; font-weight: 600; font-size: 0.875rem; color: var(--text-muted);">Sat</div>
            </div>

            <!-- Calendar Grid -->
            <div id="calendar-grid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px;">
                <!-- Calendar days will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
// Workshop management JavaScript
let allWorkshops = [];
let filteredWorkshops = [];
let currentView = 'tiles';
let currentDate = new Date();

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadWorkshops();
    updateCalendarHeader();
});

async function loadWorkshops() {
    showLoading();
    
    try {
        const response = await fetch(window.appConfig.apiPath + 'workshops');
        const data = await response.json();
        
        if (data.success) {
            allWorkshops = data.data.workshops;
            filteredWorkshops = [...allWorkshops];
            renderWorkshops();
        } else {
            showError(data.error || 'Failed to load workshops');
        }
    } catch (error) {
        showError('Network error. Please try again.');
    }
}

function applyFilters() {
    const subject = document.getElementById('subject-filter').value;
    const date = document.getElementById('date-filter').value;
    
    filteredWorkshops = allWorkshops.filter(workshop => {
        return (!subject || workshop.subject === subject) &&
               (!date || workshop.date === date);
    });
    
    renderWorkshops();
}

function clearFilters() {
    document.getElementById('subject-filter').value = '';
    document.getElementById('date-filter').value = '';
    filteredWorkshops = [...allWorkshops];
    renderWorkshops();
}

function switchView(view) {
    currentView = view;
    
    // Update button states
    document.getElementById('tiles-view-btn').className = view === 'tiles' ? 'btn btn-primary' : 'btn';
    document.getElementById('calendar-view-btn').className = view === 'calendar' ? 'btn btn-primary' : 'btn';
    
    // Show/hide views
    document.getElementById('tiles-view').style.display = view === 'tiles' ? 'block' : 'none';
    document.getElementById('calendar-view').style.display = view === 'calendar' ? 'block' : 'none';
    
    renderWorkshops();
}

function renderWorkshops() {
    hideAllStates();
    
    if (filteredWorkshops.length === 0) {
        document.getElementById('no-results').style.display = 'block';
        return;
    }
    
    if (currentView === 'tiles') {
        renderTilesView();
    } else {
        renderCalendarView();
    }
}

function renderTilesView() {
    const grid = document.getElementById('workshops-grid');
    grid.innerHTML = '';
    
    filteredWorkshops.forEach(workshop => {
        const card = createWorkshopCard(workshop);
        grid.appendChild(card);
    });
    
    document.getElementById('tiles-view').style.display = 'block';
}

function renderCalendarView() {
    const grid = document.getElementById('calendar-grid');
    grid.innerHTML = '';
    
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDay = firstDay.getDay(); // 0 = Sunday
    const daysInMonth = lastDay.getDate();
    
    // Add empty cells for days before first day of month
    for (let i = 0; i < startDay; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.style.cssText = 'background: var(--bg-muted); min-height: 120px; opacity: 0.5;';
        grid.appendChild(emptyDay);
    }
    
    // Add days of month
    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = createCalendarDay(year, month, day);
        grid.appendChild(dayElement);
    }
    
    // Fill remaining cells to complete the grid
    const totalCells = Math.ceil((daysInMonth + startDay) / 7) * 7;
    for (let i = daysInMonth + startDay; i < totalCells; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.style.cssText = 'background: var(--bg-muted); min-height: 120px; opacity: 0.5;';
        grid.appendChild(emptyDay);
    }
    
    document.getElementById('calendar-view').style.display = 'block';
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
    
    card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
            <div>
                <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; background-color: ${colors.bg}; color: ${colors.text};">
                    ${workshop.subject}
                </span>
            </div>
            <div style="text-align: right; color: var(--text-muted); font-size: 0.875rem; font-weight: 600;">
                📅 ${formatDate(workshop.date)}
            </div>
        </div>
        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-primary);">
            ${workshop.title}
        </h3>
        <p style="color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.5;">
            ${workshop.description}
        </p>
        <div style="display: flex; justify-content: space-between; align-items: center; color: var(--text-muted); font-size: 0.875rem;">
            <span>👨‍🏫 ${workshop.teacher.name}</span>
        </div>
    `;
    
    return card;
}

function createCalendarDay(year, month, day) {
    const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const dayWorkshops = filteredWorkshops.filter(w => w.date === dateString);
    
    const dayElement = document.createElement('div');
    dayElement.style.cssText = 'background: var(--bg-primary); min-height: 120px; padding: 0.5rem; border: 1px solid var(--border-color);';
    
    dayElement.innerHTML = `
        <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem;">
            ${day}
        </div>
        ${dayWorkshops.map(workshop => {
            const colors = {
                'DEV': { bg: 'var(--color-dev-dark)', text: 'white' },
                'UX': { bg: 'var(--color-ux-dark)', text: 'white' },
                'PO': { bg: 'var(--color-po-dark)', text: 'white' },
                'RESEARCH': { bg: 'var(--color-research-dark)', text: 'var(--color-research-text)' },
                'PORTFOLIO': { bg: 'var(--color-portfolio-dark)', text: 'white' },
                'MISC': { bg: 'var(--color-misc-dark)', text: 'var(--color-misc-text)' }
            };
            
            const color = colors[workshop.subject] || colors['MISC'];
            
            return `
                <div style="background-color: ${color.bg}; color: ${color.text}; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 500; line-height: 1.2; margin-bottom: 0.25rem; cursor: pointer;" title="${workshop.title} by ${workshop.teacher.name}">
                    ${workshop.title}
                </div>
            `;
        }).join('')}
    `;
    
    return dayElement;
}

function changeMonth(direction) {
    currentDate.setMonth(currentDate.getMonth() + direction);
    updateCalendarHeader();
    renderWorkshops();
}

function updateCalendarHeader() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                       'July', 'August', 'September', 'October', 'November', 'December'];
    
    const monthYear = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    document.getElementById('calendar-month').textContent = monthYear;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

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
    document.getElementById('no-results').style.display = 'none';
    document.getElementById('tiles-view').style.display = 'none';
    document.getElementById('calendar-view').style.display = 'none';
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>