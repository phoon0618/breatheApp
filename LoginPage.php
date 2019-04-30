<?php 

require_once "config.php";
$loginUrl=$gClient->createAuthUrl();

?>

<!Doctype html>
<html lang="en">
<head>
	<title>Login Page</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../css/util.css">
	<link rel="stylesheet" type="text/css" href="../css/main.css">
<body>

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-form validate-form">
					<span class="login100-form-title p-b-43">
						Welcome to Breathe App
					</span>
					

					<div class="container-login100-form-btn">
						<button type="submit" class="login100-form-btn" onclick="window.location='<?php echo $loginUrl ?>'">Sign in with Google Account</button>
					</div>
					
					
					
				</div>

				<div class="login100-more" style="background-image: url('../images/bg-09.jpg');">
				</div>
			</div>
		</div>
	</div>

</body>
	
</html>

