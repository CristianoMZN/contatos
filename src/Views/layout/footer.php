    <!-- Footer -->
    <footer class="bg-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>📱 Sistema de Contatos</h5>
                    <p class="text-muted">Gerencie seus contatos de forma moderna e segura.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">
                        © <?= date('Y') ?> - Desenvolvido com ❤️ e PHP
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="/js/bootstrap.bundle.min.js"></script>

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

    // AJAX Contact Loading for Infinite Scroll
    let currentPage = 1;
    let loading = false;
    let hasMorePages = true;

    function loadMoreContacts() {
        if (loading || !hasMorePages) return;
        
        loading = true;
        document.querySelector('.loading-spinner').style.display = 'block';
        
        const params = new URLSearchParams(window.location.search);
        params.set('page', currentPage + 1);
        
        fetch(`/api/contacts?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.contacts.length > 0) {
                    appendContacts(data.contacts);
                    currentPage++;
                    hasMorePages = data.hasMore;
                } else {
                    hasMorePages = false;
                }
            })
            .catch(error => {
                console.error('Error loading contacts:', error);
            })
            .finally(() => {
                loading = false;
                document.querySelector('.loading-spinner').style.display = 'none';
            });
    }

    function appendContacts(contacts) {
        const container = document.querySelector('#contacts-container');
        if (!container) return;
        
        contacts.forEach(contact => {
            const contactCard = createContactCard(contact);
            container.appendChild(contactCard);
        });
    }

    function createContactCard(contact) {
        const col = document.createElement('div');
        col.className = 'col-md-4 col-lg-3 mb-4';
        
        col.innerHTML = `
            <div class="card contact-card h-100">
                <div class="position-relative">
                    <img src="${contact.main_image || '/img/default-contact.jpg'}" 
                         class="card-img-top" alt="${contact.name}">
                    ${contact.category_name ? `
                        <span class="badge bg-primary contact-badge">
                            ${contact.category_icon ? `<i class="${contact.category_icon}"></i>` : ''} 
                            ${contact.category_name}
                        </span>
                    ` : ''}
                </div>
                <div class="card-body">
                    <h5 class="card-title">${contact.name}</h5>
                    ${contact.description ? `<p class="card-text text-muted small">${contact.description.substring(0, 100)}...</p>` : ''}
                </div>
                <div class="card-footer bg-transparent">
                    <a href="/contato/${contact.slug}" class="btn btn-primary btn-sm">Ver Detalhes</a>
                </div>
            </div>
        `;
        
        return col;
    }

    // Infinite scroll implementation
    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelector('#contacts-container')) {
            window.addEventListener('scroll', function() {
                if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1000) {
                    loadMoreContacts();
                }
            });
        }
    });

    // Dynamic form fields for phones and emails
    function addPhoneField() {
        const container = document.getElementById('phones-container');
        if (!container) return;
        
        const index = container.children.length;
        const fieldHtml = `
            <div class="row mb-3 phone-field">
                <div class="col-md-5">
                    <input type="tel" name="phones[${index}][phone]" class="form-control" placeholder="(11) 99999-9999" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="phones[${index}][department]" class="form-control" placeholder="Departamento (opcional)">
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input type="checkbox" name="phones[${index}][is_whatsapp]" class="form-check-input" id="whatsapp_${index}">
                        <label class="form-check-label" for="whatsapp_${index}">WhatsApp</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeField(this)">×</button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', fieldHtml);
        
        // Apply phone mask to new field
        const newInput = container.lastElementChild.querySelector('input[type="tel"]');
        newInput.addEventListener('input', function() {
            applyPhoneMask(this);
        });
    }

    function addEmailField() {
        const container = document.getElementById('emails-container');
        if (!container) return;
        
        const index = container.children.length;
        const fieldHtml = `
            <div class="row mb-3 email-field">
                <div class="col-md-6">
                    <input type="email" name="emails[${index}][email]" class="form-control" placeholder="email@exemplo.com" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="emails[${index}][department]" class="form-control" placeholder="Departamento (opcional)">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeField(this)">×</button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', fieldHtml);
    }

    function removeField(button) {
        button.closest('.row').remove();
    }

    // Search functionality
    let searchTimeout;
    function handleSearch(input) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const form = input.closest('form');
            if (form) {
                form.submit();
            }
        }, 500);
    }
    </script>

    <!-- Custom page scripts -->
    <?php if (isset($pageScripts)): ?>
        <?= $pageScripts ?>
    <?php endif; ?>

</body>
</html>