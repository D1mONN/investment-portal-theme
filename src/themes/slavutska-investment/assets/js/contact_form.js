/**
 * JavaScript для контактної форми Славутського інвестиційного порталу
 * 
 * @package SlavutskaInvestment
 * @version 1.0.0
 */

(function() {
    'use strict';

    /**
     * Клас для роботи з контактною формою
     */
    class SlavutskaContactForm {
        constructor() {
            this.forms = document.querySelectorAll('.slavutska-contact-form');
            this.submitButtons = document.querySelectorAll('.form-submit');
            this.isSubmitting = false;
            
            // Конфігурація
            this.config = {
                submitTimeout: 30000, // 30 секунд
                retryAttempts: 3,
                debounceTime: 300,
                animationDuration: 300
            };
            
            // Повідомлення
            this.messages = {
                required: "Це поле є обов'язковим",
                email: "Введіть правильну email адресу",
                phone: "Введіть правильний номер телефону",
                minLength: "Занадто коротке повідомлення",
                maxLength: "Повідомлення занадто довге",
                network: "Помилка мережі. Перевірте з'єднання з інтернетом",
                timeout: "Час очікування вичерпано. Спробуйте пізніше",
                server: "Помилка сервера. Спробуйте пізніше",
                success: "Повідомлення успішно відправлено!",
                sending: "Відправляємо...",
                retry: "Повторити спробу"
            };
            
            this.init();
        }

        /**
         * Ініціалізація
         */
        init() {
            if (this.forms.length === 0) {
                return;
            }

            this.bindEvents();
            this.setupValidation();
            this.loadSavedData();
            this.initAccessibility();
        }

        /**
         * Прив'язка подій
         */
        bindEvents() {
            this.forms.forEach(form => {
                // Подія відправки форми
                form.addEventListener('submit', this.handleSubmit.bind(this));
                
                // Валідація в реальному часі
                const inputs = form.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    input.addEventListener('blur', this.validateField.bind(this));
                    input.addEventListener('input', this.debounce(this.validateFieldOnInput.bind(this), this.config.debounceTime));
                    
                    // Збереження даних в localStorage
                    input.addEventListener('input', this.debounce(this.saveFieldData.bind(this), 1000));
                });
                
                // Checkbox для згоди на обробку даних
                const privacyCheckbox = form.querySelector('#privacy-consent');
                if (privacyCheckbox) {
                    privacyCheckbox.addEventListener('change', this.validatePrivacyConsent.bind(this));
                }
            });

            // Закриття повідомлень
            document.addEventListener('click', (e) => {
                if (e.target.matches('.notification-close, .form-message-close')) {
                    this.hideMessage(e.target.closest('.notification, .form-message'));
                }
            });
        }

        /**
         * Налаштування валідації
         */
        setupValidation() {
            this.validators = {
                email: (value) => {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return emailRegex.test(value);
                },
                
                phone: (value) => {
                    const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
                    return phoneRegex.test(value);
                },
                
                name: (value) => {
                    return value.trim().length >= 2;
                },
                
                message: (value) => {
                    return value.trim().length >= 10 && value.trim().length <= 5000;
                },
                
                subject: (value) => {
                    return value.trim().length > 0;
                }
            };
        }

        /**
         * Завантаження збережених даних
         */
        loadSavedData() {
            this.forms.forEach(form => {
                const formId = form.id || 'contact-form';
                const savedData = localStorage.getItem(`slavutska_form_${formId}`);
                
                if (savedData) {
                    try {
                        const data = JSON.parse(savedData);
                        this.fillFormData(form, data);
                    } catch (e) {
                        console.warn('Не вдалося завантажити збережені дані форми');
                    }
                }
            });
        }

        /**
         * Заповнення форми збереженими даними
         */
        fillFormData(form, data) {
            Object.keys(data).forEach(name => {
                const field = form.querySelector(`[name="${name}"]`);
                if (field && field.type !== 'checkbox') {
                    field.value = data[name];
                }
            });
        }

        /**
         * Збереження даних поля
         */
        saveFieldData(event) {
            const form = event.target.closest('form');
            const formId = form.id || 'contact-form';
            const formData = new FormData(form);
            const data = {};
            
            // Збираємо дані форми (крім чутливих)
            for (let [name, value] of formData.entries()) {
                if (!['privacy_consent', 'contact_form_nonce'].includes(name)) {
                    data[name] = value;
                }
            }
            
            localStorage.setItem(`slavutska_form_${formId}`, JSON.stringify(data));
        }

        /**
         * Ініціалізація доступності
         */
        initAccessibility() {
            this.forms.forEach(form => {
                // Додавання aria-live для повідомлень
                const messageContainer = form.querySelector('.form-messages');
                if (messageContainer) {
                    messageContainer.setAttribute('aria-live', 'polite');
                    messageContainer.setAttribute('aria-atomic', 'true');
                }
                
                // Додавання describedby для полів з помилками
                const fields = form.querySelectorAll('input, textarea, select');
                fields.forEach(field => {
                    const errorId = `${field.name}-error`;
                    const errorElement = form.querySelector(`#${errorId}`);
                    if (errorElement) {
                        field.setAttribute('aria-describedby', errorId);
                    }
                });
            });
        }

        /**
         * Обробка відправки форми
         */
        async handleSubmit(event) {
            event.preventDefault();
            
            if (this.isSubmitting) {
                return false;
            }
            
            const form = event.target;
            const submitButton = form.querySelector('.form-submit');
            
            // Валідація форми
            if (!this.validateForm(form)) {
                this.showFormErrors(form);
                return false;
            }
            
            this.isSubmitting = true;
            this.setSubmitState(submitButton, true);
            
            try {
                const response = await this.submitForm(form);
                
                if (response.success) {
                    this.handleSubmitSuccess(form, response.data);
                } else {
                    this.handleSubmitError(form, response.data);
                }
            } catch (error) {
                this.handleSubmitError(form, { message: this.getErrorMessage(error) });
            } finally {
                this.isSubmitting = false;
                this.setSubmitState(submitButton, false);
            }
        }

        /**
         * Відправка форми
         */
        async submitForm(form) {
            const formData = new FormData(form);
            
            // Додавання nonce
            if (typeof contactFormAjax !== 'undefined' && contactFormAjax.nonce) {
                formData.append('nonce', contactFormAjax.nonce);
            }
            
            // Додавання action
            formData.append('action', 'slavutska_contact_form');
            
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), this.config.submitTimeout);
            
            try {
                const response = await fetch(contactFormAjax.ajaxurl, {
                    method: 'POST',
                    body: formData,
                    signal: controller.signal,
                    credentials: 'same-origin'
                });
                
                clearTimeout(timeoutId);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                return await response.json();
            } catch (error) {
                clearTimeout(timeoutId);
                throw error;
            }
        }

        /**
         * Обробка успішної відправки
         */
        handleSubmitSuccess(form, data) {
            // Показ повідомлення про успіх
            this.showSuccessMessage(form, data.message || this.messages.success);
            
            // Очищення форми
            form.reset();
            this.clearValidationErrors(form);
            
            // Очищення збережених даних
            const formId = form.id || 'contact-form';
            localStorage.removeItem(`slavutska_form_${formId}`);
            
            // Фокус на повідомленні для доступності
            const successElement = form.querySelector('.form-success');
            if (successElement) {
                successElement.focus();
            }
            
            // Відстеження для аналітики
            this.trackFormSubmission('success');
        }

        /**
         * Обробка помилки відправки
         */
        handleSubmitError(form, data) {
            const message = data.message || this.messages.server;
            
            // Показ загальної помилки
            this.showErrorMessage(form, message);
            
            // Показ помилок полів (якщо є)
            if (data.errors) {
                this.showFieldErrors(form, data.errors);
            }
            
            // Відстеження для аналітики
            this.trackFormSubmission('error', message);
        }

        /**
         * Валідація форми
         */
        validateForm(form) {
            let isValid = true;
            
            // Валідація всіх обов'язкових полів
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!this.validateField({ target: field })) {
                    isValid = false;
                }
            });
            
            return isValid;
        }

        /**
         * Валідація поля
         */
        validateField(event) {
            const field = event.target;
            const value = field.value.trim();
            const fieldName = field.name;
            const isRequired = field.hasAttribute('required');
            
            let isValid = true;
            let errorMessage = '';
            
            // Перевірка обов'язковості
            if (isRequired && !value) {
                isValid = false;
                errorMessage = this.messages.required;
            }
            // Перевірка за типом поля
            else if (value && this.validators[fieldName]) {
                if (!this.validators[fieldName](value)) {
                    isValid = false;
                    errorMessage = this.messages[fieldName] || 'Невірне значення';
                }
            }
            
            this.showFieldValidation(field, isValid, errorMessage);
            return isValid;
        }

        /**
         * Валідація поля при введенні
         */
        validateFieldOnInput(event) {
            const field = event.target;
            
            // Тільки для email та phone валідація на введення
            if (['email', 'phone'].includes(field.name) && field.value.length > 0) {
                this.validateField(event);
            }
        }

        /**
         * Валідація згоди на обробку даних
         */
        validatePrivacyConsent(event) {
            const checkbox = event.target;
            const isChecked = checkbox.checked;
            
            this.showFieldValidation(checkbox, isChecked, 
                isChecked ? '' : 'Необхідно надати згоду на обробку персональних даних');
        }

        /**
         * Показ валідації поля
         */
        showFieldValidation(field, isValid, errorMessage) {
            const errorElement = document.getElementById(`${field.name}-error`);
            const fieldWrapper = field.closest('.form-group');
            
            if (isValid) {
                field.classList.remove('error');
                field.classList.add('valid');
                
                if (fieldWrapper) {
                    fieldWrapper.classList.remove('has-error');
                    fieldWrapper.classList.add('has-success');
                }
                
                if (errorElement) {
                    errorElement.textContent = '';
                    errorElement.style.display = 'none';
                }
            } else {
                field.classList.remove('valid');
                field.classList.add('error');
                
                if (fieldWrapper) {
                    fieldWrapper.classList.remove('has-success');
                    fieldWrapper.classList.add('has-error');
                }
                
                if (errorElement) {
                    errorElement.textContent = errorMessage;
                    errorElement.style.display = 'block';
                } else {
                    // Створення елемента помилки якщо не існує
                    const newErrorElement = document.createElement('div');
                    newErrorElement.id = `${field.name}-error`;
                    newErrorElement.className = 'form-error';
                    newErrorElement.textContent = errorMessage;
                    field.parentNode.appendChild(newErrorElement);
                }
            }
        }

        /**
         * Показ помилок полів
         */
        showFieldErrors(form, errors) {
            Object.keys(errors).forEach(fieldName => {
                const field = form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    this.showFieldValidation(field, false, errors[fieldName]);
                }
            });
        }

        /**
         * Показ помилок форми
         */
        showFormErrors(form) {
            const firstErrorField = form.querySelector('.error');
            if (firstErrorField) {
                firstErrorField.focus();
                firstErrorField.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
        }

        /**
         * Очищення помилок валідації
         */
        clearValidationErrors(form) {
            const errorElements = form.querySelectorAll('.form-error');
            errorElements.forEach(element => {
                element.textContent = '';
                element.style.display = 'none';
            });
            
            const errorFields = form.querySelectorAll('.error');
            errorFields.forEach(field => {
                field.classList.remove('error');
            });
            
            const errorGroups = form.querySelectorAll('.has-error');
            errorGroups.forEach(group => {
                group.classList.remove('has-error');
            });
        }

        /**
         * Показ повідомлення про успіх
         */
        showSuccessMessage(form, message) {
            const successElement = form.querySelector('#form-success');
            if (successElement) {
                successElement.querySelector('span').textContent = message;
                successElement.style.display = 'block';
                
                // Анімація появи
                successElement.style.opacity = '0';
                successElement.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    successElement.style.transition = 'all 0.3s ease';
                    successElement.style.opacity = '1';
                    successElement.style.transform = 'translateY(0)';
                }, 10);
            }
            
            // Приховування через 5 секунд
            setTimeout(() => {
                this.hideMessage(successElement);
            }, 5000);
        }

        /**
         * Показ повідомлення про помилку
         */
        showErrorMessage(form, message) {
            const errorElement = form.querySelector('#form-error');
            if (errorElement) {
                errorElement.querySelector('span').textContent = message;
                errorElement.style.display = 'block';
                
                // Анімація появи
                errorElement.style.opacity = '0';
                errorElement.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    errorElement.style.transition = 'all 0.3s ease';
                    errorElement.style.opacity = '1';
                    errorElement.style.transform = 'translateY(0)';
                }, 10);
            }
        }

        /**
         * Приховування повідомлення
         */
        hideMessage(element) {
            if (!element) return;
            
            element.style.transition = 'all 0.3s ease';
            element.style.opacity = '0';
            element.style.transform = 'translateY(-10px)';
            
            setTimeout(() => {
                element.style.display = 'none';
            }, this.config.animationDuration);
        }

        /**
         * Встановлення стану кнопки відправки
         */
        setSubmitState(button, isSubmitting) {
            if (!button) return;
            
            const textElement = button.querySelector('.btn-text');
            const loaderElement = button.querySelector('.btn-loader');
            
            if (isSubmitting) {
                button.disabled = true;
                button.classList.add('loading');
                
                if (textElement) textElement.style.display = 'none';
                if (loaderElement) loaderElement.style.display = 'inline-flex';
            } else {
                button.disabled = false;
                button.classList.remove('loading');
                
                if (textElement) textElement.style.display = 'inline';
                if (loaderElement) loaderElement.style.display = 'none';
            }
        }

        /**
         * Отримання повідомлення про помилку
         */
        getErrorMessage(error) {
            if (error.name === 'AbortError') {
                return this.messages.timeout;
            } else if (error.message.includes('Failed to fetch')) {
                return this.messages.network;
            } else if (error.message.includes('HTTP 5')) {
                return this.messages.server;
            } else {
                return error.message || this.messages.server;
            }
        }

        /**
         * Відстеження відправки форми
         */
        trackFormSubmission(status, error = null) {
            // Google Analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'form_submit', {
                    event_category: 'Contact Form',
                    event_label: status,
                    value: status === 'success' ? 1 : 0
                });
            }
            
            // Yandex Metrica
            if (typeof ym !== 'undefined') {
                ym(window.yaCounterId, 'reachGoal', `contact_form_${status}`);
            }
            
            // Facebook Pixel
            if (typeof fbq !== 'undefined') {
                fbq('track', 'Contact', {
                    status: status
                });
            }
            
            // Консольне логування для розробки
            console.log(`Contact form ${status}`, error || 'Success');
        }

        /**
         * Debounce функція
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
    }

    /**
     * Ініціалізація після завантаження DOM
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Ініціалізація контактної форми
        new SlavutskaContactForm();
        
        // Додаткові покращення UX
        initFormEnhancements();
    });

    /**
     * Додаткові покращення форми
     */
    function initFormEnhancements() {
        // Автоматичне підставлення коду країни для телефону
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach(input => {
            input.addEventListener('focus', function() {
                if (!this.value && this.placeholder.includes('+380')) {
                    this.value = '+380 ';
                }
            });
        });
        
        // Підказки для полів
        const tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(element => {
            element.addEventListener('mouseenter', showTooltip);
            element.addEventListener('mouseleave', hideTooltip);
        });
        
        // Автозбереження кожні 10 секунд
        setInterval(() => {
            const forms = document.querySelectorAll('.slavutska-contact-form');
            forms.forEach(form => {
                const event = new Event('input', { bubbles: true });
                const firstInput = form.querySelector('input');
                if (firstInput) {
                    firstInput.dispatchEvent(event);
                }
            });
        }, 10000);
    }

    /**
     * Показ підказки
     */
    function showTooltip(event) {
        const element = event.target;
        const tooltipText = element.dataset.tooltip;
        
        if (!tooltipText) return;
        
        const tooltip = document.createElement('div');
        tooltip.className = 'field-tooltip';
        tooltip.textContent = tooltipText;
        tooltip.style.position = 'absolute';
        tooltip.style.zIndex = '1000';
        tooltip.style.backgroundColor = 'var(--color-gray-900)';
        tooltip.style.color = 'var(--color-white)';
        tooltip.style.padding = 'var(--spacing-2) var(--spacing-3)';
        tooltip.style.borderRadius = 'var(--radius-base)';
        tooltip.style.fontSize = 'var(--font-size-sm)';
        tooltip.style.maxWidth = '200px';
        tooltip.style.opacity = '0';
        tooltip.style.transition = 'opacity 0.2s ease';
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        tooltip.style.top = `${rect.bottom + window.scrollY + 5}px`;
        tooltip.style.left = `${rect.left + window.scrollX}px`;
        
        setTimeout(() => {
            tooltip.style.opacity = '1';
        }, 10);
        
        element._tooltip = tooltip;
    }

    /**
     * Приховування підказки
     */
    function hideTooltip(event) {
        const element = event.target;
        if (element._tooltip) {
            element._tooltip.style.opacity = '0';
            setTimeout(() => {
                if (element._tooltip && element._tooltip.parentNode) {
                    element._tooltip.parentNode.removeChild(element._tooltip);
                }
                delete element._tooltip;
            }, 200);
        }
    }

})();