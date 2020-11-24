<?php

require_once '../vendor/jasig/phpcas/CAS.php';

phpCAS::setDebug();
phpCAS::setVerbose(true);
phpCAS::client(CAS_VERSION_3_0, 'cas-auth.rpi.edu', 443, '/cas');
phpCAS::setNoCasServerValidation();

if (isset($_REQUEST['login'])) {   
   phpCAS::forceAuthentication();
   header("Location: /views/dashboard.php");
   exit();
}

if (isset($_REQUEST['logout'])) {
   phpCAS::logout(array('service'=>'project.websys/views', 'url'=>'project.websys/views'));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Home - amalgamation</title>        
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <!-- Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../resources/images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../resources/images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../resources/images/favicons/favicon-16x16.png">
    <link rel="manifest" href="../resources/images/favicons/site.webmanifest">
    <link rel="mask-icon" href="../resources/images/favicons/safari-pinned-tab.svg" color="#4c3549">
    <link rel="shortcut icon" href="../resources/images/favicons/favicon.ico">
    <meta name="msapplication-TileColor" content="#9f00a7">
    <meta name="msapplication-config" content="../resources/images/favicons/browserconfig.xml">
    <meta name="theme-color" content="#4c3549">

</head>

<body><nav class="navbar navbar-dark navbar-expand-lg fixed-top bg-dark navbar-custom">
    <div class="container"><a class="navbar-brand text-lowercase" href="#"> <img src=../resources/images/logo1.png alt="amalgamation logo" width="300" height="75"></a><button data-toggle="collapse" data-target="#navbarResponsive" class="navbar-toggler"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="nav navbar-nav ml-auto">
               <?php
                  if (!phpCAS::checkAuthentication()) {
                     echo '<li class="nav-item"><a class="nav-link" href="?login=">Log In</a></li>';
                  } else {
                     echo '<li class="nav-item"><a class="nav-link" href="?logout=">Logout</a></li>';
                  }
                ?>
            </ul>
        </div>
    </div>
</nav>
    <header class="masthead text-center text-white">
        <div class="masthead-content">
            <div class="container">
                <h1 class="d-inline-block masthead-heading mb-0">amalgamation.</h1>
                <h2 class="text-lowercase text-white masthead-subheading mb-0" style="width: 1096px;">a collaborative doodling application</h2><a class="btn btn-dark btn-xl rounded-pill mt-5" role="button" href="#">Start drawing</a></div>
        </div>
    </header>
    <section class="text-center">
        <div class="container">
            <h2 class="text-white display-4" style="width: 1136px;margin: 0px 0px 8px;height: 216px;"><br>A collaborative doodling environment for both amateur and professional photo editing<br><br></h2>
            <p class="text-white">amalgamation. is a free collaborative doodling application for anyone! Our team's goal is to facilitate visual collaboration for larger teams of amateurs and professionals alike. Users will be able to create projects, and add other users to
                the projects who will be able to edit the pictures. If you've ever wanted to draw with a friend, now you can with this application! Collaborate with others in real time regardless of location.&nbsp;</p>
        </div>
    </section>
    <section></section>
    <section>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-1">
                    <div class="p-5">
                        <h2 class="text-white display-4">Under development</h2>
                        <p class="text-white">Team Tux is working hard to add more features to the application as soon as possible. Since amalgamation. is still in early stages of development, it is highly encouraged to save the drawings locally if you want to keep them.<br><br></p>
                    </div>
                </div>
                <div class="col">
                    <div class="p-5">
                        <h2 class="text-white display-4">Who is it for?</h2>
                        <ul>
                            <li class="text-white">Professional artists and creators</li>
                            <li class="text-white">Freelancer artists</li>
                            <li class="text-white">Animation studios</li>
                            <li class="text-white">Schools and universities</li>
                            <li class="text-white">General public</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="py-5 bg-black">
        <div class="container">
            <p class="text-center text-white m-0 small">Team Tux. amalgamation. Fall 2020</p>
        </div>
    </footer>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>