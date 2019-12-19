<?php
    session_start();
    unset($_SESSION["user_id"]);
    // unset($_SESSION["name"]);
	$currentPage = $_SERVER['HTTP_REFERER']; // this will cause issues if someone bookmarks the
                                             // logout page
    header("Location:$currentPage");
    $_SESSION = array();
?>