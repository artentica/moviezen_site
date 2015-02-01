<?php
    //Déconnexion
    if(!empty($_GET["deco"]) && $_SESSION["authentifie"]){
        unset($_SESSION["authentifie"]);
    }
?>
