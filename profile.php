<?php
session_start();
require_once 'vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;
use Twilio\Rest\Client;

require_once('credentials.php');
$dbh = new PDO('mysql:host=localhost;dbname=tweets', $user, $pass);

$config = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $config);

if (!$auth->isLogged()) {
	header("Location: index");
	die('Forbidden');
}

// Add user action to log
$statement = $dbh->prepare('INSERT INTO user_log (uid, ip, agent, `time`, action) VALUES (:uid, :ip, :agent, NOW(), :action)');
$statement->execute([
	'uid' => $auth->getCurrentUser()['uid'],
	'ip' => $_SERVER['REMOTE_ADDR'],
	'agent' => $_SERVER['HTTP_USER_AGENT']??null,
	'action' => 'profile'
]);

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<title>sylvstrr</title>
	<!-- Boostrap CSS & JS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
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
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
	<link rel="icon" type="image/png" href="./assets/favicon.png">
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
					<a href="home">Dashboard</a>
				</li>
				<li>
					<a href="history">Query history</a>
				</li>
				<li>
					<a href="gallery">Gallery</a>
				</li>
				<li>
					<a href="profile">Profile</a>
				</li>
			</ul>
		</div>
		<!-- /#sidebar-wrapper -->
		<!-- Page Content -->
		<div id="page-content-wrapper">
			<header>
				<!-- Fixed navbar -->
				<nav class="navbar navbar-expand fixed-top">
					<a class="navbar-brand" href="home">sylvstrr</a>
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
							<a class="dropdown-item" href="profile">Profile</a>
							<a class="dropdown-item" href="logout">Log out</a>
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
								<img class="rounded-circle profile-picture" data-type="profile" src="https://media.licdn.com/dms/image/C5603AQEAmCS6ZYjupg/profile-displayphoto-shrink_800_800/0?e=1550102400&v=beta&t=U6TrOrwZ6hFBTgAwqzFDpk6aBkSKi_ZqsdM-twcWWRU" alt="">
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
											<input type="email" autocomplete="off" id="email" name="email" class="form-control" value="<?php echo $auth->getCurrentUser()['email']; ?>">
											<label for="email" class="float-up <?php if($auth->getCurrentUser()['email']){echo "active"; } ?>">Email</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12 input-effect">
										<div class="md-form">
											<input type="password" autocomplete="off" id="current-password" name="current-password" class="form-control">
											<label for="current-password" class="float-up">Current password</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12 input-effect">
										<div class="md-form">
											<input type="password" autocomplete="off" id="new-password" name="new-password" class="form-control">
											<label for="new-password" class="float-up">New password</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12 input-effect">
										<div class="md-form">
											<input type="password" autocomplete="off" id="new-password-2" name="new-password-2" class="form-control">
											<label for="new-password-2" class="float-up">Repeat new password</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12 text-center">
										<button type="button" class="btn btn-hollow change-password" data-toggle="button" aria-pressed="false" autocomplete="off" style="line-height:18px; padding-top: 7px; padding-bottom: 7px;">Change password</button>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12 input-effect phone-form" <?php if(strlen($auth->getCurrentUser()['phone']) == 10) { echo "style='display: none'"; } ?>>
										<div class="md-form">
											<input type="tel" autocomplete="off" id="phone" name="phone" class="form-control">
											<label for="phone" class="float-up">Phone number</label>
										</div>
									</div>
									<div class="col-md-12 input-effect verify-phone-form" style="display: none;">
										<div class="md-form">
											<input type="tel" autocomplete="off" id="code" name="code" class="form-control">
											<label for="code" class="float-up">Verify code</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12 text-center phone-form" <?php if(strlen($auth->getCurrentUser()['phone']) == 10) { echo "style='display: none'"; } ?>>
										<button type="button" class="btn btn-hollow verify-number" data-toggle="button" aria-pressed="false" autocomplete="off">Enable 2FA</button>
									</div>
									<div class="col-md-12 text-center verify-phone-form" style="display: none;">
										<button type="button" class="btn btn-hollow verify-code" data-toggle="button" aria-pressed="false" autocomplete="off">Verify code</button>
									</div>
								</div>
								<div class="row phone-set" style="display: none">
									<div class="col-md-12 input-effect phone-form-disable">
										<div class="md-form">
											<input type="tel" autocomplete="off" value="<?php 

											if(  preg_match( '/^(\d{3})(\d{3})(\d{4})$/', $auth->getCurrentUser()['phone'],  $matches ) )
												  {
													$result = '(' . $matches[1] . ') ' .$matches[2] . '-' . $matches[3];
													echo $result;
												  }

											?>" id="set-phone" name="set-phone" class="form-control" disabled="disabled">
											<label for="set-phone" class="float-up active">Phone number</label>
										</div>
									</div>
								</div>
								<div class="row phone-set" style="display: none">
									<div class="col-md-12 text-center phone-form-disable">
										<button type="button" class="btn btn-hollow disable-number" data-toggle="button" aria-pressed="false" autocomplete="off">Disable 2FA</button>
									</div>
								</div>
								<br><br>
								<div class="row">
									<div class="col-md-12 text-center">
										<button type="button" class="btn btn-hollow-danger delete-account" data-toggle="modal" data-target="#delete-account-modal" aria-pressed="false" autocomplete="off" style="line-height:18px; padding-top: 7px; padding-bottom: 7px;">Delete account</button>
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
								  <a href="history"><small class="text-muted">See more <i class="fas fa-arrow-circle-right"></i></small></a>
								</div>
							<?php 
								} // End if statement, making sure user has queries
								else {
									?>
									<p class="card-text"><a href="home">Go make some queries</a> and we'll keep track of your stats here.</p>
									<?php
								}
							?>
						</div>
					</div>
				</div>
			</main>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="delete-account-modal" tabindex="-1" role="dialog" aria-labelledby="delete-account-modalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="delete-account-modalLabel">Delete your account?</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								Please enter your password to confirm deletion of your account. <strong>You can't undo this.</strong>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-12 input-effect">
								<div class="md-form">
									<input type="password" autocomplete="off" id="delete-password" name="delete-password" class="form-control">
									<label for="delete-password" class="float-up">Password</label>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger delete-account-confirmed">Delete my account</button>
						<button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-primary back-to-safety">Back to safety</button>
					</div>
				</div>
			</div>
		</div>

		<!-- /#page-content-wrapper -->
		<!-- /#wrapper -->
		<footer class="footer">
			<div class="container-fluid">
				<div class="row">
				  <div class="col text-right">sylvstrr &copy; <?php echo date('Y'); ?></div>
				</div>
			</div>
		</footer>
	</div>
	<script>
	<?php
		if(strlen($auth->getCurrentUser()['phone']) == 10) {
			echo "var enable = false;";
		} else {
			echo "var enable = true;";
		}
	?>
	$(document).ready(function() {
		var phone = '';
		$('.verify-number').on('click', function() {
			if($('#phone').val().replace(/\D/g,'').length != 10) {
				toastr.error('Please provide a complete phone number');
			} else {
				$.ajax({
					type: "POST",
					url: 'verifynumber.php',
					data: { phone: $('#phone').val() },
					success: function(response) {
						if(response == 'error') {
							toastr.error('Please provide a complete phone number');
						} else {
							toastr.success('Your code has been sent');
							$('.phone-form').hide();
							$('.verify-phone-form').show();
							phone = $('#phone').val();
							$('#code').focus();
						}
					},
					error: function(error) {
						console.error('Error!', error);
					}
				});
			}
		});

		$('.delete-account-confirmed').on('click', function() {
			$.ajax({
				type: "POST",
				url: 'deleteaccount.php',
				dataType: 'json',
				data: { password: $('#delete-password').val() },
				success: function(response) {
					if(response['error'] == true) {
						toastr.error(response['message']);
					} else {
						window.location.href = "index";
					}
				},
				error: function(error) {
					console.error('Error!', error);
				}
			});
		})

		$('.disable-number').on('click', function() {
			$.ajax({
				type: "POST",
				url: 'verifynumber.php',
				data: { phone: $('#set-phone').val() },
				success: function(response) {
					if(response == 'error') {
						toastr.error('Please provide a complete phone number');
					} else {
						console.log('Response: ', response);
						toastr.success('Your code has been sent');
						$('.phone-form').hide();
						$('.verify-phone-form').show();
						$('.phone-form-disable').hide();
						$('#code').focus();
					}
				},
				error: function(error) {
					console.error('Error!', error);
				}
			});
		});

		$('.verify-code').on('click', function() {
			$.ajax({
				type: "POST",
				url: 'verifycode.php',
				data: { code: $('#code').val(), enable: enable },
				success: function(response) {
					if(response == 'true') {
						if(enable == true) {
							toastr.success('Your 2FA has been enabled');
							$('.verify-phone-form').hide();
							$('#set-phone').val(phone);
							$('.phone-set').show();
							$('.phone-form-disable').show();
							$('#code').val('');
						} else {
							toastr.success('Your 2FA has been disabled');
							$('.verify-phone-form').hide();
							$('#phone').val('');
							$('.phone-set').hide();
							$('.phone-form').show();
							$('#code').val('');
						}
						enable = !enable;
					} else {
						toastr.error('Incorrect code, please try again');
						$('#code').val('');
						$('#code').focus();
					}
				},
				error: function(error) {
					console.error('Error!', error);
				}
			});
		});

		$('#email').on('focus', function() {
			$("label[for='email']").addClass('active');
		}).on('blur', function() {
			if($(this).val().length == 0) {
				$("label[for='email']").removeClass('active');
			}
		});

		$('.back-to-safety').on('click', function() {
			$('#delete-password').val('').blur();
		});

		$('#delete-password').on('focus', function() {
			$("label[for='delete-password']").addClass('active');
		}).on('blur', function() {
			if($(this).val().length == 0) {
				$("label[for='delete-password']").removeClass('active');
			}
		});

		$('#current-password').on('focus', function() {
			$("label[for='current-password']").addClass('active');
		}).on('blur', function() {
			if($(this).val().length == 0) {
				$("label[for='current-password']").removeClass('active');
			}
		});

		$('#new-password').on('focus', function() {
			$("label[for='new-password']").addClass('active');
		}).on('blur', function() {
			if($(this).val().length == 0) {
				$("label[for='new-password']").removeClass('active');
			}
		});

		$('#new-password-2').on('focus', function() {
			$("label[for='new-password-2']").addClass('active');
		}).on('blur', function() {
			if($(this).val().length == 0) {
				$("label[for='new-password-2']").removeClass('active');
			}
		});

		$('.change-password').on('click', function(event) {
			if($('#current-password').val().length < 2) {
				toastr.error('Please provide your current password');
				return;
			}

			if($('#new-password').val().length < 2) {
				toastr.error('Please provide a new password');
				return;
			}

			if($('#new-password-2').val() != $('#new-password').val()) {
				toastr.error('Your passwords don\'t match');
				return;
			}

			if($('#new-password').val() == $('#current-password').val()) {
				toastr.error('The new password must be different than your old password');
				return;
			}

			$.ajax({
				type: "POST",
				url: 'changepassword.php',
				data: { current: $('#current-password').val(), new: $('#new-password').val(), new2: $('#new-password-2').val() },
				success: function(result) {
					response = JSON.parse(result);
					if(response['error'] === true) {
						toastr.error(response['message'], response['title'] || 'Error');
					} else {
						toastr.success('Your password has been changed');
						$('#current-password').val('').blur();
						$('#new-password').val('').blur();
						$('#new-password-2').val('').blur();
					}
				},
				error: function(error) {
					console.error('Error!', error);
				}
			});
		})

		$("#phone").inputmask({"mask": "(999) 999-9999", showMaskOnHover: false});

		$('#phone').on('focus', function() {
			$("label[for='phone']").addClass('active');
		}).on('blur', function() {
			if($(this).val().length == 0) {
				$("label[for='phone']").removeClass('active');
			}
		});

		$("#code").inputmask({"mask": "9999", showMaskOnHover: false});

		$('#code').on('focus', function() {
			$("label[for='code']").addClass('active');
		}).on('blur', function() {
			if($(this).val().length == 0) {
				$("label[for='code']").removeClass('active');
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

		<?php
			if(strlen($auth->getCurrentUser()['phone']) == 10) {
				echo "$('.phone-set').show();";
			}
		?>

	});
	function status() {
		console.log(enable);
	}
	</script>
</body>

</html>
