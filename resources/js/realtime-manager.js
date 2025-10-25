/**
 * Real-Time Manager for Enhanced Performance
 * Handles real-time data updates, caching, and performance optimization
 */

class RealTimeManager {
    constructor() {
        this.cache = new Map();
        this.updateInterval = 30000; // 30 seconds
        this.isActive = false;
        this.observers = new Map();
        this.performanceMetrics = {
            requests: 0,
            cacheHits: 0,
            errors: 0,
            avgResponseTime: 0
        };
        
        this.init();
    }

    init() {
        console.log('🚀 RealTimeManager initialized');
        this.setupEventListeners();
        this.startHeartbeat();
    }

    setupEventListeners() {
        // Listen for visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseUpdates();
            } else {
                this.resumeUpdates();
            }
        });

        // Listen for online/offline status
        window.addEventListener('online', () => {
            console.log('🌐 Connection restored');
            this.resumeUpdates();
        });

        window.addEventListener('offline', () => {
            console.log('📴 Connection lost');
            this.pauseUpdates();
        });
    }

    startHeartbeat() {
        if (this.isActive) return;
        
        this.isActive = true;
        console.log('💓 Real-time heartbeat started');
        
        this.heartbeatInterval = setInterval(() => {
            this.performHeartbeat();
        }, this.updateInterval);
    }

    pauseUpdates() {
        this.isActive = false;
        if (this.heartbeatInterval) {
            clearInterval(this.heartbeatInterval);
        }
        console.log('⏸️ Real-time updates paused');
    }

    resumeUpdates() {
        if (!this.isActive) {
            this.startHeartbeat();
            console.log('▶️ Real-time updates resumed');
        }
    }

    async performHeartbeat() {
        if (!this.isActive) return;

        try {
            const startTime = performance.now();
            
            // Update multiple data sources in parallel
            await Promise.all([
                this.updateAbsensiData(),
                this.updateEmployeeData(),
                this.updateMasterData()
            ]);

            const endTime = performance.now();
            this.performanceMetrics.avgResponseTime = 
                (this.performanceMetrics.avgResponseTime + (endTime - startTime)) / 2;

            console.log('💓 Heartbeat completed', {
                responseTime: `${(endTime - startTime).toFixed(2)}ms`,
                cacheSize: this.cache.size
            });

        } catch (error) {
            console.error('❌ Heartbeat error:', error);
            this.performanceMetrics.errors++;
        }
    }

    async updateAbsensiData() {
        const cacheKey = 'absensi_data';
        const cached = this.cache.get(cacheKey);
        
        if (cached && Date.now() - cached.timestamp < 30000) {
            this.performanceMetrics.cacheHits++;
            return cached.data;
        }

        try {
            const response = await fetch('/api/absensi/realtime', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.cache.set(cacheKey, {
                    data: data,
                    timestamp: Date.now()
                });
                
                this.notifyObservers('absensi', data);
                this.performanceMetrics.requests++;
                return data;
            }
        } catch (error) {
            console.error('Error updating absensi data:', error);
            this.performanceMetrics.errors++;
        }
    }

    async updateEmployeeData() {
        const cacheKey = 'employees_data';
        const cached = this.cache.get(cacheKey);
        
        if (cached && Date.now() - cached.timestamp < 60000) {
            this.performanceMetrics.cacheHits++;
            return cached.data;
        }

        try {
            const response = await fetch('/api/employees/realtime', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.cache.set(cacheKey, {
                    data: data,
                    timestamp: Date.now()
                });
                
                this.notifyObservers('employees', data);
                this.performanceMetrics.requests++;
                return data;
            }
        } catch (error) {
            console.error('Error updating employee data:', error);
            this.performanceMetrics.errors++;
        }
    }

    async updateMasterData() {
        const cacheKey = 'master_data';
        const cached = this.cache.get(cacheKey);
        
        if (cached && Date.now() - cached.timestamp < 120000) {
            this.performanceMetrics.cacheHits++;
            return cached.data;
        }

        try {
            const response = await fetch('/api/master/realtime', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.cache.set(cacheKey, {
                    data: data,
                    timestamp: Date.now()
                });
                
                this.notifyObservers('master', data);
                this.performanceMetrics.requests++;
                return data;
            }
        } catch (error) {
            console.error('Error updating master data:', error);
            this.performanceMetrics.errors++;
        }
    }

    subscribe(event, callback) {
        if (!this.observers.has(event)) {
            this.observers.set(event, []);
        }
        this.observers.get(event).push(callback);
    }

    unsubscribe(event, callback) {
        if (this.observers.has(event)) {
            const callbacks = this.observers.get(event);
            const index = callbacks.indexOf(callback);
            if (index > -1) {
                callbacks.splice(index, 1);
            }
        }
    }

    notifyObservers(event, data) {
        if (this.observers.has(event)) {
            this.observers.get(event).forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error('Observer callback error:', error);
                }
            });
        }
    }

    clearCache() {
        this.cache.clear();
        console.log('🗑️ Cache cleared');
    }

    getPerformanceMetrics() {
        return {
            ...this.performanceMetrics,
            cacheSize: this.cache.size,
            isActive: this.isActive,
            observers: Array.from(this.observers.keys())
        };
    }

    // Optimized data fetching with intelligent caching
    async fetchData(url, options = {}) {
        const cacheKey = `fetch_${url}_${JSON.stringify(options)}`;
        const cached = this.cache.get(cacheKey);
        
        if (cached && Date.now() - cached.timestamp < 30000) {
            this.performanceMetrics.cacheHits++;
            return cached.data;
        }

        try {
            const startTime = performance.now();
            const response = await fetch(url, {
                ...options,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache',
                    ...options.headers
                }
            });

            const endTime = performance.now();
            const responseTime = endTime - startTime;

            if (response.ok) {
                const data = await response.json();
                this.cache.set(cacheKey, {
                    data: data,
                    timestamp: Date.now()
                });
                
                this.performanceMetrics.requests++;
                this.performanceMetrics.avgResponseTime = 
                    (this.performanceMetrics.avgResponseTime + responseTime) / 2;
                
                return data;
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        } catch (error) {
            console.error('Fetch error:', error);
            this.performanceMetrics.errors++;
            throw error;
        }
    }

    // Smart refresh for DataTables
    refreshDataTable(tableId) {
        const table = $(`#${tableId}`).DataTable();
        if (table) {
            table.ajax.reload(null, false);
            console.log(`🔄 DataTable ${tableId} refreshed`);
        }
    }

    // Batch operations for better performance
    async batchUpdate(updates) {
        const results = [];
        const batchSize = 5;
        
        for (let i = 0; i < updates.length; i += batchSize) {
            const batch = updates.slice(i, i + batchSize);
            const batchPromises = batch.map(update => this.processUpdate(update));
            
            try {
                const batchResults = await Promise.all(batchPromises);
                results.push(...batchResults);
            } catch (error) {
                console.error('Batch update error:', error);
                results.push({ error: error.message });
            }
        }
        
        return results;
    }

    async processUpdate(update) {
        try {
            const response = await fetch(update.url, {
                method: update.method || 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    ...update.headers
                },
                body: JSON.stringify(update.data)
            });

            if (response.ok) {
                return await response.json();
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        } catch (error) {
            console.error('Update processing error:', error);
            throw error;
        }
    }

    // Performance monitoring
    startPerformanceMonitoring() {
        setInterval(() => {
            const metrics = this.getPerformanceMetrics();
            console.log('📊 Performance Metrics:', metrics);
            
            // Auto-adjust update interval based on performance
            if (metrics.avgResponseTime > 1000) {
                this.updateInterval = Math.min(this.updateInterval + 10000, 120000);
                console.log('⏱️ Increased update interval to', this.updateInterval);
            } else if (metrics.avgResponseTime < 500 && this.updateInterval > 30000) {
                this.updateInterval = Math.max(this.updateInterval - 5000, 30000);
                console.log('⏱️ Decreased update interval to', this.updateInterval);
            }
        }, 60000); // Check every minute
    }
}

// Initialize global real-time manager
window.realTimeManager = new RealTimeManager();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RealTimeManager;
}
