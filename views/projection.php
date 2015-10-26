<?php
    session_start();
    include_once("../includes/fonctions.php");
    include_once("../includes/function_global.php");
    connect();

    //Protection contre faille XSS et attaques HTML-JS
    //Pour tableau POST (et GET au cas où)
    //On parcourt la totalité du tableau POST et GET et pour chaque variable, on enlève les éléments "génants"
    foreach( $_POST as $cle=>$value )
        {
            if(is_array($_POST[$cle])) {
                foreach($_POST[$cle] as $cle2 =>$value2){
                    $_POST[$cle2] = strip_tags(htmlentities($value2, ENT_QUOTES, 'UTF-8'));
                }
            }
            else{
                $_POST[$cle] = strip_tags(htmlentities($value, ENT_QUOTES, 'UTF-8'));
            }

        }

    foreach( $_GET as $cle=>$value )
        {
            if(is_array($_GET[$cle])) {
                foreach($_GET[$cle] as $cle2 =>$value2){
                    $_GET[$cle2] = strip_tags(htmlentities($value2, ENT_QUOTES, 'UTF-8'));
                }
            }
            else{
                $_GET[$cle] = strip_tags(htmlentities($value, ENT_QUOTES, 'UTF-8'));
            }

        }

    //Si l'utilisateur n'est pas authentifié, on redirige directement vers l'index du site
    if(!$_SESSION["authentifie"]){
         header('Location: ../index.php');
    }

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
	<title>Espace Liste de projection</title>

	<!-- Set Viewport Options -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
		<meta name="apple-mobile-web-app-capable" content="yes" />

	  <link rel="stylesheet" type="text/css" href="../CSS/index.css">
    <link rel="stylesheet" type="text/css" href="../CSS/menu.css">
	  <link rel="stylesheet" type="text/css" href="../CSS/bootstrap.css">
	 <?php
        include '../includes/include_on_all_page.php';
    ?>
    <script src="../js/bootstrap.js"></script>

</head>
<body>
   <div id="banniere"  style="background-image: url('../CSS/new_design/images/header.jpg');
background-size: cover;">
        <h1>
            Moviezen
        </h1>
    </div>
    <header>
        <?php
       include '../includes/panel-global.php';?>
    </header>
    <div class="panel panel-default">
		<div class="panel-body">

        </div>
	</div>


</body>
</html>
