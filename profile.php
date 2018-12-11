<?php
session_start();
require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

require_once('credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

if (!$auth->isLogged()) {
	header("Location: index.php");
	die('Forbidden');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Sylvester</title>
    <!-- Boostrap CSS & JS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="http://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.13/js/mdb.min.js"></script>
    <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.50.0/mapbox-gl.js'></script>
    <script type="text/javascript" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
    <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.50.0/mapbox-gl.css' rel='stylesheet' />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.13/css/mdb.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="home-style.css">
    <script type="text/javascript" src="cities.js"></script>
</head>

<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="#">

                    </a>
                </li>
                <li>
                    <a href="home.php">Dashboard</a>
                </li>
                <li>
                    <a href="history.php">Query history</a>
                </li>
                <li>
                    <a href="profile.php">Profile</a>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <header>
                <!-- Fixed navbar -->
                <nav class="navbar navbar-expand fixed-top">
                    <a class="navbar-brand" href="home.php">sylvester</a>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="#menu-toggle" id="menu-toggle"><i class="fas fa-bars"></i></a>
                            </li>
                        </ul>
                    </div>
                    <img class="rounded-circle" style="width:35px; margin: 0 10px;" src="https://randomuser.me/api/portraits/men/<?php echo rand(0,10);?>.jpg" alt="">
                  <li class="nav-item dropdown" style="list-style: none;">
                          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo $auth->getCurrentUser()['email']; ?>
                          </a>
                          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="profile.php">Profile</a>
                            <a class="dropdown-item" href="logout.php">Log out</a>
                          </div>
                        </li>
                </nav>
            </header>
            <!-- Begin page content -->
            <main role="main" class="container-fluid">
                <div class="row" style="margin-top: 60px">
                    <div class="col-sm-1 col-md-2"></div>
                    <div class="col-sm-10 col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <img src="assets/MjEURlF.jpg" class="w-100 header-picture" data-type="header">
                                <p class="text-muted click-to-edit invisible">Click to edit <span class="photo-selection"></span> picture</p>
                                <img class="rounded-circle profile-picture" data-type="profile" src="http://lorempixel.com/600/600/people/" alt="">
                                <br>
                                <div class="row">
                                    <div class="col-sm-12 text-center">
                                        <h5 class="card-title"><strong>Welcome, <?php echo $auth->getCurrentUser()['email']; ?></strong></h5>
                                        <p class="text-muted">User since: <?php echo date("F d, Y", strtotime($auth->getCurrentUser()['dt'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title profile-card-text"><strong>Edit profile</strong></h5>
                                <div class="row">
                                    <div class="col-md-12 input-effect">
                                        <div class="md-form">
                                            <input type="text" autocomplete="off" id="name" name="name" class="form-control">
                                            <label for="name" class="float-up">Name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 input-effect">
                                        <div class="md-form">
                                            <input type="email" autocomplete="off" id="email" name="email" class="form-control">
                                            <label for="email" class="float-up">Email</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 input-effect">
                                        <div class="md-form">
                                            <input type="password" autocomplete="off" id="password" name="password" class="form-control">
                                            <label for="password" class="float-up">Password</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 input-effect">
                                        <div class="md-form">
                                            <input type="password" autocomplete="off" id="password-2" name="password-2" class="form-control">
                                            <label for="password-2" class="float-up">Repeat password</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button type="button" class="btn btn-hollow" data-toggle="button" aria-pressed="false" autocomplete="off">Change password</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 input-effect">
                                        <div class="md-form">
                                            <input type="tel" autocomplete="off" id="phone" name="phone" class="form-control">
                                            <label for="phone" class="float-up">Phone number</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button type="button" class="btn btn-hollow" data-toggle="button" aria-pressed="false" autocomplete="off">Enable 2-factor authentication</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title profile-card-text"><strong>My stats</strong></h5>
                                <?php

                                // Get most frequent location
                                $statement = $dbh->prepare("SELECT COUNT(*) AS count FROM queries WHERE uid = :uid");
                                $statement->execute([
                                    'uid' => $auth->getCurrentUser()['uid']
                                ]);
                                // Print errors, if they exist
                                if($statement->errorInfo()[0] != "00000") {
                                    print_r($statement->errorInfo());
                                    die();
                                } else {
                                    $number_of_queries = $statement->fetch(PDO::FETCH_ASSOC);
                                }

                                if($number_of_queries['count'] > 0) {
                                    // Only continue if the user has queries

                                    // Get most frequent keyword
                                    $statement = $dbh->prepare("SELECT keyword, COUNT(*) AS magnitude FROM queries WHERE uid = :uid GROUP BY keyword ORDER BY magnitude DESC LIMIT 1");
                                    $statement->execute([
                                        'uid' => $auth->getCurrentUser()['uid']
                                    ]);

                                    // Print errors, if they exist
                                    if($statement->errorInfo()[0] != "00000") {
                                        print_r($statement->errorInfo());
                                        die();
                                    } else {
                                        $most_frequent_keyword = $statement->fetch(PDO::FETCH_ASSOC);
                                    }

                                    if(empty($most_frequent_keyword)) {
                                        // If there haven't been any searches yet...
                                    }

                                    // Get most frequent location
                                    $statement = $dbh->prepare("SELECT location, COUNT(*) AS magnitude FROM queries WHERE uid = :uid GROUP BY location ORDER BY magnitude DESC LIMIT 1");
                                    $statement->execute([
                                        'uid' => $auth->getCurrentUser()['uid']
                                    ]);
                                    // Print errors, if they exist
                                    if($statement->errorInfo()[0] != "00000") {
                                        print_r($statement->errorInfo());
                                        die();
                                    } else {
                                        $most_frequent_location = $statement->fetch(PDO::FETCH_ASSOC);
                                    }

                                    if(empty($most_frequent_location)) {
                                        // If there haven't been any searches yet...
                                    }

                                    ?>
                                    <p class="card-text">
                                        Number of queries: <strong><?php echo $number_of_queries['count']; ?></strong>
                                    </p>
                                    <p class="card-text">
                                        Most frequent keyword: <strong><?php echo $most_frequent_keyword['keyword']; ?></strong>
                                    </p>
                                    <p class="card-text">
                                        Most frequent location: <strong><?php echo $most_frequent_location['location']; ?></strong>
                                    </p>
                                    <p class="card-text">
                                        Most frequent sentiment: <?php echo ['<i class="fas fa-arrow-up"></i> <span class="text-success">positive</span>', '<i class="fas fa-arrow-down"></i> <span class="text-danger">negative</span>', '<i class="fas fa-arrow-right"></i> <span class="text-dark">neutral</span>'][rand(0,2)]; ?>
                                    </p>
                                    <p class="card-text">
                                        Number of Tweets analyzed: <strong><?php echo number_format($number_of_queries['count']*rand(52342,98457)); ?></strong>
                                    </p>
                                </div>
                                <div class="card-footer recent-searches-card-footer text-right <?php if($number_of_queries['count'] == 0) { echo "d-none"; } ?>">
                                  <a href="history.php"><small class="text-muted">See more <i class="fas fa-arrow-circle-right"></i></small></a>
                                </div>
                            <?php 
                                } // End if statement, making sure user has queries
                                else {
                                    ?>
                                    <p class="card-text"><a href="home.php">Go make some queries</a> and we'll keep track of your stats here.</p>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <!-- /#page-content-wrapper -->
        <!-- /#wrapper -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                  <div class="col text-right">Team Sylvester</div>
                </div>
            </div>
        </footer>
    </div>
    <script>
    $(document).ready(function() {
        $('#name').on('focus', function() {
            $("label[for='name']").addClass('active');
        }).on('blur', function() {
            if($(this).val().length == 0) {
                $("label[for='name']").removeClass('active');
            }
        });

        $('#email').on('focus', function() {
            $("label[for='email']").addClass('active');
        }).on('blur', function() {
            if($(this).val().length == 0) {
                $("label[for='email']").removeClass('active');
            }
        });

        $('#password').on('focus', function() {
            $("label[for='password']").addClass('active');
        }).on('blur', function() {
            if($(this).val().length == 0) {
                $("label[for='password']").removeClass('active');
            }
        });

        $('#password-2').on('focus', function() {
            $("label[for='password-2']").addClass('active');
        }).on('blur', function() {
            if($(this).val().length == 0) {
                $("label[for='password-2']").removeClass('active');
            }
        });

        $("#phone").inputmask({"mask": "(999) 999-9999", showMaskOnHover: false});

        $('#phone').on('focus', function() {
            $("label[for='phone']").addClass('active');
        }).on('blur', function() {
            if($(this).val().length == 0) {
                $("label[for='phone']").removeClass('active');
            }
        });

        $(".header-picture, .profile-picture").on('mouseenter', function() {
            $('.click-to-edit').toggleClass('invisible');
            $('.photo-selection').text($(this).attr('data-type'));
        }).on('mouseleave', function() {
            $('.click-to-edit').toggleClass('invisible');
        }).on('click', function() {
            alert('Upload a new ' + $(this).attr('data-type') + ' picture');
        });

        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            $('.navbar-collapse').toggleClass("padded");
        });
    });
    </script>
</body>

</html>
