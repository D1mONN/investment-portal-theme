/**
 * Основний JavaScript для інвестиційного порталу Славутської громади
 * 
 * @package SlavutskaInvestment
 * @version 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Головний клас додатка
     */
    class SlavutskaInvestmentApp {
        constructor() {
            this.init();
        }

        /**
         * Ініціалізація додатка
         */
        init() {
            this.bindEvents();
            this.initComponents();
            this.handlePageLoad();
        }

        /**
         * Прив'язка подій
         */
        bindEvents() {
            $(document).ready(() => this.onDocumentReady());
            $(window).on('load', () => this.onWindowLoad());
            $(window).on('scroll', () => this.onWindowScroll());
            $(window).on('resize', () => this.onWindowResize());
        }

        /**
         * Обробка завантаження документа
         */
        onDocumentReady() {
            this.initMobileMenu();
            this.initSmoothScroll();
            this.initBackToTop();
            this.initCookiesNotification();
            this.initContactForm();
            this.initAnimations();
            this.initLazyLoading();
        }

        /**
         * Обробка завантаження сторінки
         */
        onWindowLoad() {
            this.hidePreloader();
            this.initParallax();
        }

        /**
         * Обробка прокрутки сторінки
         */
        onWindowScroll() {
            this.handleHeaderScroll();
            this.handleBackToTopVisibility();
            this.handleScrollAnimations();
        }

        /**
         * Обробка зміни розміру вікна
         */
        onWindowResize() {
            this.handleMobileMenuResize();
        }

        /**
         * Ініціалізація компонентів
         */
        initComponents() {
            this.mobileMenu = new MobileMenu();
            this.scrollHandler = new ScrollHandler();
            this.formHandler = new FormHandler();
            this.animationHandler = new AnimationHandler();
        }

        /**
         * Обробка завантаження сторінки
         */
        handlePageLoad() {
            // Видалення preloader після завантаження
            setTimeout(() => {
                $('body').addClass('loaded');
            }, 500);
        }

        /**
         * Ініціалізація мобільного меню
         */
        initMobileMenu() {
            const $menuToggle = $('.menu-toggle');
            const $primaryMenu = $('.primary-menu');
            
            $menuToggle.on('click', function(e) {
                e.preventDefault();
                
                const isActive = $primaryMenu.hasClass('active');
                
                if (isActive) {
                    $primaryMenu.removeClass('active');
                    $menuToggle.removeClass('active');
                    $('body').removeClass('menu-open');
                } else {
                    $primaryMenu.addClass('active');
                    $menuToggle.addClass('active');
                    $('body').addClass('menu-open');
                }
                
                // Зміна іконки
                $menuToggle.attr('aria-expanded', !isActive);
            });

            // Закриття меню при кліку на посилання
            $primaryMenu.find('a').on('click', function() {
                $primaryMenu.removeClass('active');
                $menuToggle.removeClass('active');
                $('body').removeClass('menu-open');
                $menuToggle.attr('aria-expanded', false);
            });

            // Закриття меню при кліку поза ним
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.main-navigation').length) {
                    $primaryMenu.removeClass('active');
                    $menuToggle.removeClass('active');
                    $('body').removeClass('menu-open');
                    $menuToggle.attr('aria-expanded', false);
                }
            });
        }

        /**
         * Ініціалізація плавної прокрутки
         */
        initSmoothScroll() {
            $('a[data-scroll-to]').on('click', function(e) {
                e.preventDefault();
                
                const target = $(this).data('scroll-to');
                const $target = $('#' + target);
                
                if ($target.length) {
                    const headerHeight = $('.site-header').outerHeight();
                    const targetOffset = $target.offset().top - headerHeight - 20;
                    
                    $('html, body').animate({
                        scrollTop: targetOffset
                    }, 800, 'easeInOutCubic');
                }
            });

            // Плавна прокрутка для звичайних якорних посилань
            $('a[href^="#"]').not('[data-scroll-to]').on('click', function(e) {
                const target = $(this).attr('href');
                const $target = $(target);
                
                if ($target.length) {
                    e.preventDefault();
                    
                    const headerHeight = $('.site-header').outerHeight();
                    const targetOffset = $target.offset().top - headerHeight - 20;
                    
                    $('html, body').animate({
                        scrollTop: targetOffset
                    }, 800, 'easeInOutCubic');
                }
            });
        }

        /**
         * Ініціалізація кнопки "Вгору"
         */
        initBackToTop() {
            const $backToTop = $('.back-to-top');
            
            $backToTop.on('click', function(e) {
                e.preventDefault();
                
                $('html, body').animate({
                    scrollTop: 0
                }, 800, 'easeInOutCubic');
            });
        }

        /**
         * Обробка прокрутки для шапки
         */
        handleHeaderScroll() {
            const $header = $('.site-header');
            const scrollTop = $(window).scrollTop();
            
            if (scrollTop > 100) {
                $header.addClass('scrolled');
            } else {
                $header.removeClass('scrolled');
            }
        }

        /**
         * Обробка видимості кнопки "Вгору"
         */
        handleBackToTopVisibility() {
            const $backToTop = $('.back-to-top');
            const scrollTop = $(window).scrollTop();
            
            if (scrollTop > 300) {
                $backToTop.addClass('visible');
            } else {
                $backToTop.removeClass('visible');
            }
        }

        /**
         * Ініціалізація повідомлення про cookies
         */
        initCookiesNotification() {
            const $cookiesNotification = $('#cookies-notification');
            const $acceptButton = $('#accept-cookies');
            
            // Перевірка, чи користувач уже прийняв cookies
            if (!this.getCookie('cookies_accepted')) {
                setTimeout(() => {
                    $cookiesNotification.slideDown();
                }, 2000);
            }
            
            $acceptButton.on('click', function() {
                app.setCookie('cookies_accepted', 'true', 365);
                $cookiesNotification.slideUp();
            });
        }

        /**
         * Ініціалізація контактної форми
         */
        initContactForm() {
            const $contactForm = $('#contact-form');
            
            if ($contactForm.length) {
                $contactForm.on('submit', this.handleContactFormSubmit.bind(this));
                
                // Валідація в реальному часі
                $contactForm.find('input, textarea').on('blur', this.validateField);
                $contactForm.find('input[type="email"]').on('input', this.validateEmail);
            }
        }

        /**
         * Обробка відправки контактної форми
         */
        handleContactFormSubmit(e) {
            e.preventDefault();
            
            const $form = $(e.target);
            const $submitButton = $form.find('button[type="submit"]');
            const originalText = $submitButton.text();
            
            // Валідація форми
            if (!this.validateForm($form)) {
                return false;
            }
            
            // Показ індикатора завантаження
            $submitButton.prop('disabled', true).text(slavutskaAjax.strings.loading);
            
            // Відправка даних
            $.ajax({
                url: slavutskaAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'slavutska_contact_form',
                    nonce: slavutskaAjax.nonce,
                    ...this.serializeForm($form)
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotification('success', response.data.message);
                        $form[0].reset();
                    } else {
                        this.showNotification('error', response.data.message);
                    }
                },
                error: () => {
                    this.showNotification('error', slavutskaAjax.strings.error);
                },
                complete: () => {
                    $submitButton.prop('disabled', false).text(originalText);
                }
            });
        }

        /**
         * Валідація форми
         */
        validateForm($form) {
            let isValid = true;
            
            $form.find('[required]').each(function() {
                const $field = $(this);
                if (!app.validateField.call($field[0])) {
                    isValid = false;
                }
            });
            
            return isValid;
        }

        /**
         * Валідація поля
         */
        validateField() {
            const $field = $(this);
            const value = $field.val().trim();
            const fieldType = $field.attr('type');
            const isRequired = $field.attr('required');
            
            let isValid = true;
            let errorMessage = '';
            
            // Перевірка обов'язковості
            if (isRequired && !value) {
                isValid = false;
                errorMessage = 'Це поле є обов\'язковим';
            }
            
            // Перевірка email
            if (fieldType === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Введіть правильну email адресу';
                }
            }
            
            // Перевірка телефону
            if ($field.attr('name') === 'phone' && value) {
                const phoneRegex = /^[\+]?[0-9\s\-\(\)]+$/;
                if (!phoneRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Введіть правильний номер телефону';
                }
            }
            
            // Показ/приховування помилки
            const $errorElement = $field.siblings('.field-error');
            
            if (!isValid) {
                $field.addClass('error');
                if ($errorElement.length) {
                    $errorElement.text(errorMessage);
                } else {
                    $field.after(`<span class="field-error">${errorMessage}</span>`);
                }
            } else {
                $field.removeClass('error');
                $errorElement.remove();
            }
            
            return isValid;
        }

        /**
         * Валідація email в реальному часі
         */
        validateEmail() {
            const $field = $(this);
            const value = $field.val().trim();
            
            if (value.length > 0) {
                app.validateField.call(this);
            }
        }

        /**
         * Серіалізація форми в об'єкт
         */
        serializeForm($form) {
            const formData = {};
            $form.serializeArray().forEach(field => {
                formData[field.name] = field.value;
            });
            return formData;
        }

        /**
         * Ініціалізація анімацій
         */
        initAnimations() {
            // Анімація елементів при прокрутці
            if (typeof IntersectionObserver !== 'undefined') {
                this.observeElements();
            }
            
            // Анімація лічильників
            this.initCounters();
        }

        /**
         * Спостереження за елементами для анімацій
         */
        observeElements() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        $(entry.target).addClass('animate-in');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // Додавання елементів для спостереження
            $('.section, .investment-card, .land-plot-card, .advantage-item, .feature-item').each(function() {
                observer.observe(this);
            });
        }

        /**
         * Ініціалізація анімації лічильників
         */
        initCounters() {
            $('.hero-stat-number').each(function() {
                const $counter = $(this);
                const targetNumber = parseInt($counter.text().replace(/\D/g, ''));
                
                if (targetNumber) {
                    $counter.text('0');
                    
                    const observer = new IntersectionObserver((entries) => {
                        if (entries[0].isIntersecting) {
                            app.animateCounter($counter, targetNumber);
                            observer.unobserve(this);
                        }
                    });
                    
                    observer.observe(this);
                }
            });
        }

        /**
         * Анімація лічильника
         */
        animateCounter($element, target) {
            const duration = 2000;
            const steps = 60;
            const increment = target / steps;
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                
                const suffix = $element.text().replace(/\d/g, '');
                $element.text(Math.floor(current) + suffix);
            }, duration / steps);
        }

        /**
         * Обробка анімацій при прокрутці
         */
        handleScrollAnimations() {
            const scrollTop = $(window).scrollTop();
            const windowHeight = $(window).height();
            
            $('.animate-on-scroll').each(function() {
                const $element = $(this);
                const elementTop = $element.offset().top;
                
                if (scrollTop + windowHeight > elementTop + 100) {
                    $element.addClass('animated');
                }
            });
        }

        /**
         * Ініціалізація ледачого завантаження зображень
         */
        initLazyLoading() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }
        }

        /**
         * Приховування preloader
         */
        hidePreloader() {
            const $preloader = $('.preloader');
            if ($preloader.length) {
                $preloader.fadeOut(500, function() {
                    $(this).remove();
                });
            }
        }

        /**
         * Ініціалізація паралакс ефекту
         */
        initParallax() {
            if ($(window).width() > 768) {
                $(window).on('scroll', function() {
                    const scrollTop = $(this).scrollTop();
                    
                    $('.hero-bg-image').css({
                        transform: `translateY(${scrollTop * 0.5}px)`
                    });
                });
            }
        }

        /**
         * Обробка зміни розміру для мобільного меню
         */
        handleMobileMenuResize() {
            if ($(window).width() > 768) {
                $('.primary-menu').removeClass('active');
                $('.menu-toggle').removeClass('active');
                $('body').removeClass('menu-open');
            }
        }

        /**
         * Показ повідомлення
         */
        showNotification(type, message) {
            const $notification = $(`
                <div class="notification notification--${type}">
                    <div class="notification-content">
                        <span class="notification-message">${message}</span>
                        <button class="notification-close" aria-label="Закрити">×</button>
                    </div>
                </div>
            `);

            $('body').append($notification);

            // Показ повідомлення
            setTimeout(() => {
                $notification.addClass('visible');
            }, 100);

            // Автоматичне приховування
            setTimeout(() => {
                this.hideNotification($notification);
            }, 5000);

            // Закриття по кліку
            $notification.find('.notification-close').on('click', () => {
                this.hideNotification($notification);
            });
        }

        /**
         * Приховування повідомлення
         */
        hideNotification($notification) {
            $notification.removeClass('visible');
            setTimeout(() => {
                $notification.remove();
            }, 300);
        }

        /**
         * Отримання cookie
         */
        getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }

        /**
         * Встановлення cookie
         */
        setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
        }

        /**
         * Дебаунс функція
         */
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        /**
         * Тротлінг функція
         */
        throttle(func, delay) {
            let inProgress = false;
            return function(...args) {
                if (inProgress) return;
                inProgress = true;
                setTimeout(() => {
                    func.apply(this, args);
                    inProgress = false;
                }, delay);
            };
        }
    }

    /**
     * Клас для роботи з мобільним меню
     */
    class MobileMenu {
        constructor() {
            this.init();
        }

        init() {
            this.addAccessibility();
        }

        addAccessibility() {
            // Додавання ARIA атрибутів для доступності
            $('.menu-toggle').attr('aria-expanded', 'false');
            $('.primary-menu').attr('aria-hidden', 'true');
        }
    }

    /**
     * Клас для обробки прокрутки
     */
    class ScrollHandler {
        constructor() {
            this.init();
        }

        init() {
            this.bindEvents();
        }

        bindEvents() {
            $(window).on('scroll', this.throttle(this.handleScroll.bind(this), 16));
        }

        handleScroll() {
            this.updateActiveNavigation();
        }

        updateActiveNavigation() {
            const scrollTop = $(window).scrollTop();
            const headerHeight = $('.site-header').outerHeight();

            $('.primary-menu a[href^="#"]').each(function() {
                const target = $(this).attr('href');
                const $target = $(target);

                if ($target.length) {
                    const targetTop = $target.offset().top - headerHeight - 50;
                    const targetBottom = targetTop + $target.outerHeight();

                    if (scrollTop >= targetTop && scrollTop < targetBottom) {
                        $('.primary-menu a').removeClass('active');
                        $(this).addClass('active');
                    }
                }
            });
        }

        throttle(func, delay) {
            let inProgress = false;
            return function(...args) {
                if (inProgress) return;
                inProgress = true;
                setTimeout(() => {
                    func.apply(this, args);
                    inProgress = false;
                }, delay);
            };
        }
    }

    /**
     * Клас для обробки форм
     */
    class FormHandler {
        constructor() {
            this.init();
        }

        init() {
            this.addFormEnhancements();
        }

        addFormEnhancements() {
            // Додавання placeholder анімацій
            $('input, textarea').on('focus blur', function() {
                $(this).closest('.field-wrapper').toggleClass('focused', this.value !== '' || this === document.activeElement);
            });
        }
    }

    /**
     * Клас для обробки анімацій
     */
    class AnimationHandler {
        constructor() {
            this.init();
        }

        init() {
            this.addAnimationClasses();
        }

        addAnimationClasses() {
            // Додавання класів для анімацій
            $('.section').addClass('animate-on-scroll');
            $('.investment-card, .land-plot-card').addClass('animate-on-scroll');
            $('.advantage-item, .feature-item').addClass('animate-on-scroll');
        }
    }

    /**
     * Додавання кастомного easing для jQuery
     */
    $.extend($.easing, {
        easeInOutCubic: function(x) {
            return x < 0.5 ? 4 * x * x * x : 1 - Math.pow(-2 * x + 2, 3) / 2;
        }
    });

    /**
     * Ініціалізація додатка
     */
    const app = new SlavutskaInvestmentApp();
    
    // Експорт в глобальну область видимості
    window.SlavutskaApp = app;

})(jQuery);