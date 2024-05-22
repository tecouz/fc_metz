<?php require_once $_SERVER["DOCUMENT_ROOT"] . "/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/include/protect.php";

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $sql = "DELETE FROM evenement WHERE evenement_id= :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([":id" => $_GET["id"]]);
}
header("Location: index.php?page=2");