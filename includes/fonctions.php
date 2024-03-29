<?php

    require_once('../conf/config.php');
    //Attention ! penser à remettre Mail.php en fonction après upload
    //require_once "Mail.php";
    ini_set('upload_max_filesize', '10M');
    ini_set('post_max_size', '10M');
    ini_set('max_input_time', 300);
    ini_set('max_execution_time', 300);
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
        $subject = utf8_decode($subject);
        $message = utf8_decode($message);


//        PARTIE ENVOI DE MAIL AVEC PEAR MAIL


/*        $from_test = "<no-reply@moviezen.fr>";
        $to_test = $to;
        $subject_test = $subject;
        $message_test = '

       <html><body><p>Vous voulez vous désinscrire pour la séance "'.$seance.'" du '.$date.'.</p>
       <a href="www.moviezen.fr/views/desinscription.php?codedesin='.$temp.'">Cliquez ici pour vous désinscrire</a></body></html>;

        $host = "localhost";
        $port = "25";
        $mime = new Mail_mime();
        $mime->setHTMLBody($message_test);
        $message_test = $mime->get();
        $headers_test = array("From" => $from_test, 'To'=>$to_test,'Subject'=>$subject_test);
        $smtp = Mail::factory('smtp',
                             array('host' => $host,
                                  'port' => $port,
                                  ));
        $mail_test = $smtp->send($to_test,$headers_test,$message_test);

*/



         // Envoi
         return mail($to, $subject, $message, $headers);
    }
//################################################################################################################################################################

        //FONCTIONS GESTION DES BLACKLIST
        /*
        Tebles blacklist :
            Modulaires:
                -Une table pour les emprunts (pour les mauvais payeurs ou personnes à problèmes)
                -Une table pour les projections (pour les trolls et autres)
                -Une table pour les critiques (pour ceux qui s'amuseraient à publier du gros caca)
            Toujours le même format :
                -login ISEN : login ISEN (mail) de l'ISEN de la personne blacklistée

            Pas plus simple

        */
        //Reste à implémenter le système sur les pages cine.php, emprunts.php et critiques.php
        function addToEmpruntsBlacklist($mail){
            $query = $GLOBALS["bdd"]->prepare("INSERT INTO emprunts_blacklist (mail) VALUES (?)");
            $query->bind_param('s',$mail);
            $query->execute();
            $query->close();
        }

        function deleteFromEmpruntsBlacklist($mail){
            $query = $GLOBALS["bdd"]->prepare("DELETE FROM emprunts_blacklist WHERE mail=?");
            $query->bind_param('s',$mail);
            $query->execute();
            $query->close();
        }

        function addToProjectionsBlacklist($mail){
            $query = $GLOBALS["bdd"]->prepare("INSERT INTO projections_blacklist (mail) VALUES (?)");
            $query->bind_param('s',$mail);
            $query->execute();
            $query->close();
        }

        function deleteFromProjectionsBlacklist($mail){
            $query = $GLOBALS["bdd"]->prepare("DELETE FROM projections_blacklist WHERE mail=?");
            $query->bind_param('s',$mail);
            $query->execute();
            $query->close();
        }

        function addToCritiquesBlacklist($mail){
            $query = $GLOBALS["bdd"]->prepare("INSERT INTO critiques_blacklist (mail) VALUES (?)");
            $query->bind_param('s',$mail);
            $query->execute();
            $query->close();
        }

        function deleteFromCritiquesBlacklist($mail){
            $query = $GLOBALS["bdd"]->prepare("DELETE FROM critiques_blacklist WHERE mail=?");
            $query->bind_param('s',$mail);
            $query->execute();
            $query->close();
        }
//################################################################################################################################################################

    //FONCTIONS GESTION DES CRITIQUES
    /*
    Structure de la table Critique:
        -auteur : login ISEN de l'auteur de la critique, non modifiable (String)
        -idCritique : Identifiant auto incrémenté de la critique (Entier)
        -titre : Titre du film (String)
        -critique : bloc texte de la critique (String)


        Optionnel :
            -Affiche ? (String, chemin vers l'image récupérée)
            -Synopsys ?
            -Date de sortie ?
            -Note moyenne ?
            -Réalisateur ? (String)
            -Acteurs ?  (String)
    //PENSER A INCLURE LE POC CONCERNANT LA RECUPERATION AUTOMATIQUE DES FILMS ==> récupération automatique des affiches, réalisateurs et acteurs
    */

    function ajoutCritique($login,$idCritique,$titre,$critique){
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO critiques (auteur,idCritique,titre,critique) VALUES (?,?,?,?)");
        $query->bind_param('siss',$login,$idCritique,$titre,$critique);
        $query->execute();
        $query->close();
    }

    function deleteCritique($login,$idCritique){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM critiques WHERE auteur=? AND idCritique=?");
        $count->bind_param('si',$login,$idCritique);
        $count->execute();
        $query->close();
    }

    function updateCritique($login,$idCritique,$newTitre,$newCritique){
        $query = $GLOBALS["bdd"]->prepare("UPDATE critiques SET titre=?, critique=? WHERE auteur=? AND idCritique=?");
        $count->bind_param('sssi',$newTitre,$newCritique,$login,$idCritique);
        $count->execute();
        $query->close();
    }


    //FONCTION DE RECUPERATION DES INFORMATIONS SUR UN FILM VIA UN SIMPLE FORMULAIRE
    /*
    Entrées :
        $titre = String, contenant le titre ou morceau du titre du film ("Skyfall", "La reine des")

    Sorties :
        $final = tableau contenant toutes les informations sur les films trouvés (titre, affiche, synopsys, Note moyenne)
                Les informations non disponibles sont le réalisateur et les acteurs.
    */
    function recupInfos($titre){
        $url=utf8_encode("https://api.themoviedb.org/3/search/movie?api_key=ebcdbb93668857d48040b4bbb0695a32&query=".str_replace(" ","%20",$titre)."&language=fr&include_image_language=fr");
        // Tableau contenant les options de téléchargement
        $options=array(
            CURLOPT_URL            => $url, // Url cible (l'url la page que vous voulez télécharger)
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
        );

        // Création d'un nouvelle ressource cURL
        $CURL=curl_init();
        // Configuration des options de téléchargement
        curl_setopt_array($CURL,$options);
        // Exécution de la requête
        $content=curl_exec($CURL);      // Le contenu téléchargé est enregistré dans la variable $content
        // Fermeture de la session cURL
        curl_close($CURL);

        $obj = json_decode($content,true);
        $final = array();
        $i = 0;
        foreach ($obj["results"] as $key) {
            $final[$i]['release_date'] = $key['release_date'];
            $final[$i]['affiche'] = "http://image.tmdb.org/t/p/w500/".$key['poster_path'];
            $final[$i]['overview'] = $key['overview'];
            $final[$i]['title'] = $key['title'];
            $final[$i]['vote_average'] = $key['vote_average'];
            $i++;
        }
        return $final;
    }


