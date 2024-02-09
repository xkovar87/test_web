<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Models\AccountModel;

/*
 * Presenter obstárávající sestavení dat pro administraci již existujícího účtu
 */

final class AccountPresenter extends Nette\Application\UI\Presenter {

    private AccountModel $accountModel;

    /** @persistent */
    public $backlink = '';
    public $sortClicked = false;

    // Připojení databáze a modelů
    public function __construct(AccountModel $accountModel) {

        $this->accountModel = $accountModel;
    }

    // Zamezení zobrazení stránky
    public function startup(): void {

        parent::startup();

        // Není uživatel přihlášený, potom přesměrovat na přihlášení
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
        // Pokud je uživatel přihlášen a byl zárověň smazán z databáze, bude odhlášen
        $this->accountModel->verifyUser($this);
    }

    // Zobrazení článků v administraci
    public function renderArticles(): void {

        if ($this->sortClicked) {
            $this->template->articles = $this->accountModel->getArticles($this)->order('created_at ASC');
            $this->template->b = false;
            $this->template->sort = "nejnovějších";
        } else {
            $this->template->articles = $this->accountModel->getArticles($this)->order('created_at DESC');
            $this->template->b = true;
            $this->template->sort = "nejstarších";
        }
    }

    // Nastavení odkazu
    public function actionArticles($backlink): void {

        $this->backlink = $backlink;
        $this->template->backpage = $this->backlink;
    }

    /** @ajax */
    public function handleChangeStatus(bool $b) {

        $this->sortClicked = $b;

        // Překreslení
        $this->redrawControl('b');
        $this->redrawControl('articleList');
    }
}
