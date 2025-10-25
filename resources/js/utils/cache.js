/**
 * Modern Cache Management Utilities
 * Handles browser caching, network optimization, and real-time updates
 */

class CacheManager {
    constructor() {
        this.cache = new Map();
        this.cacheTimeout = 5 * 60 * 1000; // 5 minutes
        this.networkTimeout = 10000; // 10 seconds
    }

    /**
     * Set cache with timestamp
     */
    set(key, value, ttl = this.cacheTimeout) {
        const item = {
            value,
            timestamp: Date.now(),
            ttl
        };
        this.cache.set(key, item);
        localStorage.setItem(`cache_${key}`, JSON.stringify(item));
    }

    /**
     * Get cache with validation
     */
    get(key) {
        // Check memory cache first
        if (this.cache.has(key)) {
            const item = this.cache.get(key);
            if (this.isValid(item)) {
                return item.value;
            }
            this.cache.delete(key);
        }

        // Check localStorage
        const stored = localStorage.getItem(`cache_${key}`);
        if (stored) {
            try {
                const item = JSON.parse(stored);
                if (this.isValid(item)) {
                    this.cache.set(key, item);
                    return item.value;
                }
                localStorage.removeItem(`cache_${key}`);
            } catch (e) {
                localStorage.removeItem(`cache_${key}`);
            }
        }

        return null;
    }

    /**
     * Check if cache item is valid
     */
    isValid(item) {
        return Date.now() - item.timestamp < item.ttl;
    }

    /**
     * Clear specific cache
     */
    clear(key) {
        this.cache.delete(key);
        localStorage.removeItem(`cache_${key}`);
    }

    /**
     * Clear all cache
     */
    clearAll() {
        this.cache.clear();
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith('cache_')) {
                localStorage.removeItem(key);
            }
        });
    }
}

/**
 * Network Manager for optimized requests
 */
class NetworkManager {
    constructor() {
        this.pendingRequests = new Map();
        this.retryAttempts = 3;
        this.retryDelay = 1000;
    }

    /**
     * Make optimized request with caching and retry
     */
    async request(url, options = {}) {
        const cacheKey = `request_${url}_${JSON.stringify(options)}`;
        const cacheManager = new CacheManager();
        
        // Check cache first
        const cached = cacheManager.get(cacheKey);
        if (cached && !options.forceRefresh) {
            return cached;
        }

        // Check if request is already pending
        if (this.pendingRequests.has(cacheKey)) {
            return this.pendingRequests.get(cacheKey);
        }

        const requestPromise = this.makeRequest(url, options);
        this.pendingRequests.set(cacheKey, requestPromise);

        try {
            const result = await requestPromise;
            cacheManager.set(cacheKey, result);
            return result;
        } finally {
            this.pendingRequests.delete(cacheKey);
        }
    }

    /**
     * Make actual request with retry logic
     */
    async makeRequest(url, options = {}) {
        let lastError;
        
        for (let attempt = 1; attempt <= this.retryAttempts; attempt++) {
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), this.networkTimeout);

                const response = await fetch(url, {
                    ...options,
                    signal: controller.signal,
                    headers: {
                        'Cache-Control': 'no-cache',
                        'Pragma': 'no-cache',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...options.headers
                    }
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                return data;
            } catch (error) {
                lastError = error;
                if (attempt < this.retryAttempts) {
                    await this.delay(this.retryDelay * attempt);
                }
            }
        }

        throw lastError;
    }

    /**
     * Delay utility
     */
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

/**
 * Form Manager for enhanced form handling
 */
class FormManager {
    constructor() {
        this.networkManager = new NetworkManager();
        this.cacheManager = new CacheManager();
    }

    /**
     * Enhanced form submission with progress tracking
     */
    async submitForm(form, options = {}) {
        const formData = new FormData(form);
        const url = form.action;
        const method = form.method || 'POST';

        // Add CSRF token if not present
        if (!formData.has('_token')) {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            if (token) {
                formData.append('_token', token);
            }
        }

        // Show loading state
        this.showLoading(form);

        try {
            const result = await this.networkManager.request(url, {
                method,
                body: formData,
                forceRefresh: true
            });

            this.hideLoading(form);
            return result;
        } catch (error) {
            this.hideLoading(form);
            this.showError(error.message);
            throw error;
        }
    }

    /**
     * Show loading state
     */
    showLoading(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Menyimpan...';
        }
    }

    /**
     * Hide loading state
     */
    hideLoading(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save"></i> Simpan';
        }
    }

    /**
     * Show error message
     */
    showError(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonText: 'OK'
            });
        } else {
            alert('Error: ' + message);
        }
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert('Success: ' + message);
        }
    }
}

/**
 * Real-time Data Manager
 */
class RealTimeManager {
    constructor() {
        this.cacheManager = new CacheManager();
        this.refreshInterval = 30000; // 30 seconds
        this.intervals = new Map();
    }

    /**
     * Start real-time refresh for specific data
     */
    startRefresh(key, refreshFunction, interval = this.refreshInterval) {
        this.stopRefresh(key);
        
        const intervalId = setInterval(async () => {
            try {
                const data = await refreshFunction();
                this.cacheManager.set(key, data);
                this.triggerUpdate(key, data);
            } catch (error) {
                console.error(`Real-time refresh error for ${key}:`, error);
            }
        }, interval);

        this.intervals.set(key, intervalId);
    }

    /**
     * Stop real-time refresh
     */
    stopRefresh(key) {
        if (this.intervals.has(key)) {
            clearInterval(this.intervals.get(key));
            this.intervals.delete(key);
        }
    }

    /**
     * Trigger update event
     */
    triggerUpdate(key, data) {
        const event = new CustomEvent('dataUpdated', {
            detail: { key, data }
        });
        document.dispatchEvent(event);
    }

    /**
     * Clear all intervals
     */
    clearAll() {
        this.intervals.forEach(interval => clearInterval(interval));
        this.intervals.clear();
    }
}

// Export utilities
window.CacheManager = CacheManager;
window.NetworkManager = NetworkManager;
window.FormManager = FormManager;
window.RealTimeManager = RealTimeManager;

// Initialize global instances
window.cacheManager = new CacheManager();
window.networkManager = new NetworkManager();
window.formManager = new FormManager();
window.realTimeManager = new RealTimeManager();
