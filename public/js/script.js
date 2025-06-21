// Password toggle functionality
document.querySelectorAll('.password-toggle').forEach(toggle => {
    toggle.addEventListener('click', function() {
        const input = this.previousElementSibling;
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        this.innerHTML = type === 'password' ? 
            '<i class="fas fa-eye"></i>' : 
            '<i class="fas fa-eye-slash"></i>';
    });
});

// WhatsApp contact buttons
document.querySelectorAll('.whatsapp-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const phone = this.dataset.phone;
        const name = this.dataset.name;
        const message = `Hi ${name}, I saw your profile on WakaziLink`;
        const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
        window.open(url, '_blank');
    });
});

// Form validation
document.querySelectorAll('form.needs-validation').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });
});

// Search form AJAX
document.addEventListener('DOMContentLoaded', () => {
    const searchForm = document.querySelector('#search-form');
    
    if(searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const skills = document.querySelector('[name="skills"]').value;
            const location = document.querySelector('[name="location"]').value;
            
            fetch(`search.php?skills=${skills}&location=${location}`)
                .then(response => response.text())
                .then(data => {
                    document.querySelector('#worker-results').innerHTML = data;
                });
        });
    }
});