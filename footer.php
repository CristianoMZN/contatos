<footer>
<div class="container">
    <p></p>
</div>
</footer>
<script src="js/bootstrap.bundle.js"></script>

<!-- Theme Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const themeText = document.getElementById('theme-text');
    const htmlElement = document.documentElement;
    
    // Load theme from localStorage
    const currentTheme = localStorage.getItem('theme') || 'light';
    setTheme(currentTheme);
    
    themeToggle.addEventListener('click', function() {
        const currentTheme = htmlElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
        localStorage.setItem('theme', newTheme);
    });
    
    function setTheme(theme) {
        htmlElement.setAttribute('data-bs-theme', theme);
        if (theme === 'dark') {
            themeToggle.innerHTML = '☀️ <span id="theme-text">Modo Claro</span>';
        } else {
            themeToggle.innerHTML = '🌙 <span id="theme-text">Modo Escuro</span>';
        }
    }
});

// Phone Input Mask
function applyPhoneMask(input) {
    let value = input.value.replace(/\D/g, '');
    
    // Limitar a 11 dígitos (padrão brasileiro)
    if (value.length > 11) {
        value = value.substr(0, 11);
    }
    
    if (value.length <= 10) {
        // Formato: (xx) xxxx-xxxx (telefone fixo)
        value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    } else {
        // Formato: (xx) xxxxx-xxxx (celular)
        value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    }
    
    input.value = value;
}

// Apply mask to all telephone inputs
document.addEventListener('DOMContentLoaded', function() {
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            applyPhoneMask(this);
        });
        
        // Apply mask on paste
        input.addEventListener('paste', function() {
            setTimeout(() => applyPhoneMask(this), 0);
        });
    });
});
</script>
</body>
</html>