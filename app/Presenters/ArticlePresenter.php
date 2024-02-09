<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Models\AccountModel;
use App\Models\ArticleModel;

/*
 * Presenter obstárávající sestavení dat pro nejnovější publikované články na domovské stránce
 */

final class ArticlePresenter extends Nette\Application\UI\Presenter {

    private ArticleModel $articleModel;
    private AccountModel $accountModel;

    /** @persistent */
    public $backlink = '';
    public $sortClicked = false;

    // Připojení databáze a modelů
    public function __construct(ArticleModel $articleModel, AccountModel $accountModel) {

        $this->articleModel = $articleModel;
        $this->accountModel = $accountModel;
    }

    // Pokud je uživatel přihlášen a byl zárověň smazán, bude odhlášen
    public function startup(): void {

        parent::startup();

        $this->accountModel->verifyUser($this);
    }

    // Zobrazení článků na hlavní stránce
    public function renderArticles(): void {

        if ($this->sortClicked) {
            $this->template->articles = $this->articleModel->getArticles($this)->order('created_at ASC');
            $this->template->b = false;
            $this->template->sort = "nejnovějších";
        } else {
            $this->template->articles = $this->articleModel->getArticles($this)->order('created_at DESC');
            $this->template->b = true;
            $this->template->sort = "nejstarších";
        }
    }

// Návrat zpátky (na předchozí stranu) pomocí tlačítka Zpět
    public function actionBack($backlink): void {

        $this->backlink = $backlink;
        $this->restoreRequest($this->backlink);
        $this->redirect('Article:articles');
    }

    /** @ajax */
    public function handleChangeStatus(bool $b) {

        $this->sortClicked = $b;

        // Překreslení
        $this->redrawControl('b');
        $this->redrawControl('articleList');
    }
}
