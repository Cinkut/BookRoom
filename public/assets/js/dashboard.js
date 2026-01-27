document.addEventListener('DOMContentLoaded', () => {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const roomCards = document.querySelectorAll('.room-card');
    const searchInput = document.querySelector('.search-input');

    // Filtering Function
    function filterRooms(filterType) {
        // Update active button state
        filterBtns.forEach(btn => {
            if (btn.dataset.filter === filterType) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Show/Hide cards
        roomCards.forEach(card => {
            const status = card.dataset.status;
            if (filterType === 'all' || status === filterType) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Add Click Events to Buttons
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const filterType = btn.dataset.filter;
            filterRooms(filterType);
        });
    });

    // Search Functionality
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        
        roomCards.forEach(card => {
            const roomName = card.querySelector('h3').textContent.toLowerCase();
            // Search logic combined with visibility
            // Note: In a full implementation, search should respect current filter
            // For now, search operates on all cards
            if (roomName.includes(searchTerm)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
