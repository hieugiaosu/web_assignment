<?php
  session_start();
  if (!isset($_SESSION['role']) || $_SESSION['role']!="ADMIN") {
      header("Location: /btl/page_not_found.html");
      exit;
  }
  $id = "";
  if (isset($_GET["id"])){
    $id = $_GET["id"];
  } else {
    header("Location: page_bot_found.html");
    exit;
  }
  $host = "localhost"; 
  $user = "root";
  $password = ""; 
  $database = "manager";
  $connection = mysqli_connect($host, $user, $password, $database);
  mysqli_query($connection,"DELETE FROM product_image WHERE product_id = $id");
  mysqli_query($connection,"DELETE FROM product_info WHERE `product_info`.`product_id` = $id");
  mysqli_query($connection,"DELETE FROM feedback WHERE product_id = $id");
  
  mysqli_close($connection);
  header("Location: products_list.html");
  exit;
?>