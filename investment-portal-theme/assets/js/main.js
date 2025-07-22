// FILE: assets/js/main.js

/**
 * Slavuta Invest Theme - Main JavaScript
 * 
 * This file contains all JavaScript functionality for the theme,
 * utilizing modern ES6+ syntax and vanilla JavaScript.
 */

// Import Swiper if using Vite
import Swiper from 'swiper';
import { Navigation, Pagination, Thumbs, Autoplay } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/thumbs';

// Import styles
import '../scss/main.scss';

// -----------------------------------------------------------------------------
// DOM Ready Function
// -----------------------------------------------------------------------------

const domReady = (callback) => {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', callback);
  } else {
    callback();
  }
};

// -----------------------------------------------------------------------------
// Header Functionality
// -----------------------------------------------------------------------------

class StickyHeader {
  constructor() {
    this.header = document.getElementById('masthead');
    this.lastScrollTop = 0;
    this.scrollThreshold = 100;
    
    if (!this.header) return;
    
    this.init();
  }
  
  init() {
    let ticking = false;
    
    window.addEventListener('scroll', () => {
      if (!ticking) {
        window.requestAnimationFrame(() => {
          this.handleScroll();
          ticking = false;
        });
        ticking = true;
      }
    });
  }
  
  handleScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    // Add/remove scrolled class
    if (scrollTop > this.scrollThreshold) {
      this.header.classList.add('is-scrolled');
    } else {
      this.header.classList.remove('is-scrolled');
    }
    
    // Hide/show on scroll
    if (scrollTop > this.lastScrollTop && scrollTop > this.scrollThreshold) {
      this.header.classList.add('is-hidden');
    } else {
      this.header.classList.remove('is-hidden');
    }
    
    this.lastScrollTop = scrollTop;
  }
}

// -----------------------------------------------------------------------------
// Mobile Navigation
// -----------------------------------------------------------------------------

class MobileNavigation {
  constructor() {
    this.toggle = document.querySelector('.menu-toggle');
    this.menu = document.querySelector('.primary-menu-container');
    this.body = document.body;
    
    if (!this.toggle || !this.menu) return;
    
    this.init();
  }
  
  init() {
    this.toggle.addEventListener('click', () => this.toggleMenu());
    
    // Close menu on escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.isOpen()) {
        this.closeMenu();
      }
    });
    
    // Close menu on outside click
    document.addEventListener('click', (e) => {
      if (this.isOpen() && !this.menu.contains(e.target) && !this.toggle.contains(e.target)) {
        this.closeMenu();
      }
    });
  }
  
  toggleMenu() {
    if (this.isOpen()) {
      this.closeMenu();
    } else {
      this.openMenu();
    }
  }
  
  openMenu() {
    this.toggle.setAttribute('aria-expanded', 'true');
    this.menu.classList.add('is-active');
    this.body.classList.add('menu-open');
    
    // Focus management
    const firstMenuItem = this.menu.querySelector('a');
    if (firstMenuItem) {
      firstMenuItem.focus();
    }
  }
  
  closeMenu() {
    this.toggle.setAttribute('aria-expanded', 'false');
    this.menu.classList.remove('is-active');
    this.body.classList.remove('menu-open');
    this.toggle.focus();
  }
  
  isOpen() {
    return this.menu.classList.contains('is-active');
  }
}

// -----------------------------------------------------------------------------
// Search Toggle
// -----------------------------------------------------------------------------

class SearchToggle {
  constructor() {
    this.toggleButton = document.querySelector('.search-toggle');
    this.searchForm = document.querySelector('.header-search');
    this.closeButton = document.querySelector('.search-close');
    this.searchInput = document.querySelector('.header-search .search-field');
    
    if (!this.toggleButton || !this.searchForm) return;
    
    this.init();
  }
  
  init() {
    this.toggleButton.addEventListener('click', () => this.openSearch());
    
    if (this.closeButton) {
      this.closeButton.addEventListener('click', () => this.closeSearch());
    }
    
    // Close on escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.isOpen()) {
        this.closeSearch();
      }
    });
  }
  
  openSearch() {
    this.searchForm.classList.add('is-active');
    this.searchForm.setAttribute('aria-hidden', 'false');
    
    // Focus input after animation
    setTimeout(() => {
      if (this.searchInput) {
        this.searchInput.focus();
      }
    }, 300);
  }
  
  closeSearch() {
    this.searchForm.classList.remove('is-active');
    this.searchForm.setAttribute('aria-hidden', 'true');
    this.toggleButton.focus();
  }
  
  isOpen() {
    return this.searchForm.classList.contains('is-active');
  }
}

// -----------------------------------------------------------------------------
// Language Switcher
// -----------------------------------------------------------------------------

