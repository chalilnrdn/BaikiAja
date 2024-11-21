<?php

session_start();
include '../db/connect.php';
include '../function/auth.php';

if(isset($_POST['login'])){
    $username = $_POST ['username'];
    $password = $_POST ['password'];
    $login = login($username, $password, $conn);

    //mengalihkan ke halaman sesuai role
    if(isset($login['role'])){
        //menyimpan data user kedala session
        $_SESSION['username'] = $username;
        $_SESSION['role']     = $login ['role'];
        $_SESSION['user_id']  = $login ['user_id'];

        //arahkan halaman sesuai role
        if($login['role']== 2){
            header('location: ../petugas/dashboard-petugas.php');
        }elseif($login['role']==1){
            header('location: ../masyarakat/dashboard-masyarakat.php');
        }
    }else{
        echo "login gagal, cek password dan username anda";
    }

}


?>







<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login BaikiAja</title>
    <link rel="shortcut icon" href="../asset/flag-line.png" type="image/x-icon">
    <link rel="stylesheet" href="../bootstrap/css/sb-admin-2.min.css">
</head>

<body class="bg-gradient-danger">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                <dotlottie-player src="https://lottie.host/d684d4ea-da75-4254-bc04-3a0765a2f273/xZPfKQbELl.lottie" background="transparent" speed="1" style="width: 500px; height: 500px" loop autoplay></dotlottie-player>
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Selamat Datang</h1>
                                        <h3 class="h6 text-gray-900" >BaikiAja Hadir untuk menampung semua keluhanmu</h3>
                                    </div>

                                    <form class="user" method="POST">
                                        <div class="form-group">
                                            <label class="label-control" for="username">Username</label>
                                            <input type="text" name="username" class="form-control form-control-user" aria-describedby="emailHelp" placeholder="Enter Username">
                                        </div>
                                        <div class="form-group">
                                            <label class="label-control" for="password">Password</label>
                                            <input type="password" name="password" class="form-control form-control-user" placeholder="Enter Password">
                                        </div>
                                        <button type="submit" name="login" class="btn btn-primary btn-user btn-block">Login</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>




        </div>




    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script> 
</body>

</html>