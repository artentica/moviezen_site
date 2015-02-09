<?php
echo'
<!-- Header -->
            <div id="header-wrapper">
                <div class="">

                    <!-- Header -->
                        <header id="header">
                            <div class="inner">

                                <!-- Logo -->
                                    <h1><a href="#" id="logo">Moviezen</a></h1>

                                <!-- Nav -->
                                    <nav id="nav">
                                        <ul>
                                            <li class="current_page_item"><a href="index.php" class="nav-btn">Présentation du club</a></li>
                                            <li><a href="views/cine.php" class="nav-btn">Coté Ciné de l\'ISEN</a></li>
                                            <li><a href="views/emprunt.php" class="nav-btn">Emprunt de matériel</a></li>
                                            <li><a href="views/admin.php" class="nav-btn">Espace Administrateur</a></li>
                                            <li><a href="views/calendrier.php" class="nav-btn">Calendrier des emprunts</a></li>
                                            <li><a href="views/projection.php" class="nav-btn">Espace liste de projection</a></li>

                                        </ul>
                                    </nav>';
                            if(!empty($_SESSION["authentifie"])){
    echo'<a  title="Deconnexion button" type="button" class="btn" id="decobtn" href="?deco=1"><span class="glyphicon glyphicon-off" aria-hidden="true"></span></a>';
}
                          echo '  </div>
                        </header>



                </div>
            </div>';
?>
