<?php
function check_role($connection, $user_email)
{
	$query = "SELECT role FROM user WHERE email = '$user_email'" ;
	$result = $connection->query($query);
	if (! $result) return -1;
	if (! $result -> num_rows) return -1;
		// если нашли юзера с таким имейлом
	$users_count = $result -> num_rows;
	$user_role = $result -> fetch_array(MYSQLI_ASSOC);
	$result->close();
	return $user_role['role'];
}
?>