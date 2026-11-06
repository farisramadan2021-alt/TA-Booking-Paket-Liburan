
    </div>
</main>

<script src="/booking_liburan/bo/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const menuToggle = document.getElementById('menuToggle');

function bukaSidebar() {
    sidebar?.classList.add('show');
    overlay?.classList.add('show');
}

function tutupSidebar() {
    sidebar?.classList.remove('show');
    overlay?.classList.remove('show');
}

menuToggle?.addEventListener('click', function () {
    if (sidebar?.classList.contains('show')) {
        tutupSidebar();
    } else {
        bukaSidebar();
    }
});

overlay?.addEventListener('click', tutupSidebar);

window.addEventListener('resize', function () {
    if (window.innerWidth >= 992) {
        tutupSidebar();
    }
});
</script>

</body>
</html>
