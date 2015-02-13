<?php



        $page = basename($_SERVER['PHP_SELF']);

echo'
<!-- Header -->
            <div id="header-wrapper">
                <div class="container">

                    <!-- Header -->
                        <header id="header">
                            <div class="inner">

                                                                <!-- Logo -->
                                    <h1><a href="#" id="logo" style="display:none;@media(max-width:899){ display:inner
};">Moviezen</a></h1>

                                <!-- Nav -->
                                    <nav id="nav">
                                        <ul>
                                            <li><a href="../index.php" class="nav-btn">Présentation du club</a></li>
                                            <li';
            if($page=="cine.php")echo(" class=current_page_item");
    echo'><a href="cine.php" class="nav-btn">Coté Ciné de l\'ISEN</a></li>
                                            <li';
            if($page=="emprunt.php")echo(" class=current_page_item");
    echo'><a href="emprunt.php" class="nav-btn">Emprunt de matériel</a></li>
                                            <li';
            if($page=="admin.php")echo(" class=current_page_item");
    echo'><a href="admin.php" class="nav-btn">Espace Administrateur</a></li>
                                            <li';
            if($page=="calendrier.php")echo(" class=current_page_item");
    echo'><a href="calendrier.php" class="nav-btn">Calendrier des emprunts</a></li>
                                            <li';
if(!empty($_SESSION["authentifie"])){
    if($page=="projection.php")echo(" class=current_page_item");
    echo'><a href="projection.php" class="nav-btn">Espace liste de projection</a></li>';
}
            echo ' </ul>
                                    </nav>';
                            if(!empty($_SESSION["authentifie"])){
    echo'<a  title="Deconnexion button" type="button" class="btn" id="decobtn" href="?deco=1"><span class="glyphicon glyphicon-off" aria-hidden="true"></span></a>';
}
                          echo '  </div>
                        </header>



                </div>
            </div>';
?>
