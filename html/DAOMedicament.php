<?php

require_once './Medicament.class.php';
require_once './Substance.class.php';
require_once './Medecin.class.php';
require_once './VoieAdministration.class.php';

/**
 * Classe permettant la récupération des données concernant les médicaments,
 * grâce aux procédures créées dans la base de données
 */
class DAOMedicament {

    private $cnx;

    /**
     * Constructeur de la classe établissant la connexion grâce au Singleton
     * @param type $connexion
     */
    function __construct($connexion) {
        $this->cnx = $connexion;
    }

    /**
     * Fonction permettant de récupérer la liste de tous les médicaments, avec limite et offset pour la pagination
     * @param type $limit
     * @param type $offset
     * @return array
     */
    function getAllMedicaments($limit = 1024, $offset = 0) {
        $SQL = "CALL AllMedicaments($limit, $offset)";
        $statement = $this->cnx->prepare($SQL);
        $statement->execute();

        $list = [];
        while ($m = $statement->fetchObject("Medicament")) {
            array_push($list, $m);
        }
        return $list;
    }

    /**
     * Fonction permettant de récupérer tous les médicaments, sans limite ni offset
     * @return array
     */
    function getAllMedicamentsNoLimit() {
        $SQL = "CALL AllMedicaments(1024,0)";
        $statement = $this->cnx->prepare($SQL);
        $statement->execute();

        $list = [];
        while ($m = $statement->fetchObject("Medicament")) {
            array_push($list, $m);
        }
        return $list;
    }

    /**
     * Fonction permettant de rechercher un médicament de par un des paramètres suivants:
     * @param type $nom
     * @param type $etat
     * @param type $dateop
     * @param type $damm
     * @param type $surveillance
     * @param type $conditions
     * @param type $prixop
     * @param type $prix
     * @param type $dosages
     * @param type $limit
     * @param type $offset
     * @return array
     */
    function getSearchMedicaments($nom, $etat, $dateop, $damm, $surveillance, $conditions, $prixop, $prix, $dosages, $limit = 1024, $offset = 0) {
        $SQL = "CALL SearchMedicaments(?, ?, ?, ?, ?, ?, ?, ?, ?,$limit, $offset)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $nom);
        $statement->bindParam(2, $etat);
        $statement->bindParam(3, $dateop);
        $statement->bindParam(4, $damm);
        $statement->bindParam(5, $surveillance);
        $statement->bindParam(6, $conditions);
        $statement->bindParam(7, $prixop);
        $statement->bindParam(8, $prix);
        $statement->bindParam(9, $dosages);
        $statement->execute();

        $list = [];
        while ($m = $statement->fetchObject("Medicament")) {
            array_push($list, $m);
        }
        return $list;
    }

    /**
     * Fonction indiquant le nombre de médicaments dans la base au total
     * @return type
     */
    function getCount() {
        $SQL = "SELECT COUNT(*) FROM medicaments";
        return $this->cnx->query($SQL)->fetchColumn();
    }

    /**
     * Fonction permettant de récupérer un médicament de par son code
     * @param type $code
     * @return type
     */
    function getMedicamentByCode($code) {
        $SQL = "CALL MedicamentByCode(:codemed)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam("codemed", $code);
        $statement->execute();
        $m = $statement->fetchObject("Medicament");
        return $m;
    }

    /**
     * Fonction permettant de récupérer toutes les substances contenues dans un médicament
     * @param type $codemed
     * @param type $limit
     * @param type $offset
     * @return array
     */
    function getSubstancesByMedicament($codemed, $limit = 1024, $offset = 0) {
        $SQL = "CALL SubstancesByMedicament(?, $limit, $offset)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $codemed);
        $statement->execute();

        $list = [];
        while ($s = $statement->fetchObject("Substance")) {
            array_push($list, $s);
        }
        return $list;
    }

    /**
     * Fonction permettant de récupérer tous les médecins prescrivant ce médicament
     * @param type $codemed
     * @param type $limit
     * @param type $offset
     * @return array
     */
    function getMedecinsByMedicament($codemed, $limit = 1024, $offset = 0) {
        $SQL = "CALL MedecinsByMedicament(?, $limit, $offset)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $codemed);
        $statement->execute();

        $list = [];
        while ($m = $statement->fetchObject("Medecin")) {
            array_push($list, $m);
        }
        return $list;
    }

    /**
     * Fonction récupérant les voies d'admnistration disponibles pour ce médicament
     * @param type $codemed
     * @return array
     */
    function getVAByMedicament($codemed) {
        $SQL = "CALL VAByMedicament(?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $codemed);
        $statement->execute();

        $list = [];
        while ($va = $statement->fetchObject("VoieAdministration")) {
            array_push($list, $va);
        }
        return $list;
    }

