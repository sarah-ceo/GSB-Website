<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Classe du Singleton permettant d'instancier une seule connexion à la base de données
 *
 * @author UTILISATEUR
 */
class Singleton {

    private static $cnx;
    private static $instance;
    private $dsn = 'mysql:host=192.168.100.6;dbname=PAPPE1';
    private $username = 'sarah';
    private $password = 'azerty';
    private $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );

    /**
     * Constructeur utilisant le PDO de PHP pour se connecter à une base de données
     */
    private function __construct() {
        self::$cnx = new PDO($this->dsn, $this->username, $this->password, $this->options);
    }

    /**
     * Fonction permettant de vérifier si une connexino existe déjà, et si non d'en instancier une
     * @return type
     */
    static function getConnection() {
        if (is_null(self::$cnx)) {
            self::$instance = new Singleton();
        }
        return self::$cnx;
    }

}

?>