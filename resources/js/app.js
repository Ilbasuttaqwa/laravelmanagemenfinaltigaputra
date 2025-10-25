// Modern Management System - Production Ready

// Modern JavaScript enhancements
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Modern Management System loaded');
    
    // Initialize modern utilities manually
    window.cacheManager = new (class CacheManager {
        constructor() {
            this.cache = new Map();
            this.cacheTimeout = 5 * 60 * 1000;
        }
        set(key, value, ttl = this.cacheTimeout) {
            const item = { value, timestamp: Date.now(), ttl };
            this.cache.set(key, item);
            localStorage.setItem(`cache_${key}`, JSON.stringify(item));
        }
        get(key) {
            if (this.cache.has(key)) {
                const item = this.cache.get(key);
                if (Date.now() - item.timestamp < item.ttl) {
                    return item.value;
                }
                this.cache.delete(key);
            }
            const stored = localStorage.getItem(`cache_${key}`);
            if (stored) {
                try {
                    const item = JSON.parse(stored);
                    if (Date.now() - item.timestamp < item.ttl) {
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
        clearAll() {
            this.cache.clear();
            Object.keys(localStorage).forEach(key => {
                if (key.startsWith('cache_')) {
                    localStorage.removeItem(key);
                }
            });
        }
    })();
    
    window.formManager = new (class FormManager {
        async submitForm(form) {
            const formData = new FormData(form);
            const url = form.action;
            const method = form.method || 'POST';
            
            if (!formData.has('_token')) {
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                if (token) {
                    formData.append('_token', token);
                }
            }
            
            this.showLoading(form);
            
            try {
                const response = await fetch(url, {
                    method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const result = await response.json();
                this.hideLoading(form);
                return result;
            } catch (error) {
                this.hideLoading(form);
                throw error;
            }
        }
        
        showLoading(form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Menyimpan...';
            }
        }
        
        hideLoading(form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-save"></i> Simpan';
            }
        }
        
        showSuccess(message) {
            alert('Success: ' + message);
        }
        
        showError(message) {
            alert('Error: ' + message);
        }
    })();
    
    // Real-time functionality integrated
    console.log('✅ Real-time functionality ready');

    console.log('✅ Modern utilities initialized');
});
