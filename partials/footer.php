</div></div></div><?php if (!empty($user)): include APP_ROOT . '/partials/modals.php'; endif; ?>

<script src="<?= asset_url('js/app.js') ?>?v=<?= filemtime(APP_ROOT . '/assets/js/app.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('dashboard-sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle-btn');
    const topBar = document.getElementById('top-bar');
    const burgerIcon = document.getElementById('burger-svg-icon');
    const closeIcon = document.getElementById('close-svg-icon');
    
    if (!sidebar) return;

    // 1. Read saved choice from localStorage (Default to 'open' if none exists)
    let sidebarState = localStorage.getItem('dms_sidebar_state');
    if (sidebarState === null) {
        sidebarState = 'open';
        localStorage.setItem('dms_sidebar_state', 'open');
    }

    // 2. Synchronizer function matching header.php parameters exactly
    function syncSidebarUI(state) {
        if (state === 'closed') {
            sidebar.classList.add('collapsed');
            if (topBar) topBar.classList.add('sidebar-collapsed');
            if (burgerIcon) burgerIcon.style.display = 'block';
            if (closeIcon) closeIcon.style.display = 'none';
        } else {
            sidebar.classList.remove('collapsed');
            if (topBar) topBar.classList.remove('sidebar-collapsed');
            if (burgerIcon) burgerIcon.style.display = 'none';
            if (closeIcon) closeIcon.style.display = 'block';
        }
        
        // Trigger resize events for responsive system canvas grids/charts
        window.dispatchEvent(new Event('resize'));
        if (window.activityChartInstance) window.activityChartInstance.resize();
    }

    // Apply the synchronized state instantly on DOM render bounds
    syncSidebarUI(sidebarState);

    // 3. Click handler linked exclusively to manual user actions on the burger button
    if (toggleBtn) {
        toggleBtn.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            
            const isCurrentlyCollapsed = sidebar.classList.contains('collapsed');
            // If it is collapsed, clicking it opens it. If it is open, clicking it closes it.
            const nextState = isCurrentlyCollapsed ? 'open' : 'closed';
            
            localStorage.setItem('dms_sidebar_state', nextState);
            syncSidebarUI(nextState);
        });
    }
});
</script>
</body>
</html>
