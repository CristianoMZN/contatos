<footer>
<div class="container">
    <p></p>
</div>
</footer>

<!-- Theme Switcher Button -->
<div class="theme-switcher">
    <button type="button" class="btn btn-outline-primary" id="theme-toggle" title="Alternar tema claro/escuro">
        🌙
    </button>
</div>

<script src="js/bootstrap.bundle.js"></script>
<script>
    // Phone mask functionality
    function applyPhoneMask(input) {
        input.addEventListener('input', function (e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
        
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && e.target.value.length === 1) {
                e.target.value = '';
            }
        });
    }
    
    // Apply mask to all phone inputs when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach(applyPhoneMask);
    });
    
    // Theme switcher functionality
    const themeToggle = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;
    
    // Load saved theme or default to dark
    const savedTheme = localStorage.getItem('theme') || 'dark';
    htmlElement.setAttribute('data-bs-theme', savedTheme);
    updateToggleIcon(savedTheme);
    
    // Toggle theme function
    themeToggle.addEventListener('click', () => {
        const currentTheme = htmlElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        htmlElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateToggleIcon(newTheme);
    });
    
    // Update toggle button icon
    function updateToggleIcon(theme) {
        themeToggle.textContent = theme === 'dark' ? '☀️' : '🌙';
        themeToggle.title = theme === 'dark' ? 'Alternar para modo claro' : 'Alternar para modo escuro';
    }
</script>
</body>
</html>