<?php

declare(strict_types=1);

namespace App\Module\Tracking\Controller\Backend;

use App\Module\Tracking\TrackingContext;
use Aurora\Core\Enum\HttpMethodEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Backend screen of the client module — reference example.
 *
 * Two gates, deliberately, because they answer different questions:
 *
 * - `#[IsGranted]` asks whether this user holds the privilege. It is what
 *   makes a bookmarked URL refuse a user the sidemenu already hid.
 * - The context check asks whether the module is switched on at all. A user
 *   can hold `tracking.dashboard.view` on an installation where tracking is
 *   off, and a 404 is the honest answer there — the screen does not exist,
 *   rather than existing and being forbidden.
 *
 * The template lives under the project's `templates/`, not under
 * `src/Module/Tracking/templates/`. Aurora registers the co-located path only
 * for module names it ships itself — the glob runs over aurora-core's own
 * `src/Module/*` — so a purely client-side module has no Twig namespace of
 * its own and uses Symfony's default path.
 */
#[Route('/backend/tracking', name: 'backend_tracking_dashboard')]
#[IsGranted('tracking.dashboard.view')]
final class TrackingController extends AbstractController
{
    public function __construct(private readonly TrackingContext $trackingContext) {}

    #[Route('', name: '', methods: [HttpMethodEnum::Get->value])]
    public function index(): Response
    {
        if (!$this->trackingContext->isBackendEnabled()) {
            throw $this->createNotFoundException('Tracking is disabled on this installation.');
        }

        return $this->render('tracking/index.html.twig');
    }
}
