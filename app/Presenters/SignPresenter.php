<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Models\AccountModel;
use App\Models\LoginModel;

/*
 * Presenter obstárávající přihlášení a odhlášení
 */

final class SignPresenter extends Nette\Application\UI\Presenter {

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

    // Vytvoření formuláře pro přihlášení
    protected function createComponentSignInForm(): Form {

        return $this->loginModel->createSignInForm($this);
    }

    // Vyhodnocení přihlášení
    public function signInFormSucceeded(Form $form, \stdClass $data): void {

        $this->loginModel->signInSucceeded($this, $form, $data);
    }

    // Zamezení zobrazení stránky
    public function renderIn() {

        // Je uživatel přihlášený, potom přesměrovat na domovskou stránku
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Article:articles');
        }
    }

    // Příhlášení
    public function actionIn($backlink): void {

        $this->backlink = $backlink;
        $this->template->backpage = $this->backlink;
    }

    // Odhlášení
    public function actionOut(): void {

        $this->loginModel->actionOut($this);
    }
}
