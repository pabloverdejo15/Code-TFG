<!-- Global Mobile Overlay -->
<div class="sidebar-overlay" id="mobileOverlay"></div>

<!-- Global Mobile Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('mobileMenuBtn');
        const membersBtn = document.getElementById('mobileMembersBtn');
        const sidebar = document.querySelector('.l-app__sidebar');
        const contextSidebar = document.querySelector('.l-app__context');
        const secondary = document.querySelector('.l-app__secondary');
        const overlay = document.getElementById('mobileOverlay');

        function toggleLeftMenu() {
            if (sidebar) sidebar.classList.toggle('is-open');
            if (secondary) secondary.classList.toggle('is-open');
            if (overlay) overlay.classList.toggle('is-active');
            if (contextSidebar && contextSidebar.classList.contains('is-open')) {
                contextSidebar.classList.remove('is-open');
            }
        }

        function toggleRightMenu() {
            if (contextSidebar) contextSidebar.classList.toggle('is-open');
            if (overlay) overlay.classList.toggle('is-active');
            if (sidebar && sidebar.classList.contains('is-open')) sidebar.classList.remove('is-open');
            if (secondary && secondary.classList.contains('is-open')) secondary.classList.remove('is-open');
        }
        
        function closeAllMenus() {
            if (sidebar) sidebar.classList.remove('is-open');
            if (secondary) secondary.classList.remove('is-open');
            if (contextSidebar) contextSidebar.classList.remove('is-open');
            if (overlay) overlay.classList.remove('is-active');
        }

        if(menuBtn) menuBtn.addEventListener('click', toggleLeftMenu);
        if(membersBtn) membersBtn.addEventListener('click', toggleRightMenu);
        if(overlay) overlay.addEventListener('click', closeAllMenus);
    });
</script>
</body>
</html>
