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
                    <a href="#">Dashboard</a>
                </li>
                <li>
                    <a href="#">Shortcuts</a>
                </li>
                <li>
                    <a href="#">Overview</a>
                </li>
                <li>
                    <a href="#">Events</a>
                </li>
                <li>
                    <a href="#">About</a>
                </li>
                <li>
                    <a href="#">Services</a>
                </li>
                <li>
                    <a href="#">Contact</a>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <header>
                <!-- Fixed navbar -->
                <nav class="navbar navbar-expand fixed-top">
                    <a class="navbar-brand" href="#">sylvester</a>
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
                            <a class="dropdown-item" href="#">Profile</a>
                            <a class="dropdown-item" href="logout.php">Log out</a>
                          </div>
                        </li>
                </nav>
            </header>
            <!-- Begin page content -->
            <main role="main" class="container-fluid">
                <div class="row" style="margin-top: 60px">
                    <div class="col-sm-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><strong>New search</strong></h5>
                                <div class="row">
                                    <div class="col-md-12 col-lg-5 input-effect">
                                    	<form id="search_form">
                                        <div class="md-form">
                                            <input type="text" autocomplete="off" id="keyword" name="keyword" class="form-control">
                                            <label for="keyword" class="float-up keyword-label">Keywords</label>
                                            <span class="help d-none">Separate keywords with a space</span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-5 input-effect">
                                        <div class="md-form">
                                            <input type="text" autocomplete="off" class="form-control" name="location" id="location">
                                            <label for="location" class="float-up location-label">Location</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-2">
                                        <div class="md-form">
                                            <button type="submit" class="btn btn-hollow"><div class="submit-loader d-none" title="0">
                                              <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                               width="20px" height="20px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
                                              <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
                                                s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
                                                c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/>
                                              <path fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
                                                C22.32,8.481,24.301,9.057,26.013,10.047z">
                                                <animateTransform attributeType="xml"
                                                  attributeName="transform"
                                                  type="rotate"
                                                  from="0 20 20"
                                                  to="360 20 20"
                                                  dur="0.5s"
                                                  repeatCount="indefinite"/>
                                                </path>
                                              </svg>
                                            </div> submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                      <br>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                  <div class="col">
                                    <h5 class="card-title map-title"></h5>
                                  </div>
                                  <div class="col text-right share d-none">
                                    <a href="#"><i class="fas fa-arrow-circle-up"></i> Share</a>
                                  </div>
                                </div>
                              </h5>
                              <div id="map" style="height: 58vh"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><strong>Trending on Twitter</strong></h5>
                                <p class="card-text">Click on one of these hot hashtags that are trending worldwide right now</p>
                                <p class="card-text">
                                  <ul>
                                    <?php
                                    $url = "https://tagdef.com/en/";
                                    $ch = curl_init();
                                    $timeout = 5;
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                                    $html = curl_exec($ch);
                                    curl_close($ch);

                                    // Create a DOM parser object
                                    $dom = new DOMDocument();
                                    @$dom->loadHTML($html);

                                    // Iterate over all the <ul> tags
                                    $tags = $dom->getElementsByTagName('ul');
                                    $array = explode('#',$tags[4]->nodeValue);
                                    foreach (array_filter($array) as $key => $value) {
                                        echo "<li class='trending_topic'>#" . $value . "</li>";
                                    }
                                    ?>
                                  </ul>
                                </p>
                            </div>
                        </div>
                        <br>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><strong>Your recent searches</strong></h5>
                                <div class="card-text recent-searches">
                                    <?php
                                    // Load this first from the server, then let search.php take over to keep it updated
                                    $statement = $dbh->prepare('SELECT `time`, keyword, location FROM queries WHERE uid = :uid ORDER BY `time` DESC LIMIT 3');
                                    $statement->execute([
                                        'uid' => $auth->getCurrentUser()['uid']
                                    ]);
                                    // Print errors, if they exist
                                    if($statement->errorInfo()[0] != "00000") {
                                        print_r($statement->errorInfo());
                                        die();
                                    } else {
                                        $most_recent_queries = $statement->fetchAll(PDO::FETCH_ASSOC);
                                    }
                                    ?>
                                    <div class="row">
                                      <div class="col-8">
                                        <i class="fas fa-keyboard"></i> <?php echo $most_recent_queries[0]['keyword']; ?> <br><i class="fas fa-map-marker"></i> <?php echo $most_recent_queries[0]['location']; ?><br><i class="fas fa-clock"></i> <?php echo $most_recent_queries[0]['time']; ?>
                                      </div>
                                      <div class="col-4">
                                        <button type="button" class="btn btn-primary" data-toggle="button" aria-pressed="false" autocomplete="off">View map</button>
                                      </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                      <div class="col-8">
                                        <i class="fas fa-keyboard"></i> <?php echo $most_recent_queries[1]['keyword']; ?> <br><i class="fas fa-map-marker"></i> <?php echo $most_recent_queries[1]['location']; ?><br><i class="fas fa-clock"></i> <?php echo $most_recent_queries[1]['time']; ?>
                                      </div>
                                      <div class="col-4">
                                        <button type="button" class="btn btn-primary" data-toggle="button" aria-pressed="false" autocomplete="off">View map</button>
                                      </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                      <div class="col-8">
                                        <i class="fas fa-keyboard"></i> <?php echo $most_recent_queries[2]['keyword']; ?> <br><i class="fas fa-map-marker"></i> <?php echo $most_recent_queries[2]['location']; ?><br><i class="fas fa-clock"></i> <?php echo $most_recent_queries[2]['time']; ?>
                                      </div>
                                      <div class="col-4">
                                        <button type="button" class="btn btn-primary" data-toggle="button" aria-pressed="false" autocomplete="off">View map</button>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                              <small class="text-muted">See more <i class="fas fa-arrow-circle-right"></i></small>
                            </div>
                        </div>
                        <br>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><strong>Debug</strong></h5>
                                <pre id="debug"></pre>
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
                  <div class="col-6"><a href='https://www.mapbox.com/about/maps/' target='_blank'>Maps &copy; Mapbox &copy; OpenStreetMap</a></div>
                  <div class="col-6 text-right">Team Sylvester</div>
                </div>
            </div>
        </footer>
    </div>
    <script>
    mapboxgl.accessToken = 'pk.eyJ1IjoibWFzb25wYXdzZXkiLCJhIjoiY2puemkzb3N0MWY4djNra2JsZzBpaXpicSJ9.O8dFlt7FrskfE-GL8qvBUA';
    var map = new mapboxgl.Map({
        container: 'map', // container id
        style: 'mapbox://styles/masonpawsey/cjnzi73pd85jn2rmnm1oj9g65', // stylesheet location
        center: [-119.10506171751422, 35.34757450953665], // starting position [lng, lat]
        zoom: 4,
        interactive: true
    });

    $(document).ready(function() {
        $('input').focus(function() {
            $(this).next('.float-up').addClass('active');
        });

        $('input').focusout(function() {
            if ($(this).val().length < 1) {
                $(this).next('.float-up').removeClass('active');
            }
        });

        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            $('.navbar-collapse').toggleClass("padded");
        });

        $('#keyword').on('focus', function() {
            $('.help').removeClass("d-none");
            $('.help').addClass("d-block");
        });

        $('#keyword').on('blur', function() {
            $('.help').removeClass("d-block");
            $('.help').addClass("d-none");
        });

        $('#search_form').on('submit', function(e) {
            e.preventDefault();
            var keyword = $('#keyword').val();
            var location = $('#location').val();
            $('.submit-loader').toggleClass('d-none');
            $.ajax({
                url: 'search.php',
                type: 'POST',
                data: {
                    'keyword' : keyword,
                    'location' : location
                },
                success: function(data) {              
                    $('#debug').html(JSON.parse(data)[0]);
                    $('.map-title').html('<strong>Map for <u>'+keyword+'</u> in <u>' +location+ '</u></strong>');
                    $('.share').removeClass('d-none');
                    $('.submit-loader').toggleClass('d-none');
                    $('#keyword').val('').blur();
                    $('#location').val('').blur();
                    map.flyTo({
                        center: {lng: JSON.parse(data)[1][0], lat: JSON.parse(data)[1][1]},
                        zoom: 7
                    });

                    var recent_search = `<div class="row">
                                    <div class="col-8">
                                      <i class="fas fa-keyboard"></i> `+ JSON.parse(data)[2][0]['keyword'] +` <br><i class="fas fa-map-marker"></i> `+JSON.parse(data)[2][0]['location'] +`<br><i class="fas fa-clock"></i> `+JSON.parse(data)[2][0]['time']+`
                                    </div>
                                    <div class="col-4">
                                      <button type="button" class="btn btn-primary" data-toggle="button" aria-pressed="false" autocomplete="off">View map</button>
                                    </div>
                                  </div>
                                  <br>
                                  <div class="row">
                                    <div class="col-8">
                                      <i class="fas fa-keyboard"></i> `+ JSON.parse(data)[2][1]['keyword'] +` <br><i class="fas fa-map-marker"></i> `+JSON.parse(data)[2][1]['location'] +`<br><i class="fas fa-clock"></i> `+JSON.parse(data)[2][1]['time']+`
                                    </div>
                                    <div class="col-4">
                                      <button type="button" class="btn btn-primary" data-toggle="button" aria-pressed="false" autocomplete="off">View map</button>
                                    </div>
                                  </div>
                                  <br>
                                  <div class="row">
                                    <div class="col-8">
                                      <i class="fas fa-keyboard"></i> `+ JSON.parse(data)[2][2]['keyword'] +` <br><i class="fas fa-map-marker"></i> `+JSON.parse(data)[2][2]['location'] +`<br><i class="fas fa-clock"></i> `+JSON.parse(data)[2][2]['time']+`
                                    </div>
                                    <div class="col-4">
                                      <button type="button" class="btn btn-primary" data-toggle="button" aria-pressed="false" autocomplete="off">View map</button>
                                    </div>
                                  </div>`
                    $('.recent-searches').html(recent_search);
                },
                error: function(request,error)
                {
                    console.error("Request: "+JSON.stringify(request));
                }
            });
        })

        $('.trending_topic').on('click', function() {
            $('#keyword').focus();
            $('#keyword').val($(this).text());
            $('#location').focus();
        })

        // Cities are included in cities.js above
        $("#location").autocomplete({
            source: cities
        });
    });
    </script>
</body>

</html>
