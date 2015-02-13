<?php
    session_start();
    include_once("../includes/fonctions.php");
    include_once("../includes/function_global.php");
    connect();

    if(!empty($_GET['from']) && !empty($_GET['to'])){
        $start = date('Y-m-d H:m:s', ($_GET['from'] / 1000));
        $end = date('Y-m-d H:m:s', ($_GET['to'] / 1000));
        renduLotCalendar($start,$end);
    }

?>
