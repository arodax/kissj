<?php

namespace kissj\Participant\Ist;

use kissj\AbstractController;
use kissj\Payment\PaymentService;
use kissj\User\User;
use Slim\Http\Request;
use Slim\Http\Response;

class IstController extends AbstractController {
    private $istService;
    private $paymentService;

    public function __construct(IstService $istService, PaymentService $paymentService) {
        $this->istService = $istService;
        $this->paymentService = $paymentService;
    }

    public function showDashboard(Request $request, Response $response, User $user) {
        //$user = $request->getAttribute('user');
        $ist = $this->istService->getIst($user);
        $possibleOnePayment = $this->paymentService->findLastPayment($ist);

        return $this->view->render($response, 'dashboard-ist.twig',
            ['user' => $user, 'ist' => $ist, 'payment' => $possibleOnePayment]);
    }

    public function showDetailsChangeable(Request $request, Response $response) {
        $istDetails = $this->istService->getIst($request->getAttribute('user'));

        return $this->view->render($response, 'changeDetails-ist.twig',
            ['istDetails' => $istDetails]);
    }

    public function changeDetails(Request $request, Response $response) {
        $params = $request->getParams();
        if ($this->istService->isIstDetailsValid(
            $params['firstName'] ?? null,
            $params['lastName'] ?? null,
            $params['allergies'] ?? null,
            $params['birthDate'] ?? null,
            $params['birthPlace'] ?? null,
            $params['country'] ?? null,
            $params['gender'] ?? null,
            $params['permanentResidence'] ?? null,
            $params['scoutUnit'] ?? null,
            $params['telephoneNumber'] ?? null,
            $params['email'] ?? null,
            $params['foodPreferences'] ?? null,
            $params['cardPassportNumber'] ?? null,
            $params['notes'] ?? null,

            $params['workPreferences'] ?? null,
            $params['skills'] ?? null,
            $params['languages'] ?? null,
            $params['arrivalDate'] ?? null,
            $params['leavingDate'] ?? null,
            $params['carRegistrationPlate'] ?? null)) {

            $this->istService->editIstInfo(
                $this->istService->getIst($request->getAttribute('user')),
                $params['firstName'] ?? null,
                $params['lastName'] ?? null,
                $params['allergies'] ?? null,
                $params['birthDate'] ?? null,
                $params['birthPlace'] ?? null,
                $params['country'] ?? null,
                $params['gender'] ?? null,
                $params['permanentResidence'] ?? null,
                $params['scoutUnit'] ?? null,
                $params['telephoneNumber'] ?? null,
                $params['email'] ?? null,
                $params['foodPreferences'] ?? null,
                $params['cardPassportNumber'] ?? null,
                $params['notes'] ?? null,

                $params['workPreferences'] ?? null,
                $params['skills'] ?? null,
                $params['languages'] ?? null,
                $params['arrivalDate'] ?? null,
                $params['leavingDate'] ?? null,
                $params['carRegistrationPlate'] ?? null);

            $this->flashMessages->success('Údaje úspěšně uloženy');

            return $response->withRedirect($this->router->pathFor('ist-dashboard'));
        }

        $this->flashMessages->warning('Některé údaje nebyly validní - prosím zkus úpravu údajů znovu.');

        return $response->withRedirect($this->router->pathFor('ist-changeDetails'));
    }

    public function showCloseRegistration(Request $request, Response $response) {
        $ist = $this->istService->getIst($request->getAttribute('user'));
        $validRegistration = $this->istService->isCloseRegistrationValid($ist); // call because of warnings
        if ($validRegistration) {
            return $this->view->render($response, 'closeRegistration-ist.twig');
        }

        return $response->withRedirect($this->router->pathFor('ist-dashboard'));
    }

    public function closeRegistration(Request $request, Response $response) {
        $ist = $this->istService->getIst($request->getAttribute('user'));
        if ($this->istService->isCloseRegistrationValid($ist)) {
            $this->istService->closeRegistration($ist);
            $this->flashMessages->success('Registrace úspěšně uzavřena, čeká na schválení');
            $this->logger->info('Closing registration for IST with ID '.$ist->id);

            return $response->withRedirect($this->router->pathFor('ist-dashboard'));
        }

        $this->flashMessages->error('Registraci ještě nelze uzavřít');

        return $response->withRedirect($this->router->pathFor('ist-dashboard'));
    }

    public function approveIst(Request $request, Response $response, int $istId) {
        $ist = $this->istService->getIstFromId($istId);
        $this->istService->approveIst($ist);
        $payment = $this->paymentService->createNewPayment($ist->user->role);
        $this->istService->sendPaymentByMail($payment, $ist);
        $this->flashMessages->success('Člen IST schválen, platba vygenerována a mail odeslán');
        $this->logger->info('Approved registration for IST with ID '.$ist->id);

        return $response->withRedirect($this->router->pathFor('admin-approving'));
    }

    public function showOpenIst(Request $request, Response $response) {
        $ist = $this->istService->getIstFromId($args['istId']);

        return $this->view->render($response, 'admin/openIst.twig', ['ist' => $ist]);
    }

    public function openIst(Request $request, Response $response) {
        $ist = $this->istService->getIstFromId($args['istId']);
        $this->istService->openIst($ist);
        $reason = $request->getParsedBodyParam('reason');
        $this->istService->sendDenialMail($ist, $reason);
        $this->flashMessages->info('Člen IST zamítnut, email o zamítnutí poslán');
        $this->logger->info('Denied registration for IST with ID '.$ist->id.' with reason: '.$reason);

        return $response->withRedirect($this->router->pathFor('admin-approving'));
    }
}