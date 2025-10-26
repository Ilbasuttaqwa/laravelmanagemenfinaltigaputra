<?php

namespace App\Listeners;

use App\Services\SmartCacheService;
use Illuminate\Database\Eloquent\Model;

class CacheEventListener
{
    /**
     * Handle model created event
     */
    public function handleModelCreated(Model $model)
    {
        SmartCacheService::clearModelCache($model);
    }

    /**
     * Handle model updated event
     */
    public function handleModelUpdated(Model $model)
    {
        SmartCacheService::clearModelCache($model);
    }

    /**
     * Handle model deleted event
     */
    public function handleModelDeleted(Model $model)
    {
        SmartCacheService::clearModelCache($model);
    }
}
