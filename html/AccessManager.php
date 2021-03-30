<?php

require_once './Singleton.php';
require_once './DAOUser.php';

/**
 * Class qui fait appel aux fonctions vérifiant l'identifiant et le mot de passe ainsi que les droits
 */
class AccessManager {

    private $daouser;

    /**
     * Constructeur appelant le Singleton
     */
    function __construct() {
        $this->daouser = new DAOUser(Singleton::getConnection());
    }

    /**
     * Fonction vérifiant si un user est connecté ou non, grâce au DAOUser
     * @param type $login
     * @param type $password
     * @return type
     */
    function checkLogged($login, $password) {
        return $this->daouser->CheckUser($login, $password);
    }

    /**
     * Fonction vérifiant si la session en cours est celle d'un admin, grâce au DAOUser
     * @return type
     */
    function checkIsAdministrateur() {
        return $this->daouser->IsAdministrateur();
    }

    /**
     * Fonction vérifiant si la session en cours est celle d'un chercheur, grâce au DAOUser
     * @return type
     */
    function checkIsChercheur() {
        return $this->daouser->IsChercheur();
    }

    /**
     * Fonction vérifiant si la session en cours est celle d'un visiteur, grâce au DAOUser
     * @return type
     */
    function checkIsVisiteur() {
        return $this->daouser->IsVisiteur();
    }

}
