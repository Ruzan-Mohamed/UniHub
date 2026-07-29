document.addEventListener('DOMContentLoaded', () => {

    const searchInput = document.querySelector('.search-input-group input, input[type="search"]');
    const resourceCards = document.querySelectorAll('.resource-card');

    if (searchInput && resourceCards.length > 0) {
       
        const cardsContainer = resourceCards[0].closest('.row');
        const noResultsMessage = document.createElement('div');
        noResultsMessage.className = 'col-12 text-center py-5 no-results-msg d-none';
        noResultsMessage.innerHTML = `
            <div class="text-muted">
                <i class="bi bi-file-earmark-x fs-1 mb-3 d-block text-primary"></i>
                <h5 class="fw-bold text-dark">No matching resources found</h5>
                <p class="small">Try adjusting your keywords, course codes, or tags.</p>
            </div>
        `;
        if (cardsContainer) {
            cardsContainer.appendChild(noResultsMessage);
        }

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            let visibleCount = 0;

            resourceCards.forEach(card => {
                const title = card.querySelector('h6')?.textContent.toLowerCase() || '';
                const code = card.querySelector('.text-muted')?.textContent.toLowerCase() || '';
                const tags = Array.from(card.querySelectorAll('.badge')).map(b => b.textContent.toLowerCase()).join(' ');

                if (title.includes(query) || code.includes(query) || tags.includes(query)) {
                    card.closest('.col-lg-4, .col-md-6, .col-12').classList.remove('d-none');
                    visibleCount++;
                } else {
                    card.closest('.col-lg-4, .col-md-6, .col-12').classList.add('d-none');
                }
            });

            if (visibleCount === 0) {
                noResultsMessage.classList.remove('d-none');
            } else {
                noResultsMessage.classList.add('d-none');
            }
        });
    }
}