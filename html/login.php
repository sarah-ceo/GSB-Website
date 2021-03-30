<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Connexion</title>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="css/login.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="wrapper">
            <?php
            if (isset($_GET["logout"])){
                session_destroy();
            }
            ?>
            <form class="form-signin" method="POST" action="check.php">
                <h2 class="form-signin-heading"><img src="images/logo-gsb.jpg" alt="GSB"></h2>
                <input type="text" class="form-control" name="username" placeholder="Identifiant" required="" autofocus="" />
                <input type="password" class="form-control" name="password" placeholder="Mot de passe" required=""/>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Connexion</button>
                <?php if(isset($_GET["fail"])): ?>
                <br/><div class="alert-danger" id="erreur-connexion">Erreur de connexion, veuillez saisir vos identifiants à nouveau</div>
                <?php endif ?>
            </form>
        </div>
    </body>
</html>

