<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Models\AccountModel;
use App\Models\LoginModel;

/*
 * Presenter obstárávající registraci nového účtu
 */

final class RegistrationPresenter extends Nette\Application\UI\Presenter {

    private LoginModel $loginModel;
    private AccountModel $accountModel;

    /** @persistent */
    public $backlink = '';

    // Připojení databáze a modelů
    public function __construct(LoginModel $loginModel, AccountModel $accountModel) {

        $this->loginModel = $loginModel;
        $this->accountModel = $accountModel;
    }

    // Pokud je uživatel přihlášen a byl zárověň smazán z databáze, bude odhlášen
    public function startup(): void {

        parent::startup();

        $this->accountModel->verifyUser($this);
    }

    // Vytvoření formuláře pro registraci
    protected function createComponentRegisterInForm(): Form {

        return $this->loginModel->createRegisterInForm($this);
    }

    // Vyhodnocení registrace
    public function registerInFormSucceeded(Form $form, \stdClass $data): void {

        $this->loginModel->registerInSucceeded($this, $form, $data);
    }

    // Zamezení zobrazení stránky
    public function renderForm() {

        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Article:articles');
        }
    }

    // Registrace
    public function actionForm($backlink): void {

        $this->backlink = $backlink;
        $this->template->backpage = $this->backlink;
    }
}
