<?php 

require_once "config.php";
$loginUrl=$gClient->createAuthUrl();

?>

<!Doctype html>
<html>
<head>
<title>Login Page</title>
</head>
<body>

<button type="submit" onclick="window.location='<?php echo $loginUrl ?>'">Sign with Google Account</button>
</body>
</html>

