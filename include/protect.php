<?php
session_start();
if (!isset($_SESSION['user_connected']) || $_SESSION['user_connected'] != "ok") {
    header("Location: /Login/index.php");
    exit();
}