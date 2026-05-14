<?php

namespace App\Services\Audit;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Request;

class AuditTrailService
{
    /**
     * Log an action to the audit trail
     */
    public static function log(
        string $actionType,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        try {
            AuditTrail::create([
                'user_id' => auth()->id() ?? 1, // Fallback to 1 if system/console
                'action_type' => $actionType,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => Request::ip() ?? '127.0.0.1',
                'description' => $description,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to write audit trail: ' . $e->getMessage());
        }
    }
}
