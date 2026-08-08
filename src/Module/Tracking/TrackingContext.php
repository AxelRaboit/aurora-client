<?php

declare(strict_types=1);

namespace App\Module\Tracking;

use Aurora\Core\Module\Service\ModuleAccessChecker;

/**
 * Reads the module's own toggle — reference example.
 *
 * Goes through `ModuleAccessChecker`, never `SettingRepository::getBoolean()`.
 * The checker answers the question that actually matters: enabled globally
 * *and* not masked for the current user. Reading the setting straight would
 * see only the global half and quietly hand the module to users an
 * administrator has switched it off for.
 */
final readonly class TrackingContext
{
    public function __construct(private ModuleAccessChecker $moduleAccessChecker) {}

    public function isBackendEnabled(): bool
    {
        return $this->moduleAccessChecker->isEnabled(TrackingModule::TOGGLE_KEY);
    }
}
