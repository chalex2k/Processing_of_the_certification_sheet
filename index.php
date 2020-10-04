<?php
    $redirect_url = "controllers/index.php";
	header('HTTP/1.1 200 OK');
	header("Location: $redirect_url");
	exit();

?>