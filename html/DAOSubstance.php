<?php

require_once './Substance.class.php';
require_once './EffetSecondaire.class.php';
require_once './Medicament.class.php';
require_once './ATC.class.php';

/**
 * Classe permettant de récuperer les données concernant les substances
 * grâce aux procédures stockées créées dans la base
 */
class DAOSubstance {

    private $cnx;

    /**
     * Constructeur pour établir la connexion via le Singleton
     * @param type $connexion
     */
    function __construct($connexion) {
        $this->cnx = $connexion;
    }

    /**
     * Fonction pour récupérer toutes les substances avec limite et offset pour la pagination
     * @param type $limit
     * @param type $offset
     * @return array
     */
    function getAllSubstances($limit = 1024, $offset = 0) {
        $SQL = "CALL AllSubstances($limit, $offset)";
        $statement = $this->cnx->prepare($SQL);
        $statement->execute();

        $list = [];
        while ($s = $statement->fetchObject("Substance")) {
            array_push($list, $s);
        }
        return $list;
    }

    /**
     * Fonction permettant de récupérer toutes les substances sans limite ni offset
     * @return array
     */
    function getAllSubstancesNoLimit() {
        $SQL = "CALL AllSubstances(1024, 0)";
        $statement = $this->cnx->prepare($SQL);
        $statement->execute();

        $list = [];
        while ($s = $statement->fetchObject("Substance")) {
            array_push($list, $s);
        }
        return $list;
    }

    /**
     * Fonction permettant de trouver une ou des substances d'après les paramètres de la recherche
     * @param type $nom
     * @param type $atc2
     * @param type $act1
     * @param type $limit
     * @param type $offset
     * @return array
     */
    function getSearchSubstances($nom, $atc2, $act1, $limit = 1024, $offset = 0) {
        $SQL = "CALL SearchSubstances(?, ?, ?, $limit, $offset)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $nom);
        $statement->bindParam(2, $atc2);
        $statement->bindParam(3, $act1);
        $statement->execute();

        $list = [];
        while ($s = $statement->fetchObject("Substance")) {
            array_push($list, $s);
        }
        return $list;
    }

    /**
     * Fonction retournant le nombre de substances dans la base de données
     * @return type
     */
    function getCount() {
        $SQL = "SELECT COUNT(*) FROM substances_actives";
        return $this->cnx->query($SQL)->fetchColumn();
    }

    /**
     * Fonction récupérant une substance par son code
     * @param type $code
     * @return type
     */
    function getSubstanceByCode($code) {
        $SQL = "CALL SubstanceByCode(:codesubs)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam("codesubs", $code);
        $statement->execute();
        $s = $statement->fetchObject("Substance");
        return $s;
    }

    /**
     * Fonction récupérant tous les médicaments dans lesquels la substance est contenue
     * @param type $codesubs
     * @param type $limit
     * @param type $offset
     * @return array
     */
    function getMedicamentsBySubstance($codesubs, $limit = 1024, $offset = 0) {
        $SQL = "CALL MedicamentsBySubstance(?, $limit, $offset)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $codesubs);
        $statement->execute();

        $list = [];
        while ($m = $statement->fetchObject("Medicament")) {
            array_push($list, $m);
        }
        return $list;
    }

    /**
     * Fonction récupérant tous les effets secondaires liés à la substance
     * @param type $codesubs
     * @param type $limit
     * @param type $offset
     * @return array
     */
    function getEffetsBySubstance($codesubs, $limit = 1024, $offset = 0) {
        $SQL = "CALL EffetsBySubstance(?, $limit, $offset)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $codesubs);
        $statement->execute();

        $list = [];
        while ($es = $statement->fetchObject("EffetSecondaire")) {
            array_push($list, $es);
        }
        return $list;
    }

    /**
     * Fonction récupérant l'ATC1 et 2 de la substance et les combinant sous un format spécial
     * @param type $code_atc2
     * @return type
     */
    function getATCBySubstance($code_atc2) {
        $SQL = "SELECT ATCBySubstance(:codeatc2)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam("codeatc2", $code_atc2);
        $statement->execute();

        $ATC = $statement->fetchColumn();
        return $ATC;
    }

