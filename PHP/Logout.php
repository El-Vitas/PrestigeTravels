<?php 
    session_start();
    session_unset();
    header('Location: http://localhost/Lab2/PHP/index.php');
?>