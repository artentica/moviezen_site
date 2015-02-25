<?php

    require_once('../conf/config.php');

    //FONCTION DE CONNEXION A LA BDD
    function connect(){
		$GLOBALS["bdd"] = new mysqli(HOST, USER, PASSWORD, DATABASE);
		$GLOBALS["bdd"]->set_charset("utf8");
		if ($GLOBALS["bdd"]->connect_errno){
			echo "Echec lors de la connexion à MySQL :(" . $GLOBALS["bdd"]->connect_errno .") " . $GLOBALS["bdd"]->connect_errno;
			return false;
		}
		return true;
	}


    //FONCTION DE PROTECTION DES CHAINES UTILISATEURS
    function protect($chaine){
        $protect = $GLOBALS["bdd"]->real_escape_string($chaine);
        return $protect;
    }

    ////////////////////////////REPLACE FUNCTION/////////////////////::

    function replace_chara($texte){
        $toreplace = array('\"');
        $by   = array('"');
        $toreplace2 = array("\'");
        $by2   = array("'");

        $texte  = str_replace($toreplace, $by, $texte);
        $texte  = str_replace($toreplace2, $by2, $texte);
        return $texte;
    }



    //FONCTION DE RECUPERATION DES PROMOS DISPONIBLES
    function recupPromo(){
        $query = "SELECT * from promotion ORDER BY id";
        return $GLOBALS["bdd"]->query($query);
    }


//################################################################################################################################################################

    //SEND MAILS
    function send_mail($seance,$date,$email){
        $email = protect($email);
        $to = $email;

        $nombre_random = md5(uniqid(rand(), true));
        //verif si personne inscrit
        $verif = $GLOBALS["bdd"]->prepare("SELECT COUNT(*) FROM projections_inscrits WHERE inscrit_mail=? AND projection=?");
        $verif->bind_param("ss",$email,$seance);
        $verif->execute();
        $verif->store_result();
        $verif->bind_result($temp);
        $verif->fetch();
        $verif->close();

        if($temp == 0) return 2;

        //verif anti spam
        $verif = $GLOBALS["bdd"]->prepare("SELECT last_send FROM desinscription WHERE mail=? AND projection=?");
        $verif->bind_param("ss",$email,$seance);
        $verif->execute();
        $verif->store_result();
        $verif->bind_result($temp);
        $verif->fetch();
        $verif->close();

        $date = date_create();
        $date=date_timestamp_get($date);
        $time = $date - $temp;

        if($time < 300 ) return 4;


        $verif = $GLOBALS["bdd"]->prepare("SELECT COUNT(*) FROM desinscription WHERE mail=? AND projection=?");

        $verif->bind_param("ss",$email,$seance);
        $verif->execute();
        $verif->store_result();
        $verif->bind_result($temp);
        $verif->fetch();
        $verif->close();

        if($temp == 0){
        //on fourre le tout dans la bdd

            $query = $GLOBALS["bdd"]->prepare("INSERT INTO  `desinscription` (  `mail` ,  `desinscription_code` ,  `projection` , `last_send`) VALUES (?,?,?,?)");
            $query->bind_param('sssi',$email,$nombre_random,$seance,$date);
            $query->execute();
            $query->close();
        }
        else
        {
            $query = $GLOBALS["bdd"]->prepare("UPDATE `desinscription` SET `last_send`=? WHERE mail=? AND projection=?");
            $query->bind_param('iss',$date,$email,$seance);
            $query->execute();
            $query->close();
        }


        $desincode = $GLOBALS["bdd"]->prepare("SELECT `desinscription_code` FROM `desinscription` WHERE `mail`=? AND `projection`=?");


        $desincode->bind_param("ss",$email,$seance);
        $desincode->execute();
        $desincode->store_result();
        $desincode->bind_result($temp);
        $desincode->fetch();
        $desincode->close();
        $subject = 'Désinscription de la séance Moviezen pour: "'.$seance.'"';
        $message = '

       Vous voulez vous désinscrire pour la séance "'.$seance.'" du '.$date.'.
       Pour vous désinscrire: www.moviezen.fr/views/desinscription.php?codedesin='.$temp.'';



        // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini

        $headers = 'From: Moviezen Brest <no-reply@moviezen.fr>' . "\r\n";
        $subject = utf8_encode($subject);
        $message = utf8_encode($message);





        // En-têtes additionnels

        // $headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
        // $headers = 'From: Riouallon Vincent <riouallonvincent@gmail.com>' . "\r\n";*/
        // $headers .= 'Cc: anniversaire_archive@example.com' . "\r\n";
        // $headers .= 'Bcc: anniversaire_verif@example.com' . "\r\n";*/

         // Envoi
         return mail($to, $subject, $message, $headers);
    }

