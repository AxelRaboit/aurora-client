<?php

declare(strict_types=1);

namespace App\Module\Tracking;

use App\Module\Ged\DocumentCategory\Entity\DocumentCategory;
use Aurora\Core\Module\Contract\ModuleInterface;
use Aurora\Core\Module\Contract\ModuleToggleProviderInterface;
use Aurora\Core\Module\Nav\NavItem;
use Aurora\Core\Module\Nav\NavPermission;
use Aurora\Core\Module\Nav\NavSection;
use Aurora\Core\Module\Toggle\ModuleToggle;

/**
 * A self-contained client module — reference example.
 *
 * Nothing here extends an Aurora class. This is the other half of the client
 * story: {@see DocumentCategory} shows
 * how to *extend* something Aurora owns, this shows how to add something
 * Aurora knows nothing about and still have it appear in the admin, respect
 * privileges, and be switchable per user.
 *
 * It is picked up with no registration anywhere: `config/services.yaml`
 * mirrors aurora-core's `_instanceof` block, which tags any
 * `ModuleInterface` with `aurora.module`. That mirror is required —
 * `_instanceof` is scoped to the file that declares it and does not cross
 * bundle boundaries, so without it this class would be a plain service and
 * the module would silently never exist.
 *
 * Both interfaces live under `Aurora\Core\Module\Contract\`.
 */
final readonly class TrackingModule implements ModuleInterface, ModuleToggleProviderInterface
{
    public const string TOGGLE_KEY = 'app_tracking_backend';

    public function __construct(private TrackingContext $trackingContext) {}

    public function getId(): string
    {
        return 'tracking';
    }

    public function getPermissions(): array
    {
        return [
            new NavPermission('tracking.dashboard.view'),
        ];
    }

    public function getNavSections(): array
    {
        // Returning an empty array is how a disabled module disappears from
        // the sidemenu. The controller re-checks: hiding a link is not access
        // control, and a bookmarked URL still has to be refused.
        if (!$this->trackingContext->isBackendEnabled()) {
            return [];
        }

        return [
            new NavSection(
                id: 'tracking',
                items: [
                    new NavItem(
                        route: 'backend_tracking_dashboard',
                        labelKey: 'backend.nav.tracking_dashboard',
                        icon: 'chart-line',
                        // Never null on a NavItem: without it the entry shows
                        // for everyone, including users the privilege denies.
                        requiredPrivilege: 'tracking.dashboard.view',
                    ),
                ],
                priority: 500,
            ),
        ];
    }

    public function getCatalogNavSections(): array
    {
        return [];
    }

    public function getToggles(): array
    {
        return [
            new ModuleToggle(
                key: self::TOGGLE_KEY,
                labelKey: 'backend.modules.tracking',
                descriptionKey: 'backend.modules.tracking_description',
                // `moduleId` is what surfaces this in the per-user module
                // access picker. Exactly one toggle per module carries it.
                moduleId: 'tracking',
            ),
        ];
    }
}
