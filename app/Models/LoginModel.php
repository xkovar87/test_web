<?php

namespace App\Models;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\SimpleIdentity;
use Nette\Security\Passwords;

/*
 * Model zajištující vytvoření nového účtu, přihlášení a odhlášení
 */

final class LoginModel {

    private Nette\Database\Explorer $database;
    private Nette\Security\Passwords $passwords;

    // Připojení databáze
    public function __construct(Nette\Database\Explorer $database, Nette\Security\Passwords $passwords) {

        $this->database = $database;
        $this->passwords = $passwords; // Objekt pro šifrování hesla
    }

    // Vytvoření formuláře pro přihlášení
    public function createSignInForm($thisP) {

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
        $form->addText('username', 'Jméno:')
                ->setHtmlAttribute('placeholder', 'Uživatelské jméno')
                ->setRequired('Prosím vyplňte své uživatelské jméno.')
                ->setAttribute('class', 'my-input');

        $form->addPassword('password', 'Heslo:')
                ->setHtmlAttribute('placeholder', 'Uživatelské heslo')
                ->setRequired('Prosím vyplňte své heslo.')
                ->setAttribute('class', 'my-input');

        $form->addSubmit('send', 'Přihlásit se')->setAttribute('class', 'btn btn-outline-primary shadow-lg');

        // Po kliknutí se vykoná tato metoda
        $form->onSuccess[] = [$thisP, 'signInFormSucceeded'];

        return $form;
    }

    // Vyhodnocení přihlášení
    public function signInSucceeded($thisP, $form, $data) {

        try {
            $passwords = new Passwords(PASSWORD_BCRYPT, ['cost' => 12]);

            // Hledání uživatele se zadaným jménem
            $row = $this->database->table('user')
                    ->where('name', $data->username)
                    ->fetch();
            if (!$row) {

                // Jméno nenalezeno
                $thisP->flashMessage('Nesprávné přihlašovací jméno nebo heslo.', 'error');
                //$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
            } else {

                // Jméno nalezeno, ověření zda je zadané heslo stejné jako v databázi
                if ($passwords->verify($data->password, $row->password)) {

                    // Nastavení doby expirace přihlášení
                    $thisP->getUser()->setExpiration('30 minutes');
                    $thisP->flashMessage('Přihlášení bylo úspěšné.', 'success');

                    // Vytvoření identity uživatele a jeho přihlášení do systému
                    $thisP->getUser()->login(new SimpleIdentity($row->id, $row->role, ['name' => $row->name, 'role' => $row->role]));

                    // Přesměrování na jinou stránku
                    $thisP->restoreRequest($thisP->backlink);

                    $thisP->redirect('Article:articles');
                } else {

                    // Heslo není stejné
                    $thisP->flashMessage('Nesprávné přihlašovací jméno nebo heslo.', 'error');
                    //$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
                }
            }
        } catch (Nette\Security\AuthenticationException $e) {

            // Nastala chyba při ověřování
            $thisP->flashMessage('Nesprávné přihlašovací jméno nebo heslo.', 'error');
            //$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
        }
    }

    // Odhlášení
    public function actionOut($thisP) {

        // Odhlášení uživatele a přesměrování
        $thisP->getUser()->logout();
        $thisP->flashMessage('Odhlášení bylo úspěšné.', 'success');
        //$thisP->restoreRequest($thisP->backlink);
        $thisP->redirect('Article:articles');
    }

    // Vytvoření formuláře pro registraci
    public function createRegisterInForm($thisP) {

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
        $form->addText('username', 'Jméno:')
                ->setHtmlAttribute('placeholder', 'Nové uživatelské jméno')
                ->setRequired('Prosím zadejte své nové uživatelské jméno.')
                ->setAttribute('class', 'my-input');

        $form->addText('email', 'Email:')
                ->setHtmlAttribute('placeholder', 'Nový uživatelský email')
                ->addRule(Form::EMAIL, 'Email není zadaný správně.')
                ->setRequired('Prosím zadejte svůj email.')
                ->setAttribute('class', 'my-input');

        $form->addPassword('password', 'Heslo:')
                ->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 8)
                ->setHtmlAttribute('placeholder', 'Nové uživatelské heslo')
                ->setRequired('Prosím vyplňte své nové heslo.')
                ->setAttribute('class', 'my-input');

        $form->addPassword('passwordAgain', 'Heslo znovu:')
                ->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 8)
                ->setHtmlAttribute('placeholder', 'Znovu uživatelské heslo')
                ->setRequired('Prosím vyplňte opět své nové heslo.')
                ->setAttribute('class', 'my-input');

        $form->addSubmit('send', 'Registrovat se')->setAttribute('class', 'btn btn-outline-primary shadow-lg');

        // Po kliknutí se vykoná tato metoda
        $form->onSuccess[] = [$thisP, 'registerInFormSucceeded'];

        return $form;
    }

    // Vyhodnocení registrace
    public function registerInSucceeded($thisP, $form, $data) {

        // Porovnání dvou zadaných hesel, zda jsou stejná
        if (strcmp($data->password, $data->passwordAgain) == 0) {

            // Hledání uživatele se zadaným jménem
            $rows = $this->database->table('user')
                    ->where('name', $data->username)
                    ->fetch();

            if ($rows) {

                // Pokud existuje
                $thisP->flashMessage('Zadané jméno nebo email již existuje.', 'error');
            } else {

                // Pokud neexistuje, zjišťujeme existenci zadaného emailu
                $rows = $this->database->table('user')
                        ->where('email', $data->email)
                        ->fetch();

                if ($rows) {
                    // Pokud existuje
                    $thisP->flashMessage('Zadané jméno nebo email již existuje.', 'error');
                } else {

                    // Zadané údaje jsou v pořádku, zašifrování hesla
                    $passwords = new Passwords(PASSWORD_BCRYPT, ['cost' => 12]);
                    $res = $passwords->hash($data->password);

                    // Vložení nového uživatele do databáze
                    $row = $this->database->query('INSERT INTO user', [
                        'name' => $data->username,
                        'email' => $data->email,
                        'password' => $res,
                        'role' => 2,
                    ]);
                    if (!$row) {
                        $thisP->flashMessage('Registrace se nezdařila.', 'error');
                    } else {

                        // Úspěšná registrace a přesměrování na přihlášení
                        $thisP->flashMessage('Registrace byla úspěšná, můžete se přihlásit.', 'success');
                        $thisP->redirect('Sign:in');
                    }
                }
            }
        } else {

            // Hesla nejsou stejná
            $thisP->flashMessage('Zadaná hesla se neshodují.', 'error');
        }
    }
}
