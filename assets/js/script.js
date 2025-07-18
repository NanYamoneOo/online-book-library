// Document ready function
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Auto-hide flash messages after 3 seconds
    setTimeout(function() {
        var flashMessages = document.querySelectorAll('#msg-flash');
        flashMessages.forEach(function(msg) {
            msg.style.display = 'none';
        });
    }, 3000);
    
    // Confirm before deleting
    document.querySelectorAll('.confirm-before-delete').forEach(function(button) {
        button.addEventListener('click', function(e) {
            if(!confirm('Are you sure you want to delete this?')) {
                e.preventDefault();
            }
        });
    });
    
    // Rating system interaction
    document.querySelectorAll('.rating input').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var ratingValue = this.value;
            var ratingContainer = this.closest('.rating');
            var labels = ratingContainer.querySelectorAll('label');
            
            labels.forEach(function(label, index) {
                if(index < ratingValue) {
                    label.classList.add('text-warning');
                } else {
                    label.classList.remove('text-warning');
                }
            });
        });
    });
    
    // Search form submission
    var searchForm = document.getElementById('search-form');
    if(searchForm) {
        searchForm.addEventListener('submit', function(e) {
            var searchInput = this.querySelector('input[name="q"]');
            if(searchInput.value.trim() === '') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    }
});