//################################################################################################################################################################

    //FONCTIONS GESTION DES INSCRITS


    //AJOUT D'INSCRITS A UNE PROJECTION (UTILISATEUR)
    function ajoutInscrit($nom,$prenom,$mail,$classe,$projection){
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits VALUES (?, ?, '', ?, ?)");
        $nom = protect($nom);
        $prenom = protect($prenom);
        $mail = protect($mail);
        $classe = protect($classe);
        $query->bind_param('ssss', $nom,$prenom,$mail, $classe);
        $query->execute();
        $query->close();

        $count = $GLOBALS["bdd"]->prepare("SELECT COUNT(*) FROM projections_inscrits WHERE inscrit_mail=? AND projection=?");
        $count->bind_param("ss",$mail,$projection);
        $count->execute();

        $count->store_result();
        $count->bind_result($temp);

        $count->fetch();
        $count->close();
        if($temp==0){
            $query2 = $GLOBALS["bdd"]->prepare("INSERT INTO `projections_inscrits` (`inscrit_mail`, `projection`) VALUES (?, ?)");
            $query2->bind_param('ss', $mail, $projection);
            $query2->execute();
            $query2->close();

            return 1;

        }
        else return 2;      ////////////////A fiNIR

    }



    //FONCTION MODIFICATION D'INSCRITS A UNE PROJECTION (UTILISATEUR)
    function modifInscrit($mail, $projection, $ancien_mail){
        $query = $GLOBALS["bdd"]->prepare("UPDATE projections_inscrits SET  inscrit_mail=?, projection=? WHERE inscrit_mail=?");
        $mail = protect($mail);
        $projection = protect($projection);
        $ancien_mail = protect($ancien_mail);
        $query->bind_param('sss', $mail, $prenom, $ancien_mail);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION SUPPRESSION D'INSCRITS A UNE PROJECTION  (UTILISATEUR)
    function supprInscrit($mail,$projection){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM projections_inscrits WHERE inscrit_mail=? and projection=?");
        $mail = protect($mail);
        $projection = protect($projection);
        $query->bind_param('ss', $mail,$projection);
        $query->execute();
        $query->close();
        return true;
    }

    //FONCTION SUPPRESSION Demande désinscription  (UTILISATEUR)
    function supprdesinc($nb){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM `desinscription` WHERE desinscription_code=?");
        $query->bind_param('s', $nb);
        $query->execute();
        $query->close();
        return true;
    }






//################################################################################################################################################################


    //FONCTIONS GESTION DES EMPRUNTS

    //FONCTION AJOUT D'EMPRUNT
    /*function ajoutEmprunt($nom,$prenom,$tel,$mail, $classe,$lots,$date_emprunt,$date_retour){
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits VALUES (?, ?, ?, ?, ?)");
        $nom = protect($nom);
        $prenom = protect($prenom);
        $tel = protect($tel);
        $mail = protect($mail);
        $classe = protect($classe);
        $query->bind_param('sssss', $nom,$prenom,$tel,$mail,$classe);
        $query->execute();
        $query->close();
        $date_emprunt = protect($date_emprunt);
        $date_retour = protect($date_retour);
        $date_emprunt = date("Y-m-d H:m:s", strtotime($date_emprunt));
        $date_retour = date("Y-m-d H:m:s", strtotime($date_retour));
        $date_ajd = date("Y-m-d H:m:s");
        $date_ajd = new DateTime($date_ajd);
        $date_futur = date("Y-m-d H:m:s");
        $date_futur = new DateTime($date_futur);
        date_add($date_futur, date_interval_create_from_date_string('1 year'));
        $date_ajd = $date_ajd->format('Ymd');
        $date_futur = $date_futur->format('Ymd');
        $date_emprunt_test = new DateTime($date_emprunt);
        $date_emprunt_test = $date_emprunt_test->format('Ymd');
        $date_retour_test = new DateTime($date_retour);
        $date_retour_test = $date_retour_test->format('Ymd');
        if( $date_ajd < $date_emprunt_test && $date_emprunt_test < $date_retour_test && $date_futur > $date_emprunt_test ){
            foreach($lots as $liste){
                $verif = "SELECT disponible from lots WHERE id='".$liste."'";
                $result = $GLOBALS["bdd"]->query($verif);
                $disponible=false;
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $disponible = $row["disponible"];
                }
                if($disponible){
                    $query2 = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits_lots VALUES (?, ?, ?, ?)");
                    $query2->bind_param('ssss', $mail,$liste,$date_emprunt,$date_retour);
                    $query2->execute();
                    $query2->close();
                    $query2 = $GLOBALS["bdd"]->prepare("UPDATE lots SET disponible='0' WHERE id=?");
                    $query2->bind_param('s', $liste);
                    $query2->execute();
                    $query2->close();
                }
                else{
                    echo('Le lot '.$liste.' n\'est actuellement pas disponible et n\'a donc pas été emprunté.');
                }
            }
            return true;
        }
        else{
            return false;
        }
    }*/

    //FONCTION AJOUT D'EMPRUNT (TEST AVEC TABLE DISPONIBILITES)
    // PS : ca marche et logiquement, tous les cas sont prévus sooooo
    // Don't touch, magic is at work here !
    function ajoutEmprunt2($nom,$prenom,$tel,$mail, $classe,$lots,$date_emprunt,$date_retour){
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits VALUES (?, ?, ?, ?, ?)");
        $nom = protect($nom);
        $prenom = protect($prenom);
        $tel = protect($tel);
        $mail = protect($mail);
        $classe = protect($classe);
        $query->bind_param('sssss', $nom,$prenom,$tel,$mail,$classe);
        $query->execute();
        $query->close();
        $date_emprunt = protect($date_emprunt);
        $date_retour = protect($date_retour);
        $date_emprunt = date("Y-m-d H:m:s", strtotime($date_emprunt));
        $date_retour = date("Y-m-d H:m:s", strtotime($date_retour));
        $date_ajd = date("Y-m-d H:m:s");
        $date_ajd = new DateTime($date_ajd);
        $date_futur = date("Y-m-d H:m:s");
        $date_futur = new DateTime($date_futur);
        date_sub($date_ajd, date_interval_create_from_date_string('1 day'));
        date_add($date_futur, date_interval_create_from_date_string('1 month'));
        $date_ajd = $date_ajd->format('Ymd');
        $date_futur = $date_futur->format('Ymd');
        $date_emprunt_test = new DateTime($date_emprunt);
        $date_emprunt_test = $date_emprunt_test->format('Ymd');
        $date_retour_test = new DateTime($date_retour);
        $date_retour_test = $date_retour_test->format('Ymd');
        $string_lots = "";
        if( $date_ajd < $date_emprunt_test && $date_emprunt_test < $date_retour_test && $date_futur > $date_emprunt_test ){
            foreach($lots as $liste){
                $string_lots .= $liste." ";
                $date_emprunt_formatée = date("z", strtotime($date_emprunt));
                $date_retour_formatée = date("z", strtotime($date_retour));
                $verif = "SELECT ".$liste." from dispo WHERE jour>=".($date_emprunt_formatée+1)." AND jour<".($date_retour_formatée+1);
                $result = $GLOBALS["bdd"]->query($verif);
                $disponible=false;
                $compteur =0;
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $compteur = $compteur+intval($row[$liste]);
                }
                if($compteur == ($date_retour_formatée-$date_emprunt_formatée)){
                    $disponible = true;
                }
                if($disponible){
                    $query2 = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits_lots VALUES (?, ?, ?, ?)");
                    $query2->bind_param('ssss', $mail,$liste,$date_emprunt,$date_retour);
                    $query2->execute();
                    $query2->close();
                    $query2 = "UPDATE dispo SET ".$liste."=0 WHERE jour>=".($date_emprunt_formatée+1)." AND jour<".($date_retour_formatée+1);
                    $query2 = $GLOBALS["bdd"]->query($query2);
                }
                else{
                    echo('Le lot '.$liste.' n\'est pas disponible sur la période demandée et n\'a donc pas été emprunté.');
                }
            }
            $ok = true;
        }
        else{
            $ok = false;
        }
        return $ok;

        //PARTIE ENVOI DE MAIL, A NE TESTER QU'AVEC UN SERVEUR SMTP FONCTIONNEL
        //Ca fait tout planter méchamment sinon
        /*if($ok){
            $verif = "SELECT mail from admin WHERE responsable_emprunt=1";
            $result = $GLOBALS["bdd"]->query($verif);
            setlocale (LC_TIME, 'fr_FR','fra');
            $date_emprunt = utf8_encode(strftime("%d %b %Y",strtotime($date_emprunt)));
            $date_retour = utf8_encode(strftime("%d %b %Y",strtotime($date_retour)));
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                mail($row["mail"],"Un nouvel emprunt a été effectué !", "Les lots ".$string_lots." \r\n seront empruntés par ".$nom." ".$prenom." \r\n du ".$date_emprunt." au ".$date_retour);
            }
            return $ok;
        }
        else{
            return $ok;
        }*/
    }

    //FONCTION DE VERIFICATION D'EMPRUNT
    function verifEmpruntDispo($lots,$date_emprunt,$date_retour){
        $date_emprunt = protect($date_emprunt);
        $date_retour = protect($date_retour);
        $date_emprunt = date("Y-m-d H:m:s", strtotime($date_emprunt));
        $date_retour = date("Y-m-d H:m:s", strtotime($date_retour));
        $date_ajd = date("Y-m-d H:m:s");
        $date_ajd = new DateTime($date_ajd);
        $date_futur = date("Y-m-d H:m:s");
        $date_futur = new DateTime($date_futur);
        date_sub($date_ajd, date_interval_create_from_date_string('1 day'));
        date_add($date_futur, date_interval_create_from_date_string('1 month'));
        $date_ajd = $date_ajd->format('Ymd');
        $date_futur = $date_futur->format('Ymd');
        $date_emprunt_test = new DateTime($date_emprunt);
        $date_emprunt_test = $date_emprunt_test->format('Ymd');
        $date_retour_test = new DateTime($date_retour);
        $date_retour_test = $date_retour_test->format('Ymd');
        $reponse = "";
        if( $date_ajd < $date_emprunt_test && $date_emprunt_test < $date_retour_test && $date_futur > $date_emprunt_test ){
            foreach($lots as $liste){
                $date_emprunt_formatée = date("z", strtotime($date_emprunt));
                $date_retour_formatée = date("z", strtotime($date_retour));
                $verif = "SELECT ".$liste." from dispo WHERE jour>=".($date_emprunt_formatée+1)." AND jour<".($date_retour_formatée+1);
                $result = $GLOBALS["bdd"]->query($verif);
                $disponible=false;
                $compteur =0;
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $compteur = $compteur+intval($row[$liste]);
                }
                if($compteur == ($date_retour_formatée-$date_emprunt_formatée)){
                    $disponible = true;
                }
                if($disponible){
                    $reponse .= '<p class="alert alert-success">Le lot '.$liste.' est disponible sur la période demandée.</p>';
                }
                else{
                    $reponse .= '<p class="alert alert-danger">Le lot '.$liste.' n\'est pas disponible sur la période demandée.</p>';
                }
            }
            return $reponse;
        }
        else{
            return false;
        }
    }

    function ajoutNewEmprunt($mail,$lots,$date_emprunt,$date_retour){
        $date_emprunt = protect($date_emprunt);
        $date_retour = protect($date_retour);
        $date_emprunt = date("Y-m-d H:m:s", strtotime($date_emprunt));
        $date_retour = date("Y-m-d H:m:s", strtotime($date_retour));
        $date_ajd = date("Y-m-d H:m:s");
        $date_ajd = new DateTime($date_ajd);
        $date_futur = date("Y-m-d H:m:s");
        $date_futur = new DateTime($date_futur);
        date_sub($date_ajd, date_interval_create_from_date_string('1 day'));
        date_add($date_futur, date_interval_create_from_date_string('1 month'));
        $date_ajd = $date_ajd->format('Ymd');
        $date_futur = $date_futur->format('Ymd');
        $date_emprunt_test = new DateTime($date_emprunt);
        $date_emprunt_test = $date_emprunt_test->format('Ymd');
        $date_retour_test = new DateTime($date_retour);
        $date_retour_test = $date_retour_test->format('Ymd');
        if( $date_ajd < $date_emprunt_test && $date_emprunt_test < $date_retour_test && $date_futur > $date_emprunt_test ){
            foreach($lots as $liste){
                $date_emprunt_formatée = date("z", strtotime($date_emprunt));
                $date_retour_formatée = date("z", strtotime($date_retour));
                $verif = "SELECT ".$liste." from dispo WHERE jour>=".($date_emprunt_formatée+1)." AND jour<".($date_retour_formatée+1);
                $result = $GLOBALS["bdd"]->query($verif);
                $disponible=false;
                $compteur =0;
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $compteur = $compteur+intval($row[$liste]);
                }
                if($compteur == ($date_retour_formatée-$date_emprunt_formatée)){
                    $disponible = true;
                }
                if($disponible){
                    $query2 = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits_lots VALUES (?, ?, ?, ?)");
                    $query2->bind_param('ssss', $mail,$liste,$date_emprunt,$date_retour);
                    $query2->execute();
                    $query2->close();
                    $query2 = "UPDATE dispo SET ".$liste."=0 WHERE jour>=".($date_emprunt_formatée+1)." AND jour<".($date_retour_formatée+1);
                    $query2 = $GLOBALS["bdd"]->query($query2);
                }
                else{
                    echo('Le lot '.$liste.' n\'est pas disponible sur la période demandée et n\'a donc pas été emprunté.');
                }
            }
            $ok = true;
        }
        else{
            $ok = false;
        }
        return $ok;

        //PARTIE ENVOI DE MAIL, A NE TESTER QU'AVEC UN SERVEUR SMTP FONCTIONNEL
        //Ca fait tout planter méchamment sinon
        /*if($ok){
            $verif = "SELECT mail from admin WHERE responsable_emprunt=1";
            $result = $GLOBALS["bdd"]->query($verif);
            setlocale (LC_TIME, 'fr_FR','fra');
            $date_emprunt = utf8_encode(strftime("%d %b %Y",strtotime($date_emprunt)));
            $date_retour = utf8_encode(strftime("%d %b %Y",strtotime($date_retour)));
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                mail($row["mail"],"Un nouvel emprunt a été effectué !", "Les lots ".$string_lots." \r\n seront empruntés par ".$nom." ".$prenom." \r\n du ".$date_emprunt." au ".$date_retour);
            }
            return $ok;
        }
        else{
            return $ok;
        }*/
    }


    //FONCTION MODIFICATION D'EMPRUNT (UTILISATEUR)
    // Don't touch, magic is at work here !
    function modifEmprunt($lots,$anciens_lots,$date_emprunt,$date_retour,$mail,$new_date_emprunt,$new_date_retour){
        $mail = protect($mail);
        $date_emprunt = protect($date_emprunt);
        $date_retour = protect($date_retour);
        $new_date_emprunt = protect($new_date_emprunt);
        $new_date_retour = protect($new_date_retour);
        $new_date_emprunt = date("Y-m-d H:m:s", strtotime($new_date_emprunt));
        $new_date_retour = date("Y-m-d H:m:s", strtotime($new_date_retour));
        $date_ajd = date("Y-m-d H:m:s");
        $date_ajd = new DateTime($date_ajd);
        $date_futur = date("Y-m-d H:m:s");
        $date_futur = new DateTime($date_futur);
        date_sub($date_ajd, date_interval_create_from_date_string('1 hour'));
        date_add($date_futur, date_interval_create_from_date_string('1 month'));
        $date_ajd = $date_ajd->format('Ymd');
        $date_futur = $date_futur->format('Ymd');
        $date_emprunt_test = new DateTime($new_date_emprunt);
        $date_emprunt_test = $date_emprunt_test->format('Ymd');
        $date_retour_test = new DateTime($new_date_retour);
        $date_retour_test = $date_retour_test->format('Ymd');
        $nombre = sizeof($lots);
        $disponible=false;
        $compteur =0;
        $date_emprunt_formatée = date("z", strtotime($date_emprunt));
        $date_retour_formatée = date("z", strtotime($date_retour));
        $new_date_emprunt_formatée = date("z", strtotime($new_date_emprunt));
        $new_date_retour_formatée = date("z", strtotime($new_date_retour));
        $string_lots = "";
        if( $date_ajd < $date_emprunt_test && $date_emprunt_test < $date_retour_test && $date_futur > $date_emprunt_test ){
            $anciens = explode('/',$anciens_lots);
            foreach($anciens as $liste){
                $verif = "UPDATE dispo SET ".$liste."=1 WHERE jour>=".($date_emprunt_formatée+1)." AND jour<".($date_retour_formatée+1);
                $result = $GLOBALS["bdd"]->query($verif);
            }
            foreach($lots as $liste){
                $verif = "SELECT ".$liste." from dispo WHERE jour>=".($new_date_emprunt_formatée+1)." AND jour<".($new_date_retour_formatée+1);
                $result = $GLOBALS["bdd"]->query($verif);
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $compteur = $compteur+intval($row[$liste]);
                }
            }
        }
        if($compteur == $nombre*($new_date_retour_formatée-$new_date_emprunt_formatée)){
            $disponible = true;
        }
        if($disponible){
            $query = $GLOBALS["bdd"]->prepare("DELETE FROM inscrits_lots WHERE inscrit_mail=? AND date_emprunt=?");
            $query->bind_param('ss', $mail, $date_emprunt);
            $query->execute();
            $query->close();
            foreach($lots as $liste){
                $string_lots .= $liste." ";
                $query = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits_lots VALUES (?, ?, ?, ?)");
                $liste = protect($liste);
                $query->bind_param('ssss', $mail,$liste, $new_date_emprunt, $new_date_retour);
                $query->execute();
                $query->close();
                $verif = "UPDATE dispo SET ".$liste."=0 WHERE jour>=".($new_date_emprunt_formatée+1)." AND jour<".($new_date_retour_formatée+1);
                $result = $GLOBALS["bdd"]->query($verif);
            }
            $ok = true;
        }
        else{
            foreach($lots as $liste){
                $verif = "UPDATE dispo SET ".$liste."=0 WHERE jour>=".($date_emprunt_formatée+1)." AND jour<".($date_retour_formatée+1);
                $result = $GLOBALS["bdd"]->query($verif);
            }
            $ok = false;
        }
        return $ok;

        //PARTIE ENVOI DE MAIL, A NE TESTER QU'AVEC UN SERVEUR SMTP FONCTIONNEL
        //Ca fait tout planter méchamment sinon
        /*if($ok){
            $verif = "SELECT mail from admin WHERE responsable_emprunt=1";
            $result = $GLOBALS["bdd"]->query($verif);
            setlocale (LC_TIME, 'fr_FR','fra');
            $new_date_emprunt = utf8_encode(strftime("%d %b %Y",strtotime($new_date_emprunt)));
            $new_date_retour = utf8_encode(strftime("%d %b %Y",strtotime($new_date_retour)));
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                mail($row["mail"],"Un emprunt a été modifié !", "Les lots ".$string_lots." \r\n seront empruntés par ".$nom." ".$prenom." \r\n du ".$new_date_emprunt." au ".$new_date_retour);
            }
            return $ok;
        }
        else{
            return $ok;
        }*/
    }


    //FONCTION SUPPRESSION D'UN EMPRUNT(UTILISATEUR)
    function supprEmprunt($mail,$date){
        $mail = protect($mail);
        $date = explode('/',$date);
        $date_emprunt = date("Y-m-d H:m:s", strtotime(protect($date[0])));
        $date_retour = date("Y-m-d H:m:s", strtotime(protect($date[1])));
        $query = "SELECT * FROM inscrits_lots WHERE inscrit_mail='".$mail."' AND date_emprunt='".$date_emprunt."'";
        $result = $GLOBALS["bdd"]->query($query);
        $date_emprunt_formatée = date("z", strtotime($date_emprunt));
        $date_retour_formatée = date("z", strtotime($date_retour));
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $lot = $row["lots"];
            $query2 = "UPDATE dispo SET ".$lot."=1 WHERE jour>=".($date_emprunt_formatée+1)." AND jour<".($date_retour_formatée+1);
            $query2 = $GLOBALS["bdd"]->query($query2);
        }
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM inscrits_lots WHERE inscrit_mail=? AND date_emprunt=?");
        $query->bind_param('ss',$mail,$date_emprunt);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION DE RECUPERATION DES EMPRUNTS EFFECTUES PAR UN INSCRIT
    function recupEmprunt($mail){
        $mail = protect($mail);
        $query = "SELECT * FROM inscrits_lots WHERE inscrit_mail='".$mail."'";
        return $GLOBALS["bdd"]->query($query);
    }

    //FONCTION DE RECUPERATION DES EMPRUNTS NON EFFECTUES ENCORES
    function recupEmpruntAjd($mail){
        $mail = protect($mail);
        $date_ajd = date("Y-m-d H:m:s");
        $query = "SELECT * FROM inscrits_lots WHERE inscrit_mail='".$mail."' and date_emprunt>='".$date_ajd."'  GROUP BY date_emprunt AND date_retour";
        return $GLOBALS["bdd"]->query($query);
    }

    //FONCTION DE RECUPERATION DES EMPRUNTS EFFECTUES PAR UN INSCRIT à UNE DATE PRECISE
    function recupEmpruntDate($mail,$date){
        $mail = protect($mail);
        $date = explode('/', $date);
        $query = "SELECT lots FROM inscrits_lots WHERE inscrit_mail='".$mail."' AND date_emprunt='".$date[0]."' AND date_retour='".$date[1]."'";
        return $GLOBALS["bdd"]->query($query);
    }

    //FONCTION DE RECUPERATION DES EMPRUNTS ET REGROUPEMENTS SOUS FORME DE LOT
    function recupEmpruntLot(){
        $query = "SELECT inscrit_mail, GROUP_CONCAT(lots) as concat_lots, date_emprunt, date_retour FROM inscrits_lots GROUP BY date_emprunt";
        return $GLOBALS["bdd"]->query($query);
    }




