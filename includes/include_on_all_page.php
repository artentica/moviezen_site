<?php
   
   
 $url = $_SERVER['REQUEST_URI'];
 $url = strstr ($url,"admin");
    echo '
    	<script src="../js/jquery-2.1.3.min.js"></script>

    	 <!--TEST!!!!!!!!!!!!!!!!!!!!!!-->';
		if(empty($url)){ echo '<script src="../js/new_design/skel.min.js"></script>
		<script src="../js/new_design/skel-layers.min.js"></script>
		<script src="../js/new_design/init.js"></script>';}
		

        echo '<noscript>
			<link rel="stylesheet" href="../CSS/new_design/skel.css" />
			<link rel="stylesheet" href="../CSS/new_design/style.css" />
			<link rel="stylesheet" href="../CSS/new_design/style-desktop.css" />
		</noscript>
<!--FIN TEST-->
        <link rel="stylesheet" type="text/css" href="../CSS/global.css">
        <link rel="stylesheet" type="text/css" href="../CSS/menu.css">
    ';
?>
