        </div><!-- .page-body -->
    </div><!-- .main-content -->
</div><!-- .app-layout -->

<?php if (!empty($user)): include APP_ROOT . '/partials/modals.php'; endif; ?>

<script src="<?= asset_url('js/app.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('dashboard-sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle-btn');
    const topBar = document.getElementById('top-bar');
    const burgerIcon = document.getElementById('burger-svg-icon');
    const closeIcon = document.getElementById('close-svg-icon');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', e => {
            e.preventDefault();
            sidebar.classList.toggle('collapsed');
            const collapsed = sidebar.classList.contains('collapsed');
            burgerIcon.style.display = collapsed ? 'none' : 'block';
            closeIcon.style.display = collapsed ? 'block' : 'none';
            if (topBar) topBar.classList.toggle('sidebar-collapsed', collapsed);
            window.dispatchEvent(new Event('resize'));
            if (window.activityChartInstance) window.activityChartInstance.resize();
        });
    }
});
</script>
</body>
</html>