//################################################################################################################################################################



    //FONCTIONS GESTION DES PROJECTIONS



    //FONCTION DE RECUPERATION DES INSCRITS A UNE PROJECTION, CREE UN DOCUMENT XLS TELECHARGEABLE SUR LE SERVEUR
    function recupInscrit($projection){
        $query = $GLOBALS["bdd"]->prepare("SELECT inscrit_mail from projections_inscrits WHERE projection=?");
        $query->bind_param("s",$projection);
        $query->execute();
        $query->store_result();
        $query->bind_result($mail);
        echo('<table class="table table-striped <!--table-bordered-->"><thead><tr><th>#</th><th class="col-md-6">Nom</th><th class="col-md-6">Prenom</th><th class="col-md-4">Classe</th></tr></thead>');
        $table = "<html><body><table><tr><td><b>Nom</b></td><td><b>Prenom</b></td><td><b>Classe</b></td></tr>";
        $i=1;
        while ($query->fetch())
        {

            $query2 = $GLOBALS["bdd"]->prepare("SELECT nom , prenom , classe from inscrits WHERE mail = ?");
            $query2->bind_param("s",$mail);
            $query2->execute();
            $query2->store_result();
            $query2->bind_result($nom,$prenom,$classe);
            while ($query2->fetch())
            {
                $table = $table."<tr>";
                $table = $table."<td>".utf8_decode($nom)."</td><td>".utf8_decode($prenom)."</td><td>".utf8_decode($classe)."</td>";
                $table = $table."</tr>";
                echo('<tr><td class="inscrit_proj_list">'.$i.'</td><td class="inscrit_proj_list">'.$nom.'</td><td class="inscrit_proj_list">'.$prenom.'</td><td class="inscrit_proj_list">'.$classe.'</td></tr>');
                $i++;
            }
            $query2->close();
        }
        $query->close();
        $table = $table."</table></body></html>";
        $replace = array("'",'"'," ");
        $projection = str_replace($replace,'_',$projection);
        $projection = stripslashes($projection);
        $file = ("../xls/inscrits_".$projection.".xls");
        if(!$myfile = fopen($file, "w+"))
        {
            print("erreur: ");
            print("le fichier n'existe pas!\n");
            exit;
        }
        fwrite($myfile,$table,strlen($table));
        fclose($myfile);
        echo('</table>');
        return true;
    }


    //FONCTION RECUPERANT TOUTES LES PROJECTIONS EXISTANTES
    function recupProj(){
        $query = "SELECT * from projections";
        return $GLOBALS["bdd"]->query($query);
    }

    //FONCTION RECUPERANT TOUTES LES PROJECTIONS EXISTANTES PAR ORDRE DE PROJECTION DESCENDANT
    function recupProjDesc(){
        $query = "SELECT * from projections ORDER BY date_projection DESC";
        return $GLOBALS["bdd"]->query($query);
    }

    //FONCTION RECUPERANT UNE PROJECTION EN PARTICULIER
    function recupUniqueProj($nom){
        $query = 'SELECT * from projections WHERE nom="'.$nom.'"';
        return $GLOBALS["bdd"]->query($query);
    }

    //FONCTION PERMETTANT D'ACTIVER UNE PROJECTION (MET LA PROJECTION AFFICHEE SUR LA PAGE CINE)
    function activateProj($nom){
        $query = $GLOBALS["bdd"]->prepare("UPDATE projections SET active='0' WHERE active='1'");
        $query->execute();
        $query->close();
        $query = $GLOBALS["bdd"]->prepare("UPDATE projections SET active='1' WHERE nom=?");
        $query->bind_param('s',$nom);
        $query->execute();
        $query->close();
        return true;
    }

    //FONCTION RECUPERANT LA PROJECTION ACTIVE ACTUELLE
    function recupProjActive(){
        $query ="SELECT * FROM projections WHERE active='1'";
        return $GLOBALS["bdd"]->query($query);
    }



    //FONCTION D'AJOUT D'UNE PROJECTION A LA BDD
    function addProj($nom,$date_release,$date_projection,$description,$commentaires,$affiche,$afficheback,$langue,$prix,$bande_annonce){
        $date_release .= ":00";
        $date_projection .= ":00";
        $date_release = strtotime(str_replace('/', '-',$date_release));
        $date_projection = strtotime(str_replace('/', '-',$date_projection));

        $query = $GLOBALS["bdd"]->prepare("INSERT INTO `projections`(`nom`, `date_release`, `date_projection`, `description`, `commentaires`, `affiche`, `active`, `back_affiche`, `langue`, `prix`, `bande_annonce`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $active = 0;
        $query->bind_param('ssssssissds',$nom,$date_release,$date_projection,$description,$commentaires,$affiche,$active,$afficheback,$langue,$prix,$bande_annonce);
        $query->execute();
        $query->close();
        $replace = array("'",'"'," ",'\'','\"');
        $nom = str_replace($replace,'_',$nom);
        touch('../xls/inscrits_'.$nom.'.xls');
        chmod('../xls/inscrits_'.$nom.'.xls', 0777);
        return true;
    }


    //FONCTION DE SUPPRESSION D'UNE PROJECTION DE LA BDD
    function supprProj($nom){
        $query2 = $GLOBALS["bdd"]->prepare("SELECT  `affiche` FROM  `projections` WHERE  `nom` = ?");
        $query2->bind_param("s",$nom);
        $query2->execute();
        $query2->store_result();
        $query2->bind_result($imagetodelete);
        $query2->fetch();
        unlink($imagetodelete);

        $query2->close();

        $query2 = $GLOBALS["bdd"]->prepare("SELECT  `back_affiche` FROM  `projections` WHERE  `nom` = ? ");
        $query2->bind_param("s",$nom);
        $query2->execute();
        $query2->store_result();
        $query2->bind_result($imagetodelete);

        $query2->fetch();
        unlink($imagetodelete);
        $query2->close();

        $query = $GLOBALS["bdd"]->prepare("DELETE FROM projections WHERE nom=?");
        $query->bind_param('s',$nom);
        $query->execute();
        $query->close();
        $query2 = $GLOBALS["bdd"]->prepare("DELETE FROM projections_inscrits WHERE projection=?");
        $query2->bind_param('s',$nom);
        $query2->execute();
        $query2->close();
        $query2 = $GLOBALS["bdd"]->prepare("DELETE FROM desinscription WHERE projection=?");
        $query2->bind_param('s',$nom);
        $query2->execute();
        $query2->close();
        $replace = array("'",'"'," ",'\'','\"');
        $nom = str_replace($replace,'_',$nom);
        unlink('../xls/inscrits_'.$nom.'.xls');
        return true;
    }

    //FONCTION DE MODIFICATION D'UNE PROJECTION
    function modifProj($nom,$date_release,$date_projection,$description,$commentaires,$affiche,$ancien_nom,$afficheback,$langue,$prix,$bande_annonce){
        $date_release .= ":00";
        $date_projection .= ":00";
        $date_release = strtotime(str_replace('/', '-',$date_release));
        $date_projection = strtotime(str_replace('/', '-',$date_projection));

        if($afficheback!=''){//magic at work don't touch, even with your eyes
            $query3 = $GLOBALS["bdd"]->prepare("SELECT `back_affiche` FROM  `projections` WHERE  `nom` = ? ");
            $query3->bind_param("s",$ancien_nom);
            $query3->execute();
            $query3->store_result();
            $query3->bind_result($imagetodelete);
            $query3->fetch();
            unlink($imagetodelete);

            $query = $GLOBALS["bdd"]->prepare("UPDATE `projections` SET `back_affiche`=? WHERE nom=?");
            $query->bind_param('ss',$afficheback,$ancien_nom);
            $query->execute();
            $query->close();
        }


        if($affiche!=''){
            $query2 = $GLOBALS["bdd"]->prepare("SELECT  `affiche` FROM  `projections` WHERE  `nom` = ? ");
            $query2->bind_param("s",$ancien_nom);
            $query2->execute();
            $query2->store_result();
            $query2->bind_result($imagetodelete);
            $query2->fetch();
            unlink($imagetodelete);

            $query = $GLOBALS["bdd"]->prepare("UPDATE projections SET nom=?, date_release=?, date_projection=?, description=?, affiche=?, commentaires=?, `langue`=?, `prix`=?, `bande_annonce`=? WHERE nom=?");
            $query->bind_param('siisssssds',$nom,$date_release,$date_projection,$description,$affiche,$commentaires,$langue,$prix,$bande_annonce,$ancien_nom);
        }
        else
        {
             $query = $GLOBALS["bdd"]->prepare("UPDATE projections SET nom=?, date_release=?, date_projection=?, description=?, commentaires=?, `langue`=?, `prix`=?, `bande_annonce`=? WHERE nom=?");
            $query->bind_param('siisssdss',$nom,$date_release,$date_projection,$description,$commentaires,$langue,$prix,$bande_annonce,$ancien_nom);
        }

        $query->execute();
        $query->close();



        $query = $GLOBALS["bdd"]->prepare("UPDATE projections_inscrits SET projection=? WHERE projection=?");
        $query->bind_param('ss',$nom,$ancien_nom);
        $query->execute();
        $query->close();
        $query = $GLOBALS["bdd"]->prepare("UPDATE desinscription SET projection=? WHERE projection=?");
        $query->bind_param('ss',$nom,$ancien_nom);
        $query->execute();
        $query->close();
        $replace = array("'",'"'," ");
        $ancien_nom = str_replace($replace,'_',$ancien_nom);
        $ancien_nom = stripslashes($ancien_nom);
        $nom = str_replace($replace,'_',$nom);
        $nom = stripslashes($nom);
        unlink('../xls/inscrits_'.$ancien_nom.'.xls');
        touch('../xls/inscrits_'.$nom.'.xls');
        chmod('../xls/inscrits_'.$nom.'.xls', 0777);
        return true;
    }





//################################################################################################################################################################



    //FONCTION GESTION DES ADMINISTRATEURS


    //FONCTION VERIFIANT SI L'UTILISATEUR EST CONNU OU NON
    function recupID($identifiant){
        $identifiant = protect($identifiant);
        $query = "SELECT * FROM admin WHERE identifiant=".$identifiant;
        return $GLOBALS["bdd"]->query($query);
    }

    //FONCTION VERIFIANT SI L'UTILISATEUR EST CONNU OU NON
    function recupAdmin(){
        $query = "SELECT identifiant, responsable_emprunt FROM admin";
        return $GLOBALS["bdd"]->query($query);
    }


    //FONCTION D'AJOUT D'UN ADMIN DANS LA BASE
    function addAdmin($identifiant,$mdp,$mail,$respons){
        $identifiant = protect($identifiant);
        $mdp = protect($mdp);
        $mail = protect($mail);
        $respons = protect($respons);
        $mdp = password_hash($mdp,PASSWORD_DEFAULT);
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO admin VALUES(?,?,?,?)");
        $query->bind_param('sssi',$identifiant,$mdp,$mail,$respons);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION DE SUPPRESSION D'UN ADMIN DANS LA BASE
    function supprAdmin($identifiant){
        $identifiant = protect($identifiant);
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM admin WHERE identifiant=?");
        $query->bind_param('s',$identifiant);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION DE CHANGEMENT DE MOT DE PASSE POUR L'ADMINISTRATEUR COURANT
    function modifMDP($identifiant, $mdp, $oldMDP){
        $mdp = protect($mdp);
        $query = $GLOBALS["bdd"]->prepare("SELECT * FROM admin WHERE identifiant=?");
        $query->bind_param("s",$identifiant);
        $query->execute();
        $query->store_result();
        $query->bind_result($hash);
        $query->fetch();
        $query->close();

        if(password_verify($oldMDP, $hash)){
            $mdp = password_hash($mdp,PASSWORD_DEFAULT);
            $query = $GLOBALS["bdd"]->prepare("UPDATE admin SET mdp=? WHERE identifiant=?");
            $query->bind_param('ss',$mdp,$identifiant);
            $query->execute();
            $query->close();
            return true;
        }
        return false;
    }

    //FONCTION DE CHANGEMENT DE RESPONSABILITES POUR UN ADMINISTRATEUR (devenir responsable emprunts pour le moment)
    function changeAdmin($identifiant, $respons){
        $identifiant = protect($identifiant);
        $respons = protect($respons);
        $query = $GLOBALS["bdd"]->prepare("UPDATE admin SET responsable_emprunt=? WHERE identifiant=?");
        $query->bind_param('is',$respons,$identifiant);
        $query->execute();
        $query->close();
        return true;
    }


//################################################################################################################################################################

    //FONCTIONS GESTION DES LOTS

    //FONCTION D'AJOUT D'UN LOT
    function addLot($identifiant, $composition,$image,$caution){
        $identifiant = protect($identifiant);
        $composition = protect($composition);
        $caution = protect($caution);
        $disponible = 1;
        $image = protect($image);
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO lots VALUES(?,?,?,?)");
        $query->bind_param('sssi',$identifiant,$composition,$image,$caution);
        $query->execute();
        $query->close();
        addDispoLot($identifiant);
        return true;
    }


    //FONCTION ALTERANT LA TABLE DISPONIBILITES POUR RAJOUTER LE NOUVEAU LOT
    function addDispoLot($identifiant){
        $identifiant = protect($identifiant);
        $query = "ALTER TABLE dispo ADD ".$identifiant." BOOLEAN NOT NULL DEFAULT 1";
        $query = $GLOBALS["bdd"]->query($query);
        return true;
    }

    //FONCTION DE SUPPRESSION D'UN LOT
    function supprLot($identifiant){
        $identifiant = protect($identifiant);
        $query = "SELECT image from lots WHERE id='".$identifiant."'";
        $result = $GLOBALS["bdd"]->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $imagetodelete = $row["image"];
        }
        unlink($imagetodelete);
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM lots WHERE id=?");
        $query->bind_param('s',$identifiant);
        $query->execute();
        $query->close();
        supprDispoLot($identifiant);
        return true;
    }

    //FONCTION ALTERANT LA TABLE DISPONIBILITES POUR SUPPRIMER LE LOT
    function supprDispoLot($identifiant){
        $identifiant = protect($identifiant);
        $query = "ALTER TABLE dispo DROP ".$identifiant;
        $query = $GLOBALS["bdd"]->query($query);
        return true;
    }


    //FONCTION DE MODIFICATION D'UN LOT
    function modifLot($identifiant,$composition, $caution, $image,$ancien_identifiant){
        $identifiant = protect($identifiant);
        $composition = protect($composition);
        $ancien_identifiant = protect($ancien_identifiant);
        $caution = protect($caution);
        if(empty($image)){
            $query = "SELECT image from lots WHERE id='".$ancien_identifiant."'";
            $result = $GLOBALS["bdd"]->query($query);
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $image = $row["image"];
            }
            $query = $GLOBALS["bdd"]->prepare("UPDATE `lots` SET `id`=?,`composition`=?,`caution`=? WHERE `id`=?");
        $query->bind_param('ssis',$identifiant,$composition,$caution,$ancien_identifiant);
        }else{
            $query = "SELECT image from lots WHERE id='".$ancien_identifiant."'";
            $result = $GLOBALS["bdd"]->query($query);
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $imagetodelete = $row["image"];
            }
            unlink($imagetodelete);
            $query = $GLOBALS["bdd"]->prepare("UPDATE `lots` SET `id`=?,`composition`=?,`image`=?,`caution`=? WHERE `id`=?");
        $query->bind_param('sssis',$identifiant,$composition,$image,$caution,$ancien_identifiant);
        }

        $query->execute();
        $query->close();
        modifDispoLot($identifiant,$ancien_identifiant);
        return true;
    }

    //FONCTION ALTERANT LA TABLE DISPONIBILITES POUR MODIFIER LA DISPONIBILITE DU LOT
    function modifDispoLot($identifiant,$identifiant_old){
        $identifiant = protect($identifiant);
        $identifiant_old = protect($identifiant_old);
        $query = "ALTER TABLE dispo CHANGE ".$identifiant_old." ".$identifiant." BOOLEAN NOT NULL DEFAULT 1";
        $query = $GLOBALS["bdd"]->query($query);
        return true;
    }



    //FONCTION GERANT LA RENDU DES LOTS
    function renduLot($identifiant,$lots,$date_emprunt,$date_retour){
        $identifiant = protect($identifiant);
        $lots = protect($lots);
        $date_emprunt = protect($date_emprunt);
        $date_retour = protect($date_retour);
        $date_emprunt = date("Y-m-d H:m:s", strtotime($date_emprunt));
        $date_retour = date("Y-m-d H:m:s", strtotime($date_retour));
        $date_emprunt_formatée = date("z", strtotime($date_emprunt));
        $date_retour_formatée = date("z", strtotime($date_retour));
        $table_lots = explode(',',$lots);
        foreach($table_lots as $liste){
            $query = "UPDATE dispo SET ".$liste."=1 WHERE jour>=".($date_emprunt_formatée+1)." AND jour < ".($date_retour_formatée+1);
            $query = $GLOBALS["bdd"]->query($query);
        }
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM inscrits_lots WHERE inscrit_mail=? AND date_emprunt=? AND date_retour=?");
        $query->bind_param('sss',$identifiant,$date_emprunt,$date_retour);
        $query->execute();
        $query->close();
        return true;
    }

    //FONCTION DE RECUPERATION DE TOUT LES LOTS
    function renduLotCalendar($date_start,$date_end){
        $out = array();
        $i=1;
        $query = "SELECT * FROM inscrits_lots WHERE date_emprunt>='".$date_start."' AND date_retour<'".$date_end."' ORDER BY date_emprunt";
        $result = $GLOBALS["bdd"]->query($query);
        while ($row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $lot = $row["lots"];
            $id = $row["inscrit_mail"];
            $date_emprunt = $row["date_emprunt"];
            $date_emprunt_formatée = date_create_from_format("Y-m-d H:m:s", $date_emprunt);
            $date_emprunt_formatée = date_format($date_emprunt_formatée,'U');
            $date_retour = $row["date_retour"];
            $date_retour_formatée = date_create_from_format("Y-m-d H:m:s", $date_retour);
            $date_retour_formatée = date_format($date_retour_formatée,'U');
            $modulo = $i % 6;
            // On fait varier les couleurs pour le calendrier bootstrap
            switch ($modulo) {
                case 0:
                    $couleur = "important";
                    break;
                case 1:
                    $couleur = "success";
                    break;
                case 2:
                    $couleur = "warning";
                    break;
                case 3:
                    $couleur = "info";
                    break;
                case 4:
                    $couleur = "inverse";
                    break;
                case 5:
                    $couleur = "special";
                    break;
            }
            // Le retour se fait sous format JSON avec les attributs suivants
            $out[] = array(
                'id' => $i,
                'title' => 'Le lot '.$lot.' a été emprunté par '.$id,
                'url' => '',
                "class" => "event-".$couleur, //pour la couleur dans le calendrier
                "text" => $lot,
                'start' => strtotime($date_emprunt).'000', // Milliseconds
                'end' => strtotime($date_retour).'000' // Milliseconds
            );
            $i++;
        }
        echo json_encode(array('success' => 1, 'result' => $out));
    }

    //FONCTION DE RECUPERATION DE TOUT LES LOTS
    function recupLot(){
        $query = "SELECT * from lots ORDER BY id";
        return $GLOBALS["bdd"]->query($query);
    }

    function recupUniqueLot($id){
        $id = protect($id);
        $query = "SELECT * from lots WHERE id='".$id."'";
        return $GLOBALS["bdd"]->query($query);
    }



    function dejaInscrit($id){
        $query = "SELECT * FROM inscrits where identifiant='".protect($id)."'";
        return $GLOBALS["bdd"]->query($query);
    }

?>
