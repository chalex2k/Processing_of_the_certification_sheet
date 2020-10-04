<?php
function check_role($connection, $user_email)
{
	$query = "SELECT role FROM user WHERE email = '$user_email'" ;
	$result = $connection->query($query);
	if (! $result) return -1; //die("$connection->connect_error"); // ссылка на страницу с ошибкой и завершение
	if (! $result -> num_rows) return -1;
	//{
	//	die("User not found");  // ссылка на страницу с ошибкой и завершение
	//}
		// если нашли юзера с таким имейлом
	$users_count = $result -> num_rows;
	$user_role = $result -> fetch_array(MYSQLI_ASSOC);
	$result->close();
	//if ($user_role['role'] != 'lecturer')  // студент пытается взломать нас
	//{
	//	die("Students haven't access to this page");
	//}
	return $user_role['role'];
}
?>