//################################################################################################################################################################

    //FONCTIONS GESTION DES INSCRITS


    //AJOUT D'INSCRITS A UNE PROJECTION (UTILISATEUR)
    function ajoutInscrit($nom,$prenom,$mail,$classe,$projection){
        //On regarde si l'utilisateur est déja marqué comme inscrit
        $count = $GLOBALS["bdd"]->prepare("SELECT COUNT( * ),nom, prenom,tel,classe FROM  `inscrits` WHERE  `mail` =?");
        $count->bind_param('s',$mail);
        $count->execute();

        $count->store_result();
        $count->bind_result($count,$temp_nom,$temp_prenom,$temp_tel,$temp_classe);

        $count->fetch();
        $count->close();
        //Si une ligne est retournée et qu'une des infos ne correspond plus à celles enregistrées, on fait un UPDATE
        if($temp==1 and ($nom !==$temp_nom or $prenom !== $temp_prenom or $tel!==$temp_tel or $classe!==$temp_classe)){
            $query = $GLOBALS["bdd"]->prepare("UPDATE inscrits SET nom=?, prenom=?, tel=?, classe=? WHERE mail=?");
            $query->bind_param('sssss', $nom, $prenom, $tel, $classe,$mail);
            $query->execute();
            $query->close();

        }else{
            $query = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits VALUES (?, ?, '', ?, ?)");
            $query->bind_param('ssss', $nom,$prenom,$mail, $classe);
            $query->execute();
            $query->close();

        }
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
        $query->bind_param('sss', $mail, $prenom, $ancien_mail);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION SUPPRESSION D'INSCRITS A UNE PROJECTION  (UTILISATEUR)
    function supprInscrit($mail,$projection){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM projections_inscrits WHERE inscrit_mail=? and projection=?");
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


/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////// FONCTIONS D'EMPRUNTS VERSION SALE ///////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
    //FONCTION AJOUT D'EMPRUNT (TEST AVEC TABLE DISPONIBILITES)
    // PS : ca marche et logiquement, tous les cas sont prévus sooooo
    // Don't touch, magic is at work here !
    function ajoutEmprunt2($nom,$prenom,$tel,$mail, $classe,$lots,$date_emprunt,$date_retour){
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits VALUES (?, ?, ?, ?, ?)");
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
        date_add($date_futur, date_interval_create_from_date_string('3 months'));
        $date_ajd = $date_ajd->format('Ymd');
        $date_futur = $date_futur->format('Ymd');
        $date_emprunt_test = new DateTime($date_emprunt);
        $date_emprunt_test = $date_emprunt_test->format('Ymd');
        $date_retour_test = new DateTime($date_retour);
        $date_retour_test = $date_retour_test->format('Ymd');
        $string_lots = "";
        if( $date_ajd < $date_emprunt_test && $date_emprunt_test < $date_retour_test && $date_futur > $date_emprunt_test ){
            foreach($lots as $liste){
                $liste = protect($liste);
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
                $liste = protect($liste);
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
        date_add($date_futur, date_interval_create_from_date_string('3 months'));
        $date_ajd = $date_ajd->format('Ymd');
        $date_futur = $date_futur->format('Ymd');
        $date_emprunt_test = new DateTime($date_emprunt);
        $date_emprunt_test = $date_emprunt_test->format('Ymd');
        $date_retour_test = new DateTime($date_retour);
        $date_retour_test = $date_retour_test->format('Ymd');
        if( $date_ajd < $date_emprunt_test && $date_emprunt_test < $date_retour_test && $date_futur > $date_emprunt_test ){
            foreach($lots as $liste){
                $liste = protect($liste);
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
        $date = explode('/',$date);
        $date_emprunt = date("Y-m-d H:m:s", strtotime($date[0]));
        $date_retour = date("Y-m-d H:m:s", strtotime($date[1]));
        $date_emprunt_formatée = date("z", strtotime($date_emprunt));
        $date_retour_formatée = date("z", strtotime($date_retour));
        $query = $GLOBALS["bdd"]->prepare("SELECT lots FROM inscrits_lots WHERE inscrit_mail=? AND date_emprunt=?");
        $query->bind_param('ss',$mail,$date_emprunt);
        $query->execute();
        $query->store_result();
        $query->bind_result($lot);
        while($query->fetch()){
            $query2 = "UPDATE dispo SET ".$lot."=1 WHERE jour>=".($date_emprunt_formatée+1)." AND jour<".($date_retour_formatée+1);
            $query2 = $GLOBALS["bdd"]->query($query2);
        }
        $query->close();
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM inscrits_lots WHERE inscrit_mail=? AND date_emprunt=?");
        $query->bind_param('ss',$mail,$date_emprunt);
        $query->execute();
        $query->close();
        return true;
    }

    //FONCTION DE RECUPERATION DES EMPRUNTS EFFECTUES PAR UN INSCRIT
    function recupEmprunt($mail){
        $tab = array();
        $final = array();
        $i=0;
        $query = $GLOBALS["bdd"]->prepare("SELECT * FROM inscrits_lots WHERE inscrit_mail=?");
        $query->bind_param('s',$mail);
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["inscrit_mail"],$tab["lots"],$tab["date_emprunt"],$tab["date_retour"]);
        while($query->fetch()){
            $final[$i]["inscrit_mail"] = $tab["inscrit_mail"];
            $final[$i]["lots"] = $tab["lots"];
            $final[$i]["date_emprunt"] = $tab["date_emprunt"];
            $final[$i]["date_retour"] = $tab["date_retour"];
            $i++;
        }
        $query->close();
        return $final;
    }

    //FONCTION DE RECUPERATION DES EMPRUNTS NON EFFECTUES ENCORES
    function recupEmpruntAjd($mail){
        $tab = array();
        $final = array();
        $i=0;
        $date_ajd = date("Y-m-d H:m:s");
        $query = $GLOBALS["bdd"]->prepare("SELECT * FROM inscrits_lots WHERE inscrit_mail=? and date_emprunt>=?  GROUP BY date_emprunt");
        $query->bind_param('ss',$mail,$date_ajd);
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["inscrit_mail"],$tab["lots"],$tab["date_emprunt"],$tab["date_retour"]);
        while($query->fetch()){
            $final[$i]["inscrit_mail"] = $tab["inscrit_mail"];
            $final[$i]["lots"] = $tab["lots"];
            $final[$i]["date_emprunt"] = $tab["date_emprunt"];
            $final[$i]["date_retour"] = $tab["date_retour"];
            $i++;
        }
        $query->close();
        return $final;
    }

    //FONCTION DE RECUPERATION DES EMPRUNTS EFFECTUES PAR UN INSCRIT à UNE DATE PRECISE
    function recupEmpruntDate($mail,$date){
        $tab = array();
        $i=0;
        $query = $GLOBALS["bdd"]->prepare("SELECT lots FROM inscrits_lots WHERE inscrit_mail=? AND date_emprunt=? AND date_retour=?");
        $query->bind_param('sss',$mail,$date[0],$date[1]);
        $query->execute();
        $query->store_result();
        $query->bind_result($temp);
        while($query->fetch()){
            $tab[$i] = $temp;
            $i++;
        }
        $query->close();
        return $tab;
    }


//////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// FONCTIONS D'EMPRUNTS VERSION TIMESTAMP /////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
    //FONCTION AJOUT EMPRUNT UTILISATEUR VERSION TIMESTAMP
    //Paramètres :
    // nom : nom de l'utilisateur qui emprunte (chaine)
    // prenom : prenom de l'utilisateur qui emprunte (chaine)
    // tel : numéro de téléphone de l'utilisateur qui emprunte (chaine)
    // mail : identifiant mail ISEN de l'utilisateur qui emprunte (chaine)
    // classe : classe de l'utilisateur (chaine)
    // lots : chaine de caractères du style "A,B,C,D,E"
    // date_emprunt : date de début de l'emprunt sous forme de timestamp
    // date_retour : date de retour de l'emprunt sous forme de timestamp

    // Retour :
    //  chaine : vide si aucune erreur, erreur rencontrée sinon (lot X déja emprunté durant la période, lot X inexistant, etc...)
    function ajoutEmpruntTimestamp($nom,$prenom,$tel,$mail,$classe,$lots,$date_emprunt,$date_retour){
        //ON VERIFIE QUE LES DATES D'EMPRUNTS NE SONT PAS ABERRANTES (Emprunt dans trois mois, etc....)
        $timestamp_ajd = time();
        $timestamp_ajd = $timestamp_ajd - 86400; // On ote un jour à la date d'aujourd'hui, car c'est la limite basse de l'emprunt (impossible d'emprunter pour hier)
        $timestamp_futur = $timestamp_ajd + (86400*91); // On fixe comme limite d'emprunt 3 mois dans le futur (86400 étant le nombre de secondes par jour)
        //Si les dates sont correctes, on autorise la vérification de la disponibilité des lots
        if($date_emprunt > $timestamp_ajd && $date_retour > $timestamp_ajd && $date_emprunt < $timestamp_futur && $date_retour < $timestamp_futur){
            $chaine = "";
            //ON CREE UNE CHAINE POUR LE SELECT DU TYPE "lots='A' OR lots='B' OR lots='C' OR "
            foreach($lots as $lot){
                $lot = protect($lot);
                $chaine .= "lots='".$lot."' OR ";
            }
            $chaine = substr($chaine,0,-4); // ON OTE LE DERNIER OR ET LES ESPACES
            //ON crée le select SQL récupérant tous les emprunts concernant les lots désignés et qui ont des dates d'emprunt ou de retour situées entre les dates d'emprunts souhaitées par l'utilisateur
            //Ou étant une sous-section d'un emprunt déja fait (un emprunt d'une durée plus petite situé au milieu d'un emprunt plus grand déja effectué)
            $select = "SELECT * from inscrits_lots WHERE (".$chaine.") AND ((date_emprunt BETWEEN ".$date_emprunt." AND ".$date_retour.") OR (date_retour BETWEEN ".$date_emprunt." AND ".$date_retour.") OR (date_emprunt <= ".$date_emprunt." AND date_retour >= ".$date_retour.") )";
            //Si le select retourne vide, les lots sont disponibles à cette date, on peut donc commencer l'emprunt
            //Sinon on renvoie comme erreur une chaine disant quels lots ne sont pas disponibles car étant déja empruntés de tant à tant
            if($result = $GLOBALS["bdd"]->query($select)){
                //Un ou plusieurs lots ont déja été empruntés, vu que le resultat du SELECT n'est pas nul
                //On récupère donc les lots ayant correspondus à ces critères afin d'informer l'utilisateur de quels lots ne sont pas disponibles
                $chaine_erreur = "";
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $chaine_erreur .= "Le lot ".$row["lots"]." n'est pas disponible dans la période que vous avez demandé, ayant déja été emprunté du ".$row["date_emprunt"]. " au ".$row["date_retour"].".\n";
                }
                $result->close();
                return $chaine_erreur;
            }
            else{ //Tout va bien, les dates d'emprunts correspondent à une période valide pour tous les lots
                //ON AJOUTE LA PERSONNE QUI EMPRUNTE A LA BDD
                $query = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits VALUES (?, ?, ?, ?, ?)");
                $query->bind_param('sssss', $nom,$prenom,$tel,$mail,$classe);
                $query->execute();
                $query->close();
                foreach($lots as $lot){
                    //ON AJOUTE L'EMPRUNT LUI-MEME A LA BDD
                    $query = $GLOBALS["bdd"]->prepare("INSERT INTO inscrits_lots VALUES (?, ?, ?, ?)");
                    $query->bind_param('ssss', $mail,$lot,$date_emprunt,$date_retour);
                    $query->execute();
                    $query->close();
                }
                return 1;
            }
        }
        else{
            $chaine_erreur = "Erreur. Vous avez entré des dates d'emprunts invalides (emprunté hier, emprunté durant plus de 3 mois,...)";
            return $chaine_erreur;
        }
    }

    //FONCTION MODIFICATION D'UN EMPRUNT(UTILISATEUR) VERSION TIMESTAMP
    //On récupère les infos de l'utilisateur qui change son emprunt, on effectue un backup des anciens emprunts, on les supprime et on tente d'effectuer un nouvel emprunt
    // avec les nouvelles dates. Si une erreur est rencontrée durant ce nouvel emprunt, on restaure les anciens emprunts et on remonte l'erreur rencontrée
    //Paramètres :
    // lots : chaine contenant les lots empruntés
    // date_emprunt : ancien timestamp de début de l'emprunt
    // mail : identifiant mail ISEN de l'utilisateur qui emprunte
    // new_date_emprunt : nouveau timestamp de début de l'emprunt
    // new_date_retour :  nouveau timestamp de fin de l'emprunt

    // Retour :
    //  chaine : 1 si aucune erreur durant toutes les opérations, contient l'erreur rencontrée sinon remontée depuis la fonction d'ajout (lot X déja emprunté durant la période, lot X inexistant, etc...)
    function modifEmpruntTimestamp($lots,$date_emprunt,$mail,$new_date_emprunt,$new_date_retour){
        //on récupére les infos personnelles de l'emprunteur
        $query = $GLOBALS["bdd"]->prepare("SELECT * FROM inscrits WHERE mail=?");
        $query->bind_param('s',$mail);
        $query->execute();
        $query->store_result();
        $query->bind_result($nom,$prenom,$tel,$mail,$classe);
        $query->close();
        //On prépare la sauvegarde des anciennes données
        $tab = array();
        $save = array();
        $i = 0;
        $query = $GLOBALS["bdd"]->prepare("SELECT * FROM inscrits_lots WHERE inscrit_mail=? AND date_emprunt=?");
        $query->bind_param('si',$mail,$date_emprunt);
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["inscrit_mail"],$tab["lots"],$tab["date_emprunt"],$tab["date_retour"]);
        while($query->fetch()){
            $save[$i]["inscrit_mail"] = $tab["inscrit_mail"];
            $save[$i]["lots"] = $tab["lots"];
            $save[$i]["date_emprunt"] = $tab["date_emprunt"];
            $save[$i]["date_retour"] = $tab["date_retour"];
            $i++;
        }
        $query->close();
        //Une fois les données sauvegardées, on supprime l'ancien emprunt
        supprEmpruntTimestamp($mail,$date_emprunt);
        //On tente de faire un nouvel emprunt avec les nouvelles dates
        $chaine = ajoutEmpruntTimestamp($nom,$prenom,$tel,$mail,$classe,$lots,$new_date_emprunt,$new_date_retour);
        if($chaine != 1){
            //Erreur rencontrée durant le nouvel emprunt
            //On restaure donc l'ancien état des emprunts (Attention, faire gaffe si emprunts concurrents, il peut y avoir un problème après la restauration)
            foreach($save as $restore){
                $insert = "INSERT INTO inscrits_lots VALUES ('".$restore['inscrit_mail']."','".$restore['lots']."','".$restore['date_emprunt']."','".$restore['date_retour']."')";
                $query = $GLOBALS["bdd"]->prepare($insert);
                $query->execute();
                $query->close();
            }
            //et on fait remonter l'erreur rencontrée
            return $chaine;
        }
        else{
            //Sinon, tout s'est bien passé, on renvoie 1
            return 1;
        }
    }

    //FONCTION SUPPRESSION D'UN EMPRUNT(UTILISATEUR) VERSION TIMESTAMP
    //Paramètres :
    // mail : identifiant mail ISEN de l'utilisateur qui emprunte
    // date : timestamp de début de l'emprunt visé

    // Retour :
    //  chaine : true si aucune erreur, contient l'erreur rencontrée
    function supprEmpruntTimestamp($mail,$date){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM inscrits_lots WHERE inscrit_mail=? AND date_emprunt=?");
        $query->bind_param('si',$mail,$date_emprunt);
        $query->execute();
        $query->close();
        return true;
    }



    //FONCTION DE RECUPERATION DES EMPRUNTS NON EFFECTUES ENCORE EN VERSION TIMESTAMP
    //Paramètres :
    // mail : identifiant mail ISEN de l'utilisateur qui emprunte

    // Retour :
    //  final : tableau double (type final[3]["inscrit_mail"]) contenant tout les emprunts effectués par quelqu'un qui n'ont pas encore commencés
    function recupEmpruntAjdTimestamp($mail){
        $tab = array();
        $final = array();
        $i=0;
        $date_ajd = time();
        $query = $GLOBALS["bdd"]->prepare("SELECT * FROM inscrits_lots WHERE inscrit_mail=? and date_emprunt>=?  GROUP BY date_emprunt");
        $query->bind_param('si',$mail,$date_ajd);
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["inscrit_mail"],$tab["lots"],$tab["date_emprunt"],$tab["date_retour"]);
        while($query->fetch()){
            $final[$i]["inscrit_mail"] = $tab["inscrit_mail"];
            $final[$i]["lots"] = $tab["lots"];
            $final[$i]["date_emprunt"] = $tab["date_emprunt"];
            $final[$i]["date_retour"] = $tab["date_retour"];
            $i++;
        }
        $query->close();
        return $final;
    }



    //FONCTION DE RECUPERATION DES EMPRUNTS EFFECTUES PAR UN INSCRIT à UNE DATE PRECISE VERSION TIMESTAMP
    //Paramètres :
    // mail : identifiant mail ISEN de l'utilisateur qui emprunte
    // date : timestamp de début de l'emprunt visé

    // Retour :
    //  tab : tableau contenant tous les lots empruntés aux dates précisées
    function recupEmpruntDateTimestamp($mail,$date_emprunt,$date_retour){
        $tab = array();
        $i=0;
        $query = $GLOBALS["bdd"]->prepare("SELECT lots FROM inscrits_lots WHERE inscrit_mail=? AND date_emprunt=? AND date_retour=?");
        $query->bind_param('sii',$mail,$date_emprunt,$date_emprunt);
        $query->execute();
        $query->store_result();
        $query->bind_result($temp);
        while($query->fetch()){
            $tab[$i] = $temp;
            $i++;
        }
        $query->close();
        return $tab;
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
        $desinscrits = '<legend id="tableau2">Désinscrire des personnes :</legend><form method="POST" action="projection.php#tableau2" class="form-register"><table class="table table-striped <!--table-bordered-->"><thead><tr><th class="col-md-6">Nom</th><th class="col-md-6">Prenom</th><th class="col-md-4">Classe</th><th>Désinscription</th></tr></thead>';
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
                $desinscrits .= '<tr><td class="inscrit_proj_list">'.$nom.'</td><td class="inscrit_proj_list">'.$prenom.'</td><td class="inscrit_proj_list">'.$classe.'</td><td><input id="desinscrits" name="desinscrits[]" value="'.$mail.'" type="checkbox"></td></tr>';
                $i++;
            }
            $query2->close();
        }
        $query->close();
        $table = $table."</table></body></html>";
        $desinscrits .= "</table><input type='hidden' value='".$projection."' name='projection' id='projection'/><input type='submit' class=\"button dark_grey\" value='Désinscrire ces personnes'></form>";
        $replace = array("'",'"'," ","/","\\",";");
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
        echo $desinscrits;
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
        $tab = array();
        $query = $GLOBALS["bdd"]->prepare('SELECT * from projections WHERE nom=?');
        $query->bind_param('s',$nom);
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["nom"],$tab["date_release"],$tab["date_projection"],$tab["description"],$tab["commentaires"],$tab["affiche"],$tab["active"],$tab["back_affiche"],$tab["langue"],$tab["prix"],$tab["bande_annonce"]);
        $query->fetch();
        $query->close();
        return $tab;
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

    //FONCTION PERMETTANT D'ACTIVER UNE PROJECTION COMME PROJECTION DE FIN D'ANNEE (PROVOQUE LE CHARGEMENT DES COURTS-METRAGES ASSOCIES QUAND LA PROJECTION EST ACTIVEE)
    function finAnneeProj($nom){
        $query = $GLOBALS["bdd"]->prepare("UPDATE projections SET fin_annee='0' WHERE fin_annee='1'");
        $query->execute();
        $query->close();
        $query = $GLOBALS["bdd"]->prepare("UPDATE projections SET fin_annee='1' WHERE nom=?");
        $query->bind_param('s',$nom);
        $query->execute();
        $query->close();
        return true;
    }

    //FONCTION PERMETTANT D'ACTIVER UNE PROJECTION COMME PROJECTION DE FIN D'ANNEE (PROVOQUE LE CHARGEMENT DES COURTS-METRAGES ASSOCIES QUAND LA PROJECTION EST ACTIVEE)
    function resetFinAnneeProj(){
        $query = $GLOBALS["bdd"]->prepare("UPDATE projections SET fin_annee='0' WHERE fin_annee='1'");
        $query->execute();
        $query->close();
        return true;
    }

    //FONCTION RECUPERANT LA PROJECTION ACTIVE ACTUELLE
    function recupProjActive(){
        $query ="SELECT * FROM projections WHERE active='1'";
        return $GLOBALS["bdd"]->query($query);
    }

    //FONCTION DE RECUPERATION DES PROMOS DISPONIBLES
    function recupPromo(){
        $query = "SELECT * from promotion ORDER BY id";
        return $GLOBALS["bdd"]->query($query);
    }


    //FONCTION DE RECUPERATION DES COURTS METRAGES POUR LA SOIREE DE FIN D'ANNEE
    function recupCourts(){
        $nom = "";
        $query = "SELECT nom from projections WHERE fin_annee = '1'";
        $result = $GLOBALS["bdd"]->query($query);
        if($result->num_rows){
            while ($row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $nom = $row["nom"];
            }
        $result->close();
        }
        $query = "SELECT * from courts WHERE projection_liee='".$nom."'";
        return $GLOBALS["bdd"]->query($query);
    }

    function recupCourtsProj($nom_projection){
        $query = $GLOBALS["bdd"]->prepare("SELECT * FROM `courts` WHERE projection_liee=?");
        $query->bind_param('s',$projection_liee);
        $query->execute();
        return $query;
    }

    function activateCourts($nom){
        $query = "UPDATE projections SET fin_annee='0' WHERE fin_annee='1'";
        $result = $GLOBALS["bdd"]->query($query);
        $query = $GLOBALS["bdd"]->prepare("UPDATE projections SET fin_annee='1' WHERE nom='".$nom."'");
        $query->execute();
        $query->close();
    }

    function addCourt($titre,$description,$projection_liee,$video,$affiche,$annee){
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO `courts`(`titre`, `description`, `projection_liee`, `video`, `affiche`, `annee`) VALUES (?,?,?,?,?,?)");
        $query->bind_param('ssssss',$titre,$description,$projection_liee,$video,$affiche,$annee);
        $query->execute();
        $query->close();
        return true;
    }

    function supprCourt($titre){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM `courts` WHERE titre=?");
        $query->bind_param('s',$titre);
        $query->execute();
        $query->close();
        return true;
    }

    function supprCourtProj($nomProjection){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM `courts` WHERE projection_liee=?");
        $query->bind_param('s',$nomProjection);
        $query->execute();
        $query->close();
        return true;
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
        $replace = array("'",'"'," ","/","\\",";");
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
        $replace = array("'",'"'," ","/","\\",";");
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
        $replace = array("'",'"'," ","/","\\",";");
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
        $tab = array();
        $query = $GLOBALS["bdd"]->prepare("SELECT * FROM admin WHERE identifiant=?");
        $query->bind_param('s',$identifiant);
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["identifiant"],$tab["mdp"],$tab["mail"],$tab["responsable_emprunt"]);
        $query->fetch();
        $query->close();
        return $tab;
    }

    //FONCTION RECUPERANT TOUT LES ADMINS RESPONSABLES DES EMPRUNTS
    function recupAdminEmprunts(){
        $tab = array();
        $final = array();
        $i = 0;
        $query = $GLOBALS["bdd"]->prepare("SELECT identifiant, responsable_emprunt FROM admin");
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["identifiant"],$tab["responsable_emprunt"]);
        while($query->fetch()){
            $final[$i] = array($tab["identifiant"],$tab["responsable_emprunt"]);
            $i++;
        }
        $query->close();
        return $final;
    }

    //FONCTION RECUPERANT TOUT LES ADMINS RESPONSABLES DES EMPRUNTS
    function recupAdminSys(){
        $tab = array();
        $final = array();
        $i = 0;
        $query = $GLOBALS["bdd"]->prepare("SELECT identifiant, responsable_sys FROM admin");
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["identifiant"],$tab["responsable_sys"]);
        while($query->fetch()){
            $final[$i] = array($tab["identifiant"],$tab["responsable_sys"]);
            $i++;
        }
        $query->close();
        return $final;
    }

    //FONCTION RECUPERANT TOUT LES ADMINS RESPONSABLES DES EMPRUNTS
    function recupAdminCine(){
        $tab = array();
        $final = array();
        $i = 0;
        $query = $GLOBALS["bdd"]->prepare("SELECT identifiant, responsable_cine FROM admin");
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["identifiant"],$tab["responsable_cine"]);
        while($query->fetch()){
            $final[$i] = array($tab["identifiant"],$tab["responsable_cine"]);
            $i++;
        }
        $query->close();
        return $final;
    }

    //FONCTION RECUPERANT TOUT LES ADMINS RESPONSABLES DES EMPRUNTS
    function recupAdminSorties(){
        $tab = array();
        $final = array();
        $i = 0;
        $query = $GLOBALS["bdd"]->prepare("SELECT identifiant, responsable_sorties_semaine FROM admin");
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["identifiant"],$tab["responsable_sorties"]);
        while($query->fetch()){
            $final[$i] = array($tab["identifiant"],$tab["responsable_sorties"]);
            $i++;
        }
        $query->close();
        return $final;
    }


    //FONCTION D'AJOUT D'UN ADMIN DANS LA BASE
    function addAdmin($identifiant,$mdp,$mail,$respons_emprunts,$respons_sys,$respons_cine,$respons_sorties){
        $mdp = password_hash($mdp,PASSWORD_DEFAULT);
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO admin VALUES(?,?,?,?,?,?,?)");
        $query->bind_param('sssiiii',$identifiant,$mdp,$mail,$respons_emprunts,$respons_cine,$respons_sys,$respons_sorties);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION DE SUPPRESSION D'UN ADMIN DANS LA BASE
    function supprAdmin($identifiant){
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM admin WHERE identifiant=?");
        $query->bind_param('s',$identifiant);
        $query->execute();
        $query->close();
        return true;
    }


    //FONCTION DE CHANGEMENT DE MOT DE PASSE POUR L'ADMINISTRATEUR COURANT
    function modifMDP($identifiant, $mdp, $oldMDP){
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
    function changeAdminEmprunts($identifiant, $respons){
        $query = $GLOBALS["bdd"]->prepare("UPDATE admin SET responsable_emprunt=? WHERE identifiant=?");
        $query->bind_param('is',$respons,$identifiant);
        $query->execute();
        $query->close();
        return true;
    }
    function changeAdminSys($identifiant, $respons){
        $query = $GLOBALS["bdd"]->prepare("UPDATE admin SET responsable_sys=? WHERE identifiant=?");
        $query->bind_param('is',$respons,$identifiant);
        $query->execute();
        $query->close();
        return true;
    }
    function changeAdminCine($identifiant, $respons){
        $query = $GLOBALS["bdd"]->prepare("UPDATE admin SET responsable_cine=? WHERE identifiant=?");
        $query->bind_param('is',$respons,$identifiant);
        $query->execute();
        $query->close();
        return true;
    }
    function changeAdminSorties($identifiant, $respons){
        $query = $GLOBALS["bdd"]->prepare("UPDATE admin SET responsable_sorties_semaine=? WHERE identifiant=?");
        $query->bind_param('is',$respons,$identifiant);
        $query->execute();
        $query->close();
        return true;
    }


//################################################################################################################################################################

    //FONCTIONS GESTION DES LOTS

    //Reset tout les emprunts et toutes les disponibilités, à utiliser avec beaucoup de précaution pour le moment
    function resetDispo(){
        $query = "SELECT * from lots ORDER BY id";
        $result = $GLOBALS["bdd"]->query($query);
        while ($row = $result->fetch_array(MYSQLI_ASSOC))
        {
            $query = $GLOBALS["bdd"]->query("UPDATE dispo SET ".$row["id"]."=1 WHERE 1");
        }
        $query = $GLOBALS["bdd"]->query("DELETE FROM inscrits_lots WHERE 1");
        $result = $GLOBALS["bdd"]->query($query);
    }


    //FONCTION D'AJOUT D'UN LOT
    function addLot($identifiant, $composition,$image,$caution){
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
        $query = $GLOBALS["bdd"]->prepare("SELECT image FROM lots WHERE id=?");
        $query->bind_param('s',$identifiant);
        $query->execute();
        $query->store_result();
        $query->bind_result($imagetodelete);
        $query->fetch();
        $query->close();
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
        if(empty($image)){
            $query = $GLOBALS["bdd"]->prepare("UPDATE `lots` SET `id`=?,`composition`=?,`caution`=? WHERE `id`=?");
            $query->bind_param('ssis',$identifiant,$composition,$caution,$ancien_identifiant);
        }else{
            $query = $GLOBALS["bdd"]->prepare("SELECT image from lots WHERE id=?");
            $query->bind_param('s',$ancien_identifiant);
            $query->execute();
            $query->store_result();
            $query->bind_result($imagetodelete);
            $query->fetch();
            $query->close();
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
        $tab = array();
        $query = $GLOBALS["bdd"]->prepare("SELECT * FROM inscrits_lots WHERE date_emprunt>=? AND date_retour<? ORDER BY date_emprunt");
        $query->bind_param('ss',$date_start,$date_end);
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["inscrit_mail"],$tab["lots"],$tab["date_emprunt"],$tab["date_retour"]);
        while($query->fetch()){
            $lot = $tab["lots"];
            $id = $tab["inscrit_mail"];
            $date_emprunt = $tab["date_emprunt"];
            $date_emprunt_formatée = date_create_from_format("Y-m-d H:m:s", $date_emprunt);
            $date_emprunt_formatée = date_format($date_emprunt_formatée,'U');
            $date_retour = $tab["date_retour"];
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
        $query->close();
        echo json_encode(array('success' => 1, 'result' => $out));
    }

    //FONCTION DE RECUPERATION DE TOUT LES LOTS
    function recupLot(){
        $query = "SELECT * from lots ORDER BY id";
        return $GLOBALS["bdd"]->query($query);
    }

    function recupUniqueLot($id){
        $tab = array();
        $query = $GLOBALS["bdd"]->prepare("SELECT * from lots WHERE id=?");
        $query->bind_param('s',$id);
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["id"],$tab["compo"],$tab["image"],$tab["caution"]);
        $query->fetch();
        $query->close();
        return $tab;
    }



    function dejaInscrit($id){
        $tab = array();
        $query = $GLOBALS["bdd"]->prepare("SELECT * FROM inscrits where mail=?");
        $query->bind_param('s',$id);
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["nom"],$tab["prenom"],$tab["tel"],$tab["mail"],$tab["classe"]);
        $query->fetch();
        $query->close();
        return $tab;
    }

    function compress($source, $destination, $quality) {

		$info = getimagesize($source);

        //Si l'image est une JPG, on utilise imagejpg() qui assure une bonne compression
		if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg'){
			$image = imagecreatefromjpeg($source);
            imagejpeg($image, $destination, $quality);
        }
        //Sinon si l'image est une PNG, on utilise la fonction pngquant qui permet une bonne compression
		else if ($info['mime'] == 'image/png'){
            //$image = imagecreatefrompng($source);
            //imagepng($image, $destination, 0);
            $min_quality = 60;
            $compressed_png_content = shell_exec("pngquant --quality=$min_quality-$quality - < ".escapeshellarg($source));
            file_put_contents($destination,$compressed_png_content);
        }
		return $destination;
	}

