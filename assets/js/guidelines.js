
document.addEventListener('DOMContentLoaded', function() {
    
    initializeGuidelines();
});

function initializeGuidelines() {
    
    initializeSmoothScrolling();

    
    initializeTableOfContents();

    
    initializeScrollSpy();

    
    initializeAnimations();
}

function initializeSmoothScrolling() {
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const headerHeight = 80; 
                const targetPosition = target.offsetTop - headerHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

function initializeTableOfContents() {
    const tocItems = document.querySelectorAll('.toc-item');
    
    tocItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const target = document.querySelector(targetId);
            
            if (target) {
                const headerHeight = 80;
                const targetPosition = target.offsetTop - headerHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

function initializeScrollSpy() {
    const sections = document.querySelectorAll('.guideline-section');
    const tocItems = document.querySelectorAll('.toc-item');
    
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                
                tocItems.forEach(item => {
                    item.classList.remove('active');
                });
                
                
                const activeId = entry.target.getAttribute('id');
                const activeTocItem = document.querySelector(`.toc-item[href="#${activeId}"]`);
                if (activeTocItem) {
                    activeTocItem.classList.add('active');
                }
            }
        });
    }, {
        rootMargin: '-80px 0px -50% 0px'
    });
    
    
    sections.forEach(section => {
        observer.observe(section);
    });
}

function initializeAnimations() {
    
    const sections = document.querySelectorAll('.guideline-section');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, {
        threshold: 0.1
    });
    
    sections.forEach(section => {
        observer.observe(section);
    });
}


const style = document.createElement('style');
style.textContent = `
    .guideline-section {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease-out;
    }
    
    .guideline-section.animate-in {
        opacity: 1;
        transform: translateY(0);
    }
    
    .toc-item.active {
        background: var(--gradient-1);
        color: white;
        border-color: var(--primary);
        box-shadow: var(--shadow-glow);
    }
    
    .toc-item.active .toc-icon {
        color: white;
    }
`;
document.head.appendChild(style);


function highlightCurrentSection() {
    const sections = document.querySelectorAll('.guideline-section');
    const scrollPosition = window.scrollY + 100;
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionBottom = sectionTop + section.offsetHeight;
        
        if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
            section.classList.add('current');
        } else {
            section.classList.remove('current');
        }
    });
}


window.addEventListener('scroll', highlightCurrentSection);


document.addEventListener('keydown', function(e) {
    
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        const sections = Array.from(document.querySelectorAll('.guideline-section'));
        const currentSection = document.querySelector('.guideline-section.current');
        
        if (currentSection) {
            const currentIndex = sections.indexOf(currentSection);
            let nextIndex;
            
            if (e.key === 'ArrowDown') {
                nextIndex = Math.min(currentIndex + 1, sections.length - 1);
            } else {
                nextIndex = Math.max(currentIndex - 1, 0);
            }
            
            const nextSection = sections[nextIndex];
            if (nextSection) {
                nextSection.scrollIntoView({ behavior: 'smooth' });
            }
        }
    }
});

function addPrintButton() {
    const printButton = document.createElement('button');
    printButton.textContent = '🖨️ Print Guidelines';
    printButton.className = 'btn btn-secondary';
    printButton.style.position = 'fixed';
    printButton.style.bottom = '20px';
    printButton.style.right = '20px';
    printButton.style.zIndex = '1000';
    
    printButton.addEventListener('click', function() {
        window.print();
    });
    
    document.body.appendChild(printButton);
}


addPrintButton();


const printStyles = document.createElement('style');
printStyles.textContent = `
    @media print {
        .header,
        .footer,
        .toc-section,
        .support-section,
        .btn {
            display: none !important;
        }
        
        .guideline-section {
            page-break-inside: avoid;
            margin-bottom: 30px;
        }
        
        .guideline-content {
            box-shadow: none;
            border: 1px solid #ccc;
        }
        
        body {
            font-size: 12pt;
            line-height: 1.4;
        }
        
        h1, h2, h3 {
            color: #000 !important;
        }
    }
`;
document.head.appendChild(printStyles);


function addSearchFunctionality() {
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search guidelines...';
    searchInput.className = 'search-input';
    searchInput.style.position = 'fixed';
    searchInput.style.top = '100px';
    searchInput.style.right = '20px';
    searchInput.style.zIndex = '1000';
    searchInput.style.padding = '8px 12px';
    searchInput.style.borderRadius = '6px';
    searchInput.style.border = '1px solid rgba(255, 255, 255, 0.1)';
    searchInput.style.background = 'var(--surface)';
    searchInput.style.color = 'var(--text)';
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const sections = document.querySelectorAll('.guideline-section');
        
        sections.forEach(section => {
            const text = section.textContent.toLowerCase();
            if (text.includes(query) || query === '') {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    });
    
    document.body.appendChild(searchInput);
}


function initializeVideoTabs() {
    const languageTabs = document.querySelectorAll('.language-tab');
    const videoWrappers = document.querySelectorAll('.video-wrapper');
    
    languageTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const selectedLang = this.getAttribute('data-lang');
            
            
            languageTabs.forEach(t => t.classList.remove('active'));
            videoWrappers.forEach(w => w.classList.remove('active'));
            
            
            this.classList.add('active');
            const targetVideo = document.getElementById(`video-${selectedLang}`);
            if (targetVideo) {
                targetVideo.classList.add('active');
            }
            
            
            const allIframes = document.querySelectorAll('.video-frame iframe');
            allIframes.forEach(iframe => {
                const currentSrc = iframe.src;
                iframe.src = currentSrc; 
            });
        });
    });
}


addSearchFunctionality();
//test

initializeVideoTabs();
