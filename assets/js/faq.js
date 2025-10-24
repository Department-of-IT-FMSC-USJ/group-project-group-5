
document.addEventListener('DOMContentLoaded', function() {
    
    initializeFAQ();
});

function initializeFAQ() {
    
    initializeFAQToggles();

    
    initializeSearch();

    
    initializeSmoothScrolling();
}

function initializeFAQToggles() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const toggle = item.querySelector('.faq-toggle');
        
        question.addEventListener('click', function() {
            toggleFAQItem(item);
        });
    });
}

function toggleFAQItem(item) {
    const isActive = item.classList.contains('active');
    

    document.querySelectorAll('.faq-item').forEach(otherItem => {
        if (otherItem !== item) {
            otherItem.classList.remove('active');
        }
    });
    
    
    if (isActive) {
        item.classList.remove('active');
    } else {
        item.classList.add('active');
    }
}

function initializeSearch() {
    const searchInput = document.getElementById('faqSearch');
    const searchBtn = document.getElementById('searchBtn');
    

    searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        if (query.length > 0) {
            searchFAQ(query);
        } else {
            clearSearch();
        }
    });
    
    
    searchBtn.addEventListener('click', function() {
        const query = searchInput.value.trim().toLowerCase();
        if (query.length > 0) {
            searchFAQ(query);
        }
    });
    
    
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim().toLowerCase();
            if (query.length > 0) {
                searchFAQ(query);
            }
        }
    });
}

function searchFAQ(query) {
    const faqItems = document.querySelectorAll('.faq-item');
    const categories = document.querySelectorAll('.category-card');
    let foundResults = 0;
    

    clearSearch();
    
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question h4').textContent.toLowerCase();
        const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
        
        if (question.includes(query) || answer.includes(query)) {
            item.classList.add('highlighted');
            foundResults++;
            
            
            item.classList.add('active');
        }
    });
    

    categories.forEach(category => {
        const categoryItems = category.querySelectorAll('.faq-item');
        const hasResults = Array.from(categoryItems).some(item => 
            item.classList.contains('highlighted')
        );
        
        if (!hasResults) {
            category.style.display = 'none';
        }
    });
    

    showSearchResults(foundResults, query);
}

function clearSearch() {
    
    document.querySelectorAll('.faq-item').forEach(item => {
        item.classList.remove('highlighted', 'active');
    });
    

    document.querySelectorAll('.category-card').forEach(category => {
        category.style.display = 'block';
    });
    

    hideSearchResults();
}

function showSearchResults(count, query) {
   
    hideSearchResults();
    
    
    const searchResults = document.createElement('div');
    searchResults.className = 'search-results';
    searchResults.innerHTML = `
        <h3>Search Results for "${query}"</h3>
        <p>Found ${count} matching question${count !== 1 ? 's' : ''}</p>
    `;
    
    
    const searchSection = document.querySelector('.search-section');
    searchSection.insertAdjacentElement('afterend', searchResults);
}

function hideSearchResults() {
    const existingResults = document.querySelector('.search-results');
    if (existingResults) {
        existingResults.remove();
    }
}

function initializeSmoothScrolling() {

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}


function highlightSearchTerms(text, query) {
    if (!query) return text;
    
    const regex = new RegExp(`(${query})`, 'gi');
    return text.replace(regex, '<mark>$1</mark>');
}


document.addEventListener('keydown', function(e) {

    if (e.key === 'Escape') {
        const searchInput = document.getElementById('faqSearch');
        if (searchInput.value.trim()) {
            searchInput.value = '';
            clearSearch();
        }
    }
});


function trackFAQInteraction(action, question) {
   
    console.log(`FAQ ${action}: ${question}`);
}


document.addEventListener('click', function(e) {
    if (e.target.closest('.faq-question')) {
        const question = e.target.closest('.faq-question').querySelector('h4').textContent;
        trackFAQInteraction('opened', question);
    }
});


document.getElementById('faqSearch').addEventListener('input', function() {
    if (this.value.trim().length > 2) {
        trackFAQInteraction('searched', this.value);
    }
});
