<?php
session_start();
	if (isset($_SESSION['user_email']))
	{
		destroy_session();
	}

	header('Location: index.php');

	function destroy_session()
	{
		$_SESSION = array();
		if (session_id() != "" || isset($_COOKIE[session_name()]))
			setcookie(session_name(), '', time() - 2592000, '/');
		session_destroy();
	}
?>