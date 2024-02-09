<?php

namespace App\Models;

use Nette;
use Latte;
use Nette\Application\UI\Form;

/*
 * Model zajištující získání a vytváření článků
 */

final class ArticleModel {

    private Nette\Database\Explorer $database;
    private Nette\Application\LinkGenerator $linkGenerator;
    private Nette\Bridges\ApplicationLatte\TemplateFactory $templateFactory;
    private string $__EMAIL__;

    // Připojení databáze
    public function __construct(string $__EMAIL__, Nette\Database\Explorer $database, Nette\Application\LinkGenerator $linkGenerator, Nette\Bridges\ApplicationLatte\TemplateFactory $templateFactory) {

        $this->database = $database;
        $this->linkGenerator = $linkGenerator; // Objekt pro generování odkazů u emailu
        $this->templateFactory = $templateFactory; // Objekt tvořící šablony
        $this->__EMAIL__ = $__EMAIL__; // Cesta umístění šablony pro email
    }

    // Získání všech článků
    public function getArticles($thisP) {

        // Tabulka článků
        $articles = $this->database->table('article');
        return $articles;
    }

    // Zobrazení editačního pole pro nový článek
    public function renderCreateArticle($thisP) {

        $thisP->template->backpage = $thisP->backlink;
    }

    // Vytvoření formuláře pro nový článek
    public function createArticleForm($thisP) {

        $form = new Form;

        // Responzivní struktura formuláře
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = null;
        $renderer->wrappers['pair']['container'] = 'div class="form-group row mb-4"';
        $renderer->wrappers['pair']['.error'] = 'has-danger';
        $renderer->wrappers['control']['container'] = 'div class="col-lg-10 col-md-10"';
        $renderer->wrappers['label']['container'] = 'div class="fw-bold col-lg-1 col-md-2 col-form-label"';
        $renderer->wrappers['control']['description'] = 'span class=form-text';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
        $renderer->wrappers['control']['.error'] = 'is-invalid';

        // Formulářové prvky
        $form->addTextArea('title', 'Titulek:')
                ->setRequired('Titulek je povinný parametr.');
        $form->addTextArea('content', 'Obsah:')
                ->setRequired('Obsah je povinný parametr.');
        $form->addMultiUpload('files', 'Soubory:')
                ->addRule($form::MIME_TYPE, 'Lze nahrát pouze obrázky', 'image/*')
                //->addRule($form::MaxLength, 'Maximálně lze nahrát %d souborů', 10)
                ->setAttribute('class', 'my-file btn btn-outline-secondary');
        $form->addSubmit('send', 'Uložit a publikovat')->setAttribute('class', 'btn btn-outline-primary shadow-lg');

        // Po kliknutí se vykoná tato metoda
        $form->onSuccess[] = [$thisP, 'ArticleFormSucceeded'];
        return $form;
    }

    // Uložení nového článku
    public function createArticle($thisP, $form, $data) {

        // Tabulka článků
        $article = $this->database->table('article');

        // Vložení nového článku
        $article = $article->insert([
            'user_id' => $thisP->getUser()->getId(),
            'title' => $data->title,
            'content' => $data->content,
        ]);

        // EMAIL //
        // Kdo článek vytvořil
        /*$user = $this->database->table('user')->get($thisP->getUser()->getId());
        $admin = $this->database->table('user')->where('role', 1)->fetch();

        if ($user != $admin) {

            $params = [
                'user' => $user->name,
                'title' => $data->title,
            ];

            // Vytvoření šablony pro email
            $template = $this->createTemplate();
            $html = $template->renderToString($this->__EMAIL__, $params);

            // Vytvoření emailové zprávy
            $mail = new Nette\Mail\Message;
            $mail->setFrom('testwebnewsystem@gmail.com')
                    ->addTo($admin->email)
                    ->setSubject('Nový článek (test web)')
                    ->setHtmlBody($html);
            // Odeslání emailu
            $mailer = new Nette\Mail\SendmailMailer([
                'host' => 'smtp.gmail.com',
                'username' => 'testwebnewsystem@gmail.com',
                'password' => 'NewWebTest2024page',
                'secure' => 'ssl',
            ]);
            $mailer->send($mail);
        }*/

        // Vše proběhlo v pořádku
        $thisP->flashMessage("Článek byl úspěšně publikován.", 'success');

        // Přesměrování
        $thisP->restoreRequest($thisP->backlink);
        $thisP->redirect('Article:articles');
    }

    // Vytvoření šablony a propojení s generátorem odkazů
    private function createTemplate(): Nette\Application\UI\Template {

        $template = $this->templateFactory->createTemplate();
        $template->getLatte()->addProvider('uiControl', $this->linkGenerator);
        return $template;
    }
}
