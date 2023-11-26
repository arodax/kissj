<?php

declare(strict_types=1);

namespace kissj\Skautis;

use kissj\AbstractController;
use kissj\Event\EventRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SkautisController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly SkautisService $skautisService,
    ) {
    }

    /**
     * catch POST data from skautis, login user and redirect to dashboard
     */
    public function redirectFromSkautis(Request $request, Response $response): Response
    {
        $this->skautisService->saveDataFromPost((array)$request->getParsedBody());
        $this->flashMessages->info($this->translator->trans('flash.info.redirectedFromSkautis'));

        $eventSlug = $this->getParameterFromQuery($request, 'ReturnUrl');
        $event = $this->eventRepository->getOneBy(['slug' => $eventSlug]);

        if (!$this->skautisService->isUserLoggedIn()) {
            $this->flashMessages->error($this->translator->trans('flash.error.skautisUserNotLoggedIn'));
            
            return $this->redirect($request, $response, 'landing');
        }

        $skautisUserData = $this->skautisService->getUserDetailsFromLoggedSkautisUser();
        if ($skautisUserData === null) {
            $this->flashMessages->error($this->translator->trans('flash.error.skautisUserError'));

            return $this->redirect($request, $response, 'landing');
        }

        $this->skautisService->getOrCreateAndLogInSkautisUser($skautisUserData, $event);

        return $this->redirect($request, $response, 'getDashboard', ['eventSlug' => $eventSlug]);
    }
}