    /**
     * Fonction récupérant tous les effets secondaires
     * @return array
     */
    function getAllEffets() {
        $SQL = "CALL AllEffets()";
        $statement = $this->cnx->prepare($SQL);
        $statement->execute();

        $list = [];
        while ($es = $statement->fetchObject("EffetSecondaire")) {
            array_push($list, $es);
        }
        return $list;
    }

    /**
     * Fonction récupérant tous les atc de niveau 1
     * @return array
     */
    function getAllATC1() {
        $SQL = "CALL AllATC1()";
        $statement = $this->cnx->prepare($SQL);
        $statement->execute();

        $list = [];
        while ($atc1 = $statement->fetchObject("ATC")) {
            array_push($list, $atc1);
        }
        return $list;
    }

    /**
     * Fonction récupérant les atc2 correspondant à un atc1
     * @param type $atc1
     * @return array
     */
    function getATC2ByATC1($atc1) {
        $SQL = "CALL ATC2ByATC1('$atc1')";
        $statement = $this->cnx->prepare($SQL);
        $statement->execute();

        $list = [];
        while ($atc2 = $statement->fetchObject("ATC")) {
            array_push($list, $atc2);
        }
        return $list;
    }

    /**
     * Fonction permettant de créer une substance
     * @param type $DCI
     * @param type $infos_sup
     * @param type $code_atc2
     * @return type
     */
    function getCreateSubstance($DCI, $infos_sup, $code_atc2) {
        $SQL = "CALL CreateSubstance(?, ?, ?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $DCI);
        $statement->bindParam(2, $infos_sup);
        $statement->bindParam(3, $code_atc2);
        $statement->execute();

        $last_insertid = $statement->fetchColumn();
        return $last_insertid;
    }

    /**
     * Fonction permettant d'associer une substance à un effet secondaire
     * @param type $substance
     * @param type $effet
     */
    function getAssociateSubstanceES($substance, $effet) {
        $SQL = "CALL AssociateSubstanceES(?, ?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $substance);
        $statement->bindParam(2, $effet);
        $statement->execute();
    }

    /**
     * Fonction permettant de supprimmer une substance
     * @param type $code
     */
    function getDeleteSubstance($code) {
        $SQL = "CALL DeleteSubstance(:codesubs)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam("codesubs", $code);
        $statement->execute();
    }

    /**
     * Fonction vérifiant si une substance cause un certain effet secondaire
     * @param type $substance
     * @param type $es
     * @return boolean
     */
    function getCheckAssociationSubstanceES($substance, $es) {
        $SQL = "CALL CheckAssociationSubstanceES(?,?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $substance);
        $statement->bindParam(2, $es);
        $statement->execute();

        $result = $statement->fetchColumn();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fonction supprimant les associations d'une substance
     * @param type $code
     */
    function getDeleteSubstanceAssoc($code) {
        $SQL = "CALL DeleteSubstanceAssoc(:codesubs)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam("codesubs", $code);
        $statement->execute();
    }

    /**
     * Fonction mettant à jour une substance
     * @param type $code
     * @param type $DCI
     * @param type $infos_sup
     * @param type $code_atc2
     */
    function getUpdateSubstance($code, $DCI, $infos_sup, $code_atc2) {
        $SQL = "CALL UpdateSubstance(?, ?, ?, ?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $code);
        $statement->bindParam(2, $DCI);
        $statement->bindParam(3, $infos_sup);
        $statement->bindParam(4, $code_atc2);
        $statement->execute();
    }

    /**
     * Fonction permettant de récupérer les emails des médecins qui prescrivent des
     * médicaments dans lesquels la substance est contenue
     * @param type $code
     * @return array
     */
    function getEmailsMedecinsForSubstance($code) {
        $SQL = "CALL EmailsMedecinsForSubstance(?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $code);
        $statement->execute();

        $emails = [];
        while ($e = $statement->fetchColumn()) {
            array_push($emails, $e);
        }
        return $emails;
    }

    /**
     * Fonction permettant de récupérer un effet secondaire depuis son code
     * @param type $code
     * @return type
     */
    function getEffetbyCode($code) {
        $SQL = "CALL EffetbyCode(:codees)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam("codees", $code);
        $statement->execute();
        $es = $statement->fetchObject("EffetSecondaire");
        return $es;
    }

}

?>