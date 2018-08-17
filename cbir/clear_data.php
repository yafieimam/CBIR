<?php
include('functions.php');

// connect to database
$connection = mysqli_connect($db_server["host"], $db_server["username"], $db_server["password"], $db_server["database"]);

$query = "DELETE FROM images";
$result = mysqli_query($connection, $query);
if (!$result) die('Invalid query: ' . mysqli_error());

$query = "DELETE FROM coeffs_y";
$result = mysqli_query($connection, $query);
if (!$result) die('Invalid query: ' . mysqli_error());

$query = "DELETE FROM coeffs_i";
$result = mysqli_query($connection, $query);
if (!$result) die('Invalid query: ' . mysqli_error());

$query = "DELETE FROM coeffs_q";
$result = mysqli_query($connection, $query);
if (!$result) die('Invalid query: ' . mysqli_error());

mysqli_close($connection);

header( 'Location: index.php' ) ;

?>

