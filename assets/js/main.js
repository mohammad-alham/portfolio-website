/* ============================================
   NETWORK ENGINEER PORTFOLIO - Main JavaScript
   Author: [Your Name]
   Description: All JavaScript functionality
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {

    'use strict';

    /* ------------------------------------------
       1. Loader
       ------------------------------------------ */
    const loader = document.getElementById('loader');
    window.addEventListener('load', function() {
        setTimeout(function() {
            loader.classList.add('opacity-0');
            setTimeout(function() {
                loader.style.display = 'none';
            }, 500);
        }, 800);
    });

    /* ------------------------------------------
       2. Theme Toggle (Dark/Light)
       ------------------------------------------ */
    const themeToggle = document.querySelector('.theme-toggle');
    const html = document.documentElement;
    const body = document.body;

    function setTheme(theme) {
        html.setAttribute('data-theme', theme);
        if (theme === 'light') {
            body.classList.remove('bg-[#0a0a0f]', 'text-gray-100');
            body.classList.add('bg-gray-50', 'text-gray-900');
            document.cookie = 'theme=light; path=/; max-age=' + (60 * 60 * 24 * 365);
        } else {
            body.classList.remove('bg-gray-50', 'text-gray-900');
            body.classList.add('bg-[#0a0a0f]', 'text-gray-100');
            document.cookie = 'theme=dark; path=/; max-age=' + (60 * 60 * 24 * 365);
        }
        updateThemeIcon(theme);
    }

    function updateThemeIcon(theme) {
        if (themeToggle) {
            themeToggle.innerHTML = theme === 'light'
                ? '<i class="fas fa-moon"></i>'
                : '<i class="fas fa-sun"></i>';
        }
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const current = html.getAttribute('data-theme');
            setTheme(current === 'light' ? 'dark' : 'light');
        });
    }

    /* ------------------------------------------
       3. Mobile Menu
       ------------------------------------------ */
    const mobileBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileOverlay = document.querySelector('.mobile-overlay');

    function toggleMobileMenu() {
        mobileBtn.classList.toggle('active');
        mobileMenu.classList.toggle('open');
        if (mobileOverlay) mobileOverlay.classList.toggle('open');
        document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
    }

    if (mobileBtn) {
        mobileBtn.addEventListener('click', toggleMobileMenu);
    }
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', toggleMobileMenu);
    }

    /* Close mobile menu on link click */
    document.querySelectorAll('.mobile-menu a').forEach(function(link) {
        link.addEventListener('click', function() {
            if (mobileMenu.classList.contains('open')) {
                toggleMobileMenu();
            }
        });
    });

    /* ------------------------------------------
       4. Navbar Scroll Effect
       ------------------------------------------ */
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    /* ------------------------------------------
       5. Back to Top Button
       ------------------------------------------ */
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 400) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });
        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ------------------------------------------
       6. Scroll Animations (Intersection Observer)
       ------------------------------------------ */
    const animateElements = document.querySelectorAll('.animate-on-scroll, .animate-on-scroll-left, .animate-on-scroll-right, .animate-on-scroll-scale');

    if (animateElements.length > 0) {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        animateElements.forEach(function(el) {
            observer.observe(el);
        });
    }

    /* ------------------------------------------
       7. Skill Progress Bars
       ------------------------------------------ */
    const skillBars = document.querySelectorAll('.skill-progress');
    if (skillBars.length > 0) {
        const skillObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const bar = entry.target;
                    const targetWidth = bar.getAttribute('data-width') || bar.getAttribute('style')?.match(/width:\s*(\d+)%/)?.[1];
                    if (targetWidth) {
                        setTimeout(function() {
                            bar.style.width = targetWidth + '%';
                        }, 200);
                    }
                    skillObserver.unobserve(bar);
                }
            });
        }, { threshold: 0.3 });

        skillBars.forEach(function(bar) {
            // Store the width from inline style or data attribute
            const match = bar.getAttribute('style')?.match(/width:\s*(\d+)%/);
            if (match) {
                bar.setAttribute('data-width', match[1]);
            }
            bar.style.width = '0%';
            skillObserver.observe(bar);
        });
    }

    /* ------------------------------------------
       8. Stat Counters
       ------------------------------------------ */
    const statNumbers = document.querySelectorAll('.stat-number');
    if (statNumbers.length > 0) {
        const statObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.getAttribute('data-target')) || 0;
                    const duration = 2000;
                    const step = Math.ceil(target / (duration / 16));
                    let current = 0;

                    const counter = setInterval(function() {
                        current += step;
                        if (current >= target) {
                            current = target;
                            clearInterval(counter);
                        }
                        el.textContent = current + (el.getAttribute('data-suffix') || '+');
                    }, 16);

                    statObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        statNumbers.forEach(function(el) {
            statObserver.observe(el);
        });
    }

    /* ------------------------------------------
       9. Portfolio Filtering
       ------------------------------------------ */
    const filterBtns = document.querySelectorAll('.filter-btn');
    const projectItems = document.querySelectorAll('.project-item');

    if (filterBtns.length > 0) {
        filterBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                filterBtns.forEach(function(b) { b.classList.remove('active'); });
                this.classList.add('active');

                const filterValue = this.getAttribute('data-filter');

                projectItems.forEach(function(item) {
                    if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                        item.style.display = 'block';
                        item.classList.add('animate-on-scroll');
                        setTimeout(function() {
                            item.classList.add('animated');
                        }, 100);
                    } else {
                        item.style.display = 'none';
                        item.classList.remove('animated');
                    }
                });
            });
        });
    }

    /* ------------------------------------------
       10. Project Modal
       ------------------------------------------ */
    const modalTriggers = document.querySelectorAll('[data-modal-trigger]');
    const projectModal = document.querySelector('.project-modal');
    const modalClose = document.querySelector('.modal-close');

    function openProjectModal(projectId) {
        if (!projectModal) return;
        const modalBody = projectModal.querySelector('.modal-body');

        // Find project data
        const projectCard = document.querySelector('.project-item[data-project="' + projectId + '"]');
        if (!projectCard) return;

        const title = projectCard.querySelector('.project-title')?.textContent || 'Project Title';
        const desc = projectCard.querySelector('.project-description')?.textContent || '';
        const image = projectCard.querySelector('.project-image')?.src || '';
        const category = projectCard.getAttribute('data-category') || 'general';
        const details = projectCard.getAttribute('data-details') || 'No additional details available.';

        if (modalBody) {
            modalBody.innerHTML = `
                <div class="mb-6">
                    <img src="${image}" alt="${title}" class="w-full h-64 object-cover rounded-lg" onerror="this.parentElement.innerHTML='<div class=\'w-full h-64 rounded-lg bg-gradient-to-br from-network-900 to-primary-900 flex items-center justify-center text-network-400 text-4xl\'><i class=\'fas fa-network-wired\'></i></div>'">
                </div>
                <span class="text-xs font-mono text-network-400 uppercase tracking-wider">${category}</span>
                <h3 class="text-2xl font-bold mt-2 mb-4">${title}</h3>
                <p class="text-gray-400 mb-6">${desc}</p>
                <div class="text-gray-300 leading-relaxed">${details}</div>
            `;
        }

        projectModal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    if (modalTriggers.length > 0) {
        modalTriggers.forEach(function(trigger) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const projectId = this.getAttribute('data-modal-trigger');
                openProjectModal(projectId);
            });
        });
    }

    if (modalClose) {
        modalClose.addEventListener('click', function() {
            projectModal.classList.remove('open');
            document.body.style.overflow = '';
        });
    }

    if (projectModal) {
        projectModal.querySelector('.modal-backdrop')?.addEventListener('click', function() {
            projectModal.classList.remove('open');
            document.body.style.overflow = '';
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && projectModal.classList.contains('open')) {
                projectModal.classList.remove('open');
                document.body.style.overflow = '';
            }
        });
    }

    /* ------------------------------------------
       11. Contact Form Validation
       ------------------------------------------ */
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const subjectInput = document.getElementById('subject');
        const messageInput = document.getElementById('message');
        const formStatus = document.getElementById('formStatus');

        function validateEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function validateField(input) {
            const value = input.value.trim();
            let valid = true;

            if (input.id === 'name') {
                valid = value.length >= 2;
            } else if (input.id === 'email') {
                valid = validateEmail(value);
            } else if (input.id === 'subject') {
                valid = value.length >= 3;
            } else if (input.id === 'message') {
                valid = value.length >= 10;
            }

            if (valid) {
                input.classList.remove('error');
            } else {
                input.classList.add('error');
            }
            return valid;
        }

        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const isValidName = validateField(nameInput);
            const isValidEmail = validateField(emailInput);
            const isValidSubject = validateField(subjectInput);
            const isValidMessage = validateField(messageInput);

            if (isValidName && isValidEmail && isValidSubject && isValidMessage) {
                if (formStatus) {
                    formStatus.innerHTML = '<div class="p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400"><i class="fas fa-check-circle mr-2"></i> Thank you! Your message has been sent successfully. I will get back to you soon.</div>';
                    formStatus.style.display = 'block';
                }
                contactForm.reset();
            } else {
                if (formStatus) {
                    formStatus.innerHTML = '<div class="p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400"><i class="fas fa-exclamation-circle mr-2"></i> Please fill in all fields correctly before submitting.</div>';
                    formStatus.style.display = 'block';
                }
            }
        });

        [nameInput, emailInput, subjectInput, messageInput].forEach(function(input) {
            if (input) {
                input.addEventListener('input', function() {
                    validateField(this);
                    if (formStatus) formStatus.style.display = 'none';
                });
                input.addEventListener('blur', function() {
                    validateField(this);
                });
            }
        });
    }

    /* ------------------------------------------
       12. Active Nav Link
       ------------------------------------------ */
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.nav-link').forEach(function(link) {
        const href = link.getAttribute('href');
        if (href === currentPage || (currentPage === '' && href === 'index.php')) {
            link.classList.add('active');
        }
    });

    /* ------------------------------------------
       13. Smooth Scroll for Anchor Links
       ------------------------------------------ */
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* ------------------------------------------
       14. Typing Animation (Home page)
       ------------------------------------------ */
    const typingElement = document.querySelector('.typing-text');
    if (typingElement) {
        const text = typingElement.textContent;
        typingElement.textContent = '';
        typingElement.style.width = '0';
        typingElement.style.animation = 'none';

        // Force reflow
        void typingElement.offsetWidth;

        typingElement.style.animation = 'typing 3.5s steps(' + text.length + ') 1s 1 normal both, blink 0.7s step-end infinite';
        typingElement.textContent = text;
    }

    /* ------------------------------------------
       15. Certificate Download
       ------------------------------------------ */
    document.querySelectorAll('.cert-download').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const certName = this.getAttribute('data-cert') || 'Certificate';
            // In production, this would download the actual file
            alert('Download: ' + certName + '.pdf\n\n(Placeholder - Replace with actual certificate file)');
        });
    });

    console.log('%c Network Engineer Portfolio ', 'background: #06b6d4; color: #fff; font-size: 16px; font-weight: bold; padding: 10px 20px; border-radius: 4px;');
    console.log('%c Built with ❤ using PHP, HTML, CSS & JavaScript ', 'color: #06b6d4; font-size: 12px;');

});