    /**
     * Fonction permettant de récupérer la date à laquelle un médecin s'est mis à prescrire un médicament
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
     * Fonction récupérant toutes les voies d'administration possibles
     * @return array
     */
    function getAllVAs() {
        $SQL = "CALL AllVAs()";
        $statement = $this->cnx->prepare($SQL);
        $statement->execute();

        $list = [];
        while ($va = $statement->fetchObject("VoieAdministration")) {
            array_push($list, $va);
        }
        return $list;
    }

    /**
     * Fonction permettant de créer un nouveau médicament
     * @param type $nom
     * @param type $etat
     * @param type $datemm
     * @param type $surveillance
     * @param type $prescription
     * @param type $prix
     * @param type $dos
     * @return type
     */
    function getCreateMedicament($nom, $etat, $datemm, $surveillance, $prescription, $prix, $dos) {
        $SQL = "CALL CreateMedicament(?, ?, ?, ?, ?, ?, ?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $nom);
        $statement->bindParam(2, $etat);
        $statement->bindParam(3, $datemm);
        $statement->bindParam(4, $surveillance);
        $statement->bindParam(5, $prescription);
        $statement->bindParam(6, $prix);
        $statement->bindParam(7, $dos);
        $statement->execute();

        $last_insertid = $statement->fetchColumn();
        return $last_insertid;
    }

    /**
     * Fonction permettant d'associer un médicament à des substances actives
     * @param type $medicament
     * @param type $substance
     */
    function getAssociateMedicamentSubs($medicament, $substance) {
        $SQL = "CALL AssociateMedicamentSubs(?, ?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $medicament);
        $statement->bindParam(2, $substance);
        $statement->execute();
    }

    /**
     * Fonction permettant d'associer un médicament à des voies d'administration
     * @param type $medicament
     * @param type $va
     */
    function getAssociateMedicamentVas($medicament, $va) {
        $SQL = "CALL AssociateMedicamentVas(?, ?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $medicament);
        $statement->bindParam(2, $va);
        $statement->execute();
    }

    /**
     * Fonction permettant de supprimer un médicament
     * @param type $code
     */
    function getDeleteMedicament($code) {
        $SQL = "CALL DeleteMedicament(:codemed)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam("codemed", $code);
        $statement->execute();
    }

    /**
     * Fonction vérifiant si un médicament et une substance sont associés
     * @param type $medicament
     * @param type $substance
     * @return boolean
     */
    function getCheckAssociationMedicamentSubs($medicament, $substance) {
        $SQL = "CALL CheckAssociationMedicamentSubs(?,?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $medicament);
        $statement->bindParam(2, $substance);
        $statement->execute();

        $result = $statement->fetchColumn();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fonction vérifiant si un médicament et une voie d'administration sont associés
     * @param type $medicament
     * @param type $va
     * @return boolean
     */
    function getCheckAssociationMedicamentVA($medicament, $va) {
        $SQL = "CALL CheckAssociationMedicamentVA(?,?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $medicament);
        $statement->bindParam(2, $va);
        $statement->execute();

        $result = $statement->fetchColumn();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fonction permettant de supprimer toutes les associations du médicament 
     * @param type $code
     */
    function getDeleteMedicamentAssoc($code) {
        $SQL = "CALL DeleteMedicamentAssoc(:codemed)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam("codemed", $code);
        $statement->execute();
    }

    /**
     * Fonction permettant de mettre à jour un médicament
     * @param type $code
     * @param type $nom
     * @param type $etat
     * @param type $datemm
     * @param type $surveillance
     * @param type $prescription
     * @param type $prix
     * @param type $dos
     */
    function getUpdateMedicament($code, $nom, $etat, $datemm, $surveillance, $prescription, $prix, $dos) {
        $SQL = "CALL UpdateMedicament(?, ?, ?, ?, ?, ?, ?, ?)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam(1, $code);
        $statement->bindParam(2, $nom);
        $statement->bindParam(3, $etat);
        $statement->bindParam(4, $datemm);
        $statement->bindParam(5, $surveillance);
        $statement->bindParam(6, $prescription);
        $statement->bindParam(7, $prix);
        $statement->bindParam(8, $dos);
        $statement->execute();
    }

    /**
     * Fonction permettant de récupérer les adresses email des médecins prescrivant ce médicament
     * @param type $code
     * @return array
     */
    function getEmailsMedecinsForMedicament($code) {
        $SQL = "CALL EmailsMedecinsForMedicament(?)";
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
     * Fonction permettant de récupérer une voie d'administration par son code
     * @param type $code
     * @return type
     */
    function getVAbyCode($code) {
        $SQL = "CALL VAbyCode(:codeva)";
        $statement = $this->cnx->prepare($SQL);
        $statement->bindParam("codeva", $code);
        $statement->execute();
        $va = $statement->fetchObject("VoieAdministration");
        return $va;
    }

}

?>