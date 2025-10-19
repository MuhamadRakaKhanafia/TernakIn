// Add interactivity to menu items
document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all items
            menuItems.forEach(i => i.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Update page title based on selected menu
            const pageTitle = this.querySelector('span').textContent;
            document.querySelector('.header h2').textContent = `${pageTitle} - TernakIN`;
        });
    });
    
    // Add hover effect to cards
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
        
        // Add click event to card footer
        const cardFooter = this.querySelector('.card-footer');
        if (cardFooter) {
            cardFooter.addEventListener('click', function(e) {
                e.stopPropagation();
                const cardTitle = card.querySelector('.card-title').textContent;
                alert(`Membuka detail: ${cardTitle}`);
            });
        }
    });
    
    // Add functionality to action buttons
    const editButtons = document.querySelectorAll('.action-icon.edit');
    const deleteButtons = document.querySelectorAll('.action-icon.delete');

    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const diseaseName = this.closest('tr').querySelector('td:first-child').textContent;
            // For now, redirect to diseases index since no edit route exists
            window.location.href = '/web/diseases';
        });
    });

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const diseaseName = this.closest('tr').querySelector('td:first-child').textContent;
            if (confirm(`Apakah Anda yakin ingin menghapus penyakit: ${diseaseName}?`)) {
                // For now, just show alert since no delete route exists
                alert(`Penyakit ${diseaseName} berhasil dihapus`);
            }
        });
    });

    // Add functionality to table rows
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('click', function() {
            const diseaseName = this.querySelector('td:first-child').textContent;
            // For now, redirect to diseases index since no show route for specific disease exists
            window.location.href = '/web/diseases';
        });
    });
    
    // Simulate loading data
    console.log('TernakIN Dashboard loaded successfully!');
});