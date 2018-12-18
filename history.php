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

// Add user action to log
$statement = $dbh->prepare('INSERT INTO user_log (uid, ip, agent, `time`, action) VALUES (:uid, :ip, :agent, NOW(), :action)');
$statement->execute([
    'uid' => $auth->getCurrentUser()['uid'],
    'ip' => $_SERVER['REMOTE_ADDR'],
    'agent' => $_SERVER['HTTP_USER_AGENT']??null,
    'action' => 'history.php'
]);

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
    <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.50.0/mapbox-gl.css' rel='stylesheet' />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.13/css/mdb.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="home-style.css">
    <script type="text/javascript" src="cities.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">

    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
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
                    <img class="rounded-circle" style="width:35px; margin: 0 10px;" src="https://media.licdn.com/dms/image/C5603AQEAmCS6ZYjupg/profile-displayphoto-shrink_800_800/0?e=1550102400&v=beta&t=U6TrOrwZ6hFBTgAwqzFDpk6aBkSKi_ZqsdM-twcWWRU" alt="">
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
                    <div class="col-sm-2"></div>
                    <div class="col-sm-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><strong>Your history</strong></h5>
                                <?php
                                $statement = $dbh->prepare("
                                    SELECT keyword, COUNT(*) AS magnitude 
                                    FROM queries where uid = :uid
                                    GROUP BY keyword ORDER BY magnitude DESC LIMIT 1");
                                $statement->execute([
                                    'uid' => $auth->getCurrentUser()['uid']
                                ]);
                                // Print errors, if they exist
                                if($statement->errorInfo()[0] != "00000") {
                                    print_r($statement->errorInfo());
                                    die();
                                } else {
                                    $most_frequent_keyword = $statement->fetch(PDO::FETCH_ASSOC);
                                    // print_r($most_frequent_keyword);
                                }
                                if(empty($most_frequent_keyword)) {
                                    echo "<p class='card-text'>This is where we'll keep track of all your queries. <a href='home.php'>Go here</a> and make a query!</p>";
                                } else {
                                ?>  <p class="card-text">Here is a log of all your queries. <br>
                                    Your most frequent keyword is: <strong><?php echo $most_frequent_keyword['keyword']; ?></strong>. You've searched for it <strong>
                                    <?php
                                    echo $most_frequent_keyword['magnitude'];
                                    if($most_frequent_keyword['magnitude'] > 1) {
                                        echo " times.";
                                    } else {
                                        echo " time.";
                                    }
                                    ?></strong><br>
                                    <?php
                                    $statement = $dbh->prepare("SELECT location, COUNT(*) AS magnitude  FROM queries where uid = :uid GROUP BY location ORDER BY magnitude DESC LIMIT 1");
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
                                    ?>
                                    Your most frequent location is: <strong><?php echo $most_frequent_location['location']; ?></strong>. You've searched for it <strong>
                                    <?php
                                    echo $most_frequent_location['magnitude'];
                                    if($most_frequent_location['magnitude'] > 1) {
                                        echo " times.";
                                    } else {
                                        echo " time.";
                                    }
                                    ?></strong>
                                <?php 
                                // End the 'else' leg of the above condition. The above will only print if the user has made a query
                                } 
                                ?>
                                <table id="table" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Keyword</th>
                                                <th>Location</th>
                                                <th>Time</th>
                                                <th>Sentiment</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                // Get the users searches!
                                                $statement = $dbh->prepare("SELECT keyword, location, CONVERT_TZ(`time`,'+00:00','-08:00') as `time` FROM queries WHERE uid = :uid ORDER BY `time` DESC");
                                                $statement->execute([
                                                    'uid' => $auth->getCurrentUser()['uid']
                                                ]);
                                                // Print errors, if they exist
                                                if($statement->errorInfo()[0] != "00000") {
                                                    print_r($statement->errorInfo());
                                                    die();
                                                } else {
                                                    $most_recent_queries = $statement->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($most_recent_queries as $key => $value) {
                                                        $time = date("M d, Y, h:i:s a", strtotime($value['time']));
                                                        $sentiment = ['<i class="fas fa-arrow-up"></i> <span class="text-success">positive</span>', '<i class="fas fa-arrow-down"></i> <span class="text-danger">negative</span>', '<i class="fas fa-arrow-right"></i> <span class="text-dark">neutral</span>'][rand(0,2)];
                                                        echo "<tr>
                                                            <td>".$value['keyword']."</td>
                                                            <td>".$value['location']."</td>
                                                            <td>".$time."</td>
                                                            <td>".$sentiment."</td></tr>";

                                                    }
                                                }
                                                ?>
                                        </tbody>
                                    </table>
                            </div>
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
        $('#table').DataTable({
            "order": [[ 2, "desc" ]],
            dom: 'Bfrtip',
            buttons: [
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        });

        $('.dt-button').each(function(){
            $(this).addClass('btn btn-hollow');
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