//################################################################################################################################################################

    //FONCTIONS GESTION DES SORTIES DE LA SEMAINE

    //Format de la table des sorties de la semaine en BDD
        //Semaine : Chaine sous-forme XX-AAAA où XX représente le numéro de la semaine dans l'année
        //description : le synopsys du film . Format String
        //Affiche : l'affiche du film. Format String contenant l'URL du fichier image

    function ajoutSortie($description,$affiche,$active){
        $semaine = date("W-Y");
        $timestamp = time();
        $query = $GLOBALS["bdd"]->prepare("INSERT INTO sorties_semaine VALUES(?,?,?,?,?)");
        $query->bind_param('sssii',$semaine,$description,$affiche,$active,$timestamp);
        $query->execute();
        $query->close();
        return true;
    }

    function supprSortie($semaine){
        $query = $GLOBALS["bdd"]->prepare("SELECT  affiche FROM  sorties_semaine WHERE  semaine=?");
        $query->bind_param("s",$semaine);
        $query->execute();
        $query->store_result();
        $query->bind_result($imagetodelete);
        $query->fetch();
        unlink($imagetodelete);
        $query->close();
        $query = $GLOBALS["bdd"]->prepare("DELETE FROM sorties_semaine where semaine=?");
        $query->bind_param('s',$semaine);
        $query->execute();
        $query->close();
        return true;
    }

    function modifSortie($semaine,$nouvelle_semaine,$nouvelle_description,$nouvelle_affiche,$active,$timestamp){
        $timestamp = time();
        $query = $GLOBALS["bdd"]->prepare("UPDATE sorties_semaine SET semaine=?, description=?,affiche=?,active=?, timestamp=? where semaine=?");
        $query->bind_param('sss',$nouvelle_semaine, $nouvelle_description,$nouvelle_affiche,$active,$timestamp,$semaine);
        $query->execute();
        $query->close();
        return true;
    }

    function recupSortieSemaine(){
        $tab = array();
        $query = $GLOBALS["bdd"]->prepare("SELECT * FROM sorties_semaine WHERE active=1");
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["semaine"],$tab["description"],$tab["affiche"],$tab["active"],$tab["timestamp_ajout"]);
        $query->fetch();
        $query->close();
        return $tab;
    }

    function recupToutesSortiesSemaine(){
        $tab = array();
        $final = array();
        $i = 0;
        $query = $GLOBALS["bdd"]->prepare("SELECT * FROM sorties_semaine");
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["semaine"],$tab["description"],$tab["affiche"],$tab["active"],$tab["timestamp_ajout"]);
        while($query->fetch()){
            $final[$i]["semaine"] = $tab["semaine"];
            $final[$i]["description"] = $tab["description"];
            $final[$i]["affiche"] = $tab["affiche"];
            $final[$i]["active"] = $tab["active"];
            $final[$i]["timestamp_ajout"] = $tab["timestamp_ajout"];
            $i++;
        }
        $query->close();
        return $final;
    }

    function recupSortieSemainePrecise($semaine){
        $tab = array();
        $query = $GLOBALS["bdd"]->prepare("SELECT * FROM sorties_semaine WHERE semaine=?");
        $query->bind_param('s',$semaine);
        $query->execute();
        $query->store_result();
        $query->bind_result($tab["semaine"],$tab["description"],$tab["affiche"],$tab["active"],$tab["timestamp_ajout"]);
        $query->fetch();
        $query->close();
        return $tab;
    }

?>
