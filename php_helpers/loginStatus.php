<?php
// Always start this first
if (!isset($_SESSION)) {
	session_start();
}
include_once("Database.php");

if (!empty($_POST)) {
	if (isset($_POST['emailLogin']) && isset($_POST['passwordLogin'])) {
		// Getting submitted user data from database
		$con = Database::open();
		$loginSql = 'SELECT eid, role, id FROM person WHERE eid = $1 AND password = $2';
		pg_prepare($con, "", $loginSql);
		$loginInfo = pg_execute($con, "", array($_POST['emailLogin'], $_POST['passwordLogin']))
		or die("Query failed: " . pg_last_error());
		pg_close($con);
		if (pg_num_rows($loginInfo) == 1) {
			$loginStatus = pg_fetch_row($loginInfo);
			$_SESSION['user_id'] = $loginStatus[0];
			$_SESSION['role'] = $loginStatus[1];
			$_SESSION['id'] = $loginStatus[2];
		} else {
			echo '<script>';
			echo 'alert("Log in not successful, please check your credentials and try again.")';
			echo '</script>';
		}
		// Verify user password and set $_SESSION
		// if ( password_verify( $_POST['password'], $user->password ) ) {
		// 	$_SESSION['user_id'] = $user->ID;
		// }
	}
}
?>