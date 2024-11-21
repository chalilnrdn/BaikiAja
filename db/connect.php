<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database = "pengaduan";

$conn = mysqli_connect($hostname, $username, $password, $database);

if (mysqli_connect_errno()){
    echo "gagal connect ke database" . mysqli_connect_errno();
} else {
    echo "";
}

?>