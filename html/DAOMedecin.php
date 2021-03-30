<?php

require_once './Medecin.class.php';
require_once './Medicament.class.php';

/**
 * Classe permettant de récupérer les donnnées concernant les médecins
 * grâce aux procédures créés dans la base de données
 */
class DAOMedecin {

    private $cnx;

    /**
     * Constructeur prenant en paramètre le singleton
     * @param type $connexion
     */
    function __construct($connexion) {
        $this->cnx = $connexion;
    }

    /**
     * Fonction permettant de récupérer tous les médecins contenus dans la base, 
     * avec une limite et un offset pour la pagination
     * @param type $limit
     * @param type $offset
     * @return array
     */
    function getAllMedecins($limit = 1024, $offset = 0) {
        $SQL = "CALL AllMedecins($limit, $offset)";
        $statement = $this->cnx->prepare($SQL);
        $statement->execute();

        $list = [];
        while ($m = $statement->fetchObject("Medecin")) {
            array_push($list, $m);
        }
        return $list;
    }

    /**
     * Fonction permettant de chercher un ou des medecins depuis leur nom, prénom ou adresse
     * (même incomplets), limite et offset pour la pagination
     * @param type $nom
     * @param type $prenom
     * @param type $adresse
     * @param type $limit
     * @param type $offset
     * @return array
     */
    function getSearchMedecins($nom, $prenom, $adresse, $limit = 1024, $offset = 0) {
        $SQL = "CALL SearchMedecins(?, ?, ?, $limit, $offset)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $nom);
        $statement->bindParam(2, $prenom);
        $statement->bindParam(3, $adresse);
        $statement->execute();

        $list = [];
        while ($m = $statement->fetchObject("Medecin")) {
            array_push($list, $m);
        }
        return $list;
    }

    /**
     * Fonction retournant le nombre de médecins dans la base
     * @return type
     */
    function getCount() {
        $SQL = "SELECT COUNT(*) FROM medecins";
        return $this->cnx->query($SQL)->fetchColumn();
    }

    /**
     * Fonction permettant de récupérer un médecin de par son code unique
     * @param type $code
     * @return type
     */
    function getMedecinByCode($code) {
        $SQL = "CALL MedecinByCode(:codedoc)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam("codedoc", $code);
        $statement->execute();
        $m = $statement->fetchObject("Medecin");
        return $m;
    }

    /**
     * Fonction permettant de récupérer tous les médicaments auxquels un médecin
     * a souscrit
     * @param type $codedoc
     * @param type $limit
     * @param type $offset
     * @return array
     */
    function getMedicamentsByMedecin($codedoc, $limit = 1024, $offset = 0) {
        $SQL = "CALL MedicamentsByMedecin(?, $limit, $offset)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $codedoc);
        $statement->execute();

        $list = [];
        while ($m = $statement->fetchObject("Medicament")) {
            array_push($list, $m);
        }
        return $list;
    }

    /**
     * Fonction permettant de retrouver la date à laquelle un medecin a accepté de 
     * prescrire un médicament
     * @param type $codemed
     * @param type $codedoc
     * @return type
     */
    function getDateSouscription($codemed, $codedoc) {
        $SQL = "CALL DateSouscription(?,?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $codemed);
        $statement->bindParam(2, $codedoc);
        $statement->execute();

        $date = $statement->fetchColumn();
        return $date;
    }

    /**
     * Fonction permettant de créer un nouveau médecin
     * @param type $nom
     * @param type $prenom
     * @param type $adresse_cabinet
     * @param type $adresse_mail
     * @return type
     */
    function getCreateMedecin($nom, $prenom, $adresse_cabinet, $adresse_mail) {
        $SQL = "CALL CreateMedecin(?, ?, ?, ?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $nom);
        $statement->bindParam(2, $prenom);
        $statement->bindParam(3, $adresse_cabinet);
        $statement->bindParam(4, $adresse_mail);
        $statement->execute();

        $last_insertid = $statement->fetchColumn();
        return $last_insertid;
    }

    /**
     * Fonction permettant d'associer un médecin à un médicament qu'il a décidé de prescrire
     * @param type $medecin
     * @param type $medicament
     */
    function getAssociateMedecinMedicament($medecin, $medicament) {
        $SQL = "CALL AssociateMedecinMedicament(?, ?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $medecin);
        $statement->bindParam(2, $medicament);
        $statement->execute();
    }

    /**
     * Fonction permettant de supprimer un médecin 
     * @param type $code
     */
    function getDeleteMedecin($code) {
        $SQL = "CALL DeleteMedecin(:codedoc)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam("codedoc", $code);
        $statement->execute();
    }

    /**
     * Fonction permettant de vérifier si un medecin prescrit un certain médicament
     * @param type $medecin
     * @param type $medicament
     * @return boolean
     */
    function getCheckAssociationMedecinMedicament($medecin, $medicament) {
        $SQL = "CALL CheckAssociationMedecinMedicament(?,?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $medecin);
        $statement->bindParam(2, $medicament);
        $statement->execute();

        $result = $statement->fetchColumn();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fonction permettant de supprimer toutes associations de ce médecin 
     * (données présentes dans les tables d'association)
     * @param type $code
     */
    function getDeleteMedecinAssoc($code) {
        $SQL = "CALL DeleteMedecinAssoc(:codedoc)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam("codedoc", $code);
        $statement->execute();
    }

    /**
     * Focntion permettant de mettre à jour les données d'un médecin
     * @param type $codedoc
     * @param type $nom
     * @param type $prenom
     * @param type $adressecab
     * @param type $adressemail
     */
    function getUpdateMedecin($codedoc, $nom, $prenom, $adressecab, $adressemail) {
        $SQL = "CALL UpdateMedecin(?, ?, ?, ?, ?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $codedoc);
        $statement->bindParam(2, $nom);
        $statement->bindParam(3, $prenom);
        $statement->bindParam(4, $adressecab);
        $statement->bindParam(5, $adressemail);
        $statement->execute();
    }

}

?>