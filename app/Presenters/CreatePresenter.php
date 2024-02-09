<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Models\AccountModel;
use App\Models\ArticleModel;

/*
 * Presenter zajišťující vytvoření článku
 */

final class CreatePresenter extends Nette\Application\UI\Presenter {

    private ArticleModel $articleModel;
    private AccountModel $accountModel;

    /** @persistent */
    public $backlink = '';

    // Připojení databáze a modelů
    public function __construct(ArticleModel $articleModel, AccountModel $accountModel) {

        $this->articleModel = $articleModel;
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

    // Zobrazení editačního pole pro nový článek
    public function renderCreateArticle($backlink): void {

        $this->backlink = $backlink;
        $this->articleModel->renderCreateArticle($this);
    }

    // Vytvoření formuláře pro nový článek
    protected function createComponentArticleForm(): Form {

        return $this->articleModel->createArticleForm($this);
    }

    // Uložení nového článku
    public function ArticleFormSucceeded(Form $form, \stdClass $data): void {

        $this->articleModel->createArticle($this, $form, $data);
    }
}