class LanguageSwitcher {
  constructor() {
    this.switcher = document.querySelector('.language-switcher');
    this.toggle = document.querySelector('.language-switcher-toggle');
    this.menu = document.querySelector('.language-switcher-menu');
    
    if (!this.switcher || !this.toggle || !this.menu) return;
    
    this.init();
  }
  
  init() {
    this.toggle.addEventListener('click', (e) => {
      e.stopPropagation();
      this.toggleMenu();
    });
    
    // Close on outside click
    document.addEventListener('click', () => {
      if (this.isOpen()) {
        this.closeMenu();
      }
    });
  }
  
  toggleMenu() {
    const isOpen = this.toggle.getAttribute('aria-expanded') === 'true';
    
    if (isOpen) {
      this.closeMenu();
    } else {
      this.openMenu();
    }
  }
  
  openMenu() {
    this.toggle.setAttribute('aria-expanded', 'true');
    this.menu.classList.add('is-active');
  }
  
  closeMenu() {
    this.toggle.setAttribute('aria-expanded', 'false');
    this.menu.classList.remove('is-active');
  }
  
  isOpen() {
    return this.menu.classList.contains('is-active');
  }
}

// -----------------------------------------------------------------------------
// Swiper Sliders
// -----------------------------------------------------------------------------

class SliderManager {
  constructor() {
    this.initProjectsSlider();
    this.initGallerySliders();
  }
  
  initProjectsSlider() {
    const projectsSlider = document.querySelector('.projects-slider');
    
    if (!projectsSlider) return;
    
    new Swiper(projectsSlider, {
      modules: [Navigation, Pagination, Autoplay],
      slidesPerView: 1,
      spaceBetween: 24,
      loop: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      navigation: {
        nextEl: '.projects-slider-wrapper .swiper-button-next',
        prevEl: '.projects-slider-wrapper .swiper-button-prev',
      },
      pagination: {
        el: '.projects-slider-wrapper .swiper-pagination',
        clickable: true,
      },
      breakpoints: {
        640: {
          slidesPerView: 2,
        },
        1024: {
          slidesPerView: 3,
        },
        1280: {
          slidesPerView: 4,
        },
      },
    });
  }
  
  initGallerySliders() {
    // Gallery thumbs
    const galleryThumbs = document.querySelector('.gallery-thumbs');
    let thumbsSwiper = null;
    
    if (galleryThumbs) {
      thumbsSwiper = new Swiper(galleryThumbs, {
        modules: [Navigation],
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
        breakpoints: {
          640: {
            slidesPerView: 5,
          },
          768: {
            slidesPerView: 6,
          },
          1024: {
            slidesPerView: 8,
          },
        },
      });
    }
    
    // Main gallery
    const galleryMain = document.querySelector('.gallery-main');
    
    if (galleryMain) {
      new Swiper(galleryMain, {
        modules: [Navigation, Pagination, Thumbs],
        spaceBetween: 10,
        navigation: {
          nextEl: '.gallery-main .swiper-button-next',
          prevEl: '.gallery-main .swiper-button-prev',
        },
        thumbs: thumbsSwiper ? {
          swiper: thumbsSwiper,
        } : null,
      });
    }
  }
}

// -----------------------------------------------------------------------------
// Show More Functionality
// -----------------------------------------------------------------------------

class ShowMore {
  constructor() {
    this.containers = document.querySelectorAll('[data-show-more-container]');
    
    if (!this.containers.length) return;
    
    this.init();
  }
  
  init() {
    this.containers.forEach(container => {
      const button = container.parentElement.querySelector('[data-show-more-button]');
      const items = container.querySelectorAll('[data-show-more-item]');
      
      if (!button || !items.length) return;
      
      button.addEventListener('click', () => {
        this.toggleItems(container, button, items);
      });
    });
  }
  
  toggleItems(container, button, items) {
    const isExpanded = container.classList.contains('is-expanded');
    const buttonText = button.querySelector('.button-text');
    const buttonIcon = button.querySelector('.button-icon');
    
    if (isExpanded) {
      // Collapse
      container.classList.remove('is-expanded');
      items.forEach(item => item.classList.add('hidden-item'));
      buttonText.textContent = button.dataset.showText;
      buttonIcon.style.transform = 'rotate(0deg)';
    } else {
      // Expand
      container.classList.add('is-expanded');
      items.forEach(item => item.classList.remove('hidden-item'));
      buttonText.textContent = button.dataset.hideText;
      buttonIcon.style.transform = 'rotate(180deg)';
    }
  }
}

// -----------------------------------------------------------------------------
// Google Maps Integration
// -----------------------------------------------------------------------------

class MapManager {
  constructor() {
    this.maps = document.querySelectorAll('.acf-map');
    
    if (!this.maps.length) return;
    
    // Load Google Maps API if not already loaded
    if (!window.google || !window.google.maps) {
      this.loadGoogleMapsAPI();
    } else {
      this.initMaps();
    }
  }
  
