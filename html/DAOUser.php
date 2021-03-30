<?php

require_once './User.class.php';

/**
 * Classe intéragissant avec la table users de la base de données
 */
class DAOUser {

    /**
     * La variable $cnx stocke la connexion à la base de données du Singleton
     * @var type
     */
    private $cnx;
    private $salt = "gsb2018";

    function __construct($cnx) {
        $this->cnx = $cnx;
    }

    /**
     * Fonction créant un profil de chaque catégorie: un admin, un chercheur et un visiteur
     * avec sécurité du mot de passe : salt + sha
     */
    function faker() {
        $this->cnx->exec("DELETE FROM users;");
        $pwd = '123+aze' . $this->salt;
        $this->cnx->exec("INSERT INTO users(nom, prenom, login, pass, categorie)
                            VALUES('Ce-Ougna', 'Sarah', 'admin', SHA2('" . $pwd . "',512), 'Administrateur');");
        $pwd = 'DPpass1965' . $this->salt;
        $this->cnx->exec("INSERT INTO users(nom, prenom, login, pass, categorie)
                            VALUES('Dubois', 'Paul', 'paul.dubois@swiss-galaxy.com', SHA2('" . $pwd . "',512), 'Chercheur');");
        $pwd = 'Foot12Azerty' . $this->salt;
        $this->cnx->exec("INSERT INTO users(nom, prenom, login, pass, categorie)
                            VALUES('Thomas', 'Vincent', 'vincent.thomas@swiss-galaxy.com', SHA2('" . $pwd . "',512), 'Visiteur');");
    }

    /**
     * Fonction vérifiant auprès de la base de données si un couple identifiant/mot de passe existe bien
     * @param type $login
     * @param type $password
     * @return boolean
     */
    function CheckUser($login, $password) {
        $SQL = "SELECT * FROM users WHERE login=:log AND pass=SHA2(:pass,512)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam("log", $login);
        $p = $password . $this->salt;
        $statement->bindParam("pass", $p);
        $statement->execute();
        $u = $statement->fetchObject("User");

        if ($u == false) {
            return false;
        } else {
            session_start();
            $_SESSION['auth'] = $u->categorie;
            $_SESSION["name"] = $u->prenom . " " . $u->nom;
            return true;
        }
    }

    /**
     * Fonction vérifiant si les droits de la session sont ceux d'un admin
     * @return boolean
     */
    function IsAdministrateur() {
        if (isset($_SESSION["auth"])) {
            if ($_SESSION["auth"] == 'Administrateur') {
                return true;
            }
        }
        return false;
    }

    /**
     * Fonction vérifiant si les droits de la session sont ceux d'un chercheur
     * @return boolean
     */
    function IsChercheur() {
        if (isset($_SESSION["auth"])) {
            if ($_SESSION["auth"] == 'Chercheur') {
                return true;
            }
        }
        return false;
    }

    /**
     * Fonction vérifiant si les droits de la session sont ceux d'un visiteur
     * @return boolean
     */
    function IsVisiteur() {
        if (isset($_SESSION["auth"])) {
            if ($_SESSION["auth"] == 'Visiteur') {
                return true;
            }
        }
        return false;
    }

}
