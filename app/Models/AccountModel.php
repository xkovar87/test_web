<?php

namespace App\Models;

use Nette;
use Nette\Application\UI\Form;

/*
 * Model zajištující získání článků náležících k účtu.
 */

final class AccountModel {

    private Nette\Database\Explorer $database;

    // Připojení databáze
    public function __construct(Nette\Database\Explorer $database) {

        $this->database = $database;
    }

    // Získání článků náležících k účtu
    public function getArticles($thisP) {

        // Tabulka článků
        $articles = $this->database->table('article');

        // Informace o přihlášeném uživateli, jeho jméno a role
        $user = $this->database->table('user')->get($thisP->getUser()->getId());
        $thisP->template->name = $user->name;
        $thisP->template->role = $user->ref('role')->name;

        // Je-li uživatelova role Redaktor
        if ($thisP->getUser()->getIdentity()->role != 1) {

            // Články, které mu patří
            $articles = $articles->where('user_id', $thisP->getUser()->getId());
        }

        // Vrácení článků
        return $articles;
    }

    // Pokud je uživatel přihlášen a byl zárověň smazán z databáze, bude odhlášen
    public function verifyUser($thisP) {

        // Je-li uživatel přihlášený
        if ($thisP->getUser()->isLoggedIn()) {

            // Uživatel
            $row = $this->database->table('user')->get($thisP->getUser()->getId());

            // Pokud není v databázi
            if (!$row) {

                // Odhlásit ze systému
                $thisP->getUser()->logout();
                $thisP->flashMessage('Odhlášení bylo úspěšné.', 'success');

                // Přesměrování
                $thisP->redirect('Article:articles');
            } else if ($row->role != $thisP->getUser()->getIdentity()->role) {

                // Je v databázi, pokud nesouhlasí role, dojde také k odhlášení
                $thisP->getUser()->logout();
                $thisP->flashMessage('Odhlášení bylo úspěšné.', 'success');

                // Přesměrování
                $thisP->redirect('Article:articles');
            }
        }
    }
}
