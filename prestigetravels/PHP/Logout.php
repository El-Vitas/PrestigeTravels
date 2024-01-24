<?php 
    session_start();
    session_unset();
    header('Location: /PHP/index.php');
?>