  loadGoogleMapsAPI() {
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMaps`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
    
    // Make initMaps globally available
    window.initMaps = () => this.initMaps();
  }
  
  initMaps() {
    this.maps.forEach(mapElement => {
      const markers = mapElement.querySelectorAll('.marker');
      const zoom = parseInt(mapElement.dataset.zoom) || 14;
      
      if (!markers.length) return;
      
      // Create map
      const map = new google.maps.Map(mapElement, {
        zoom: zoom,
        center: { lat: 0, lng: 0 },
        mapTypeId: google.maps.MapTypeId.ROADMAP,
      });
      
      // Add markers
      const bounds = new google.maps.LatLngBounds();
      
      markers.forEach(marker => {
        const lat = parseFloat(marker.dataset.lat);
        const lng = parseFloat(marker.dataset.lng);
        const position = { lat, lng };
        
        const mapMarker = new google.maps.Marker({
          position: position,
          map: map,
        });
        
        // Info window
        if (marker.innerHTML) {
          const infoWindow = new google.maps.InfoWindow({
            content: marker.innerHTML,
          });
          
          mapMarker.addListener('click', () => {
            infoWindow.open(map, mapMarker);
          });
        }
        
        bounds.extend(position);
      });
      
      // Center map
      if (markers.length === 1) {
        map.setCenter(bounds.getCenter());
      } else {
        map.fitBounds(bounds);
      }
    });
  }
}

// -----------------------------------------------------------------------------
// Smooth Scroll
// -----------------------------------------------------------------------------

class SmoothScroll {
  constructor() {
    this.links = document.querySelectorAll('a[href^="#"]');
    
    if (!this.links.length) return;
    
    this.init();
  }
  
  init() {
    this.links.forEach(link => {
      link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        
        if (href === '#' || href === '#0') return;
        
        const target = document.querySelector(href);
        
        if (target) {
          e.preventDefault();
          
          const headerHeight = document.querySelector('.site-header').offsetHeight;
          const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;
          
          window.scrollTo({
            top: targetPosition,
            behavior: 'smooth',
          });
          
          // Update URL
          history.pushState(null, null, href);
        }
      });
    });
  }
}

// -----------------------------------------------------------------------------
// Form Validation
// -----------------------------------------------------------------------------

class FormValidator {
  constructor() {
    this.forms = document.querySelectorAll('.contact-form, .newsletter-form');
    
    if (!this.forms.length) return;
    
    this.init();
  }
  
  init() {
    this.forms.forEach(form => {
      form.addEventListener('submit', (e) => {
        if (!this.validateForm(form)) {
          e.preventDefault();
        }
      });
      
      // Real-time validation
      const inputs = form.querySelectorAll('input, textarea, select');
      inputs.forEach(input => {
        input.addEventListener('blur', () => {
          this.validateField(input);
        });
      });
    });
  }
  
  validateForm(form) {
    const inputs = form.querySelectorAll('input, textarea, select');
    let isValid = true;
    
    inputs.forEach(input => {
      if (!this.validateField(input)) {
        isValid = false;
      }
    });
    
    return isValid;
  }
  
  validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    const required = field.hasAttribute('required');
    
    // Remove previous error
    this.removeError(field);
    
    // Required field check
    if (required && !value) {
      this.addError(field, 'Це поле є обов\'язковим');
      return false;
    }
    
    // Email validation
    if (type === 'email' && value) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        this.addError(field, 'Введіть дійсну email адресу');
        return false;
      }
    }
    
    // Phone validation
    if (type === 'tel' && value) {
      const phoneRegex = /^[\d\s\-\+\(\)]+$/;
      if (!phoneRegex.test(value)) {
        this.addError(field, 'Введіть дійсний номер телефону');
        return false;
      }
    }
    
    return true;
  }
  
  addError(field, message) {
    field.classList.add('error');
    
    const error = document.createElement('span');
    error.className = 'error-message';
    error.textContent = message;
    
    field.parentElement.appendChild(error);
  }
  
  removeError(field) {
    field.classList.remove('error');
    
    const error = field.parentElement.querySelector('.error-message');
    if (error) {
      error.remove();
    }
  }
}

// -----------------------------------------------------------------------------
// Initialize Everything
// -----------------------------------------------------------------------------

domReady(() => {
  // Initialize components
  new StickyHeader();
  new MobileNavigation();
  new SearchToggle();
  new LanguageSwitcher();
  new SliderManager();
  new ShowMore();
  new MapManager();
  new SmoothScroll();
  new FormValidator();
  
  // Initialize AOS if needed
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 800,
      once: true,
      offset: 100,
    });
  }
  
  // Lazy loading for images
  if ('loading' in HTMLImageElement.prototype) {
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
      img.src = img.dataset.src || img.src;
    });
  } else {
    // Fallback for browsers that don't support lazy loading
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
    document.body.appendChild(script);
  }
});