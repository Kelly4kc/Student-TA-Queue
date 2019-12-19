<?php
if (!isset($_SESSION)) {
	session_start();
}
include_once("php_helpers/loginStatus.php");
?>

<script defer src="/js/login-modal.js"></script>
<div class="jmuPurple ui inverted fixed menu ">
	<h1 class="ui item">TA Queue</h1>
	<a class="ui <?php if (strval($_SERVER['REQUEST_URI']) == '/') {
	  echo 'active';
  } ?> item" href="..">Home</a>
	<a class="ui <?php if (strpos($_SERVER['REQUEST_URI'], 'calendar')) {
	  echo 'active';
  } ?> item" href="../calendar.php">Calendar</a>
	<a class="ui <?php if (strpos($_SERVER['REQUEST_URI'], 'student')) {
	  echo 'active';
  } ?> item" href="../student.php">Student Queue</a>
	<a class="ui <?php if (strpos($_SERVER['REQUEST_URI'], 'questions')) {
	  echo 'active';
  } ?> item" href="../questions.php">Questions</a>
	<a class="ui <?php if (strpos($_SERVER['REQUEST_URI'], 'assignments')) {
	  echo 'active';
  } ?> item" href="../assignments.php">Assignments</a>
	<?php if (isset($_SESSION['user_id'])) { ?>
			<div class="jmuPurple inverted fixed  right menu">
				<div class="ui item">Logged in as: <?php echo $_SESSION['user_id'] ?> </div>
				<a class="ui item" href="../php_helpers/logout.php">Log out</a>
		  <?php if ($_SESSION['role'] == 'TA') { ?>
						<a class="ui item setting icon" href="../ta_preferences.php"><i
									class="setting icon"></i></a>
		  <?php } ?>
			</div>
	<?php } else { ?>
			<a class="ui right item" id="login-button">Log in</a>
	<?php } ?>

</div>
<div class="ui small modal" id="login-modal">
	<div class="ui bottom attached segment" id="loginTabSegment">
		<div id="login" class="ui middle aligned center aligned grid">
			<div class="column">
				<h2 class="ui jmuPurpleText image header">
					<div class="content">
						Log in to your account
					</div>
				</h2>
				<form class="ui large form" id="loginForm" method="post">
					<div class="ui stacked segment">
						<div class="field">
							<div class="ui left icon input">
								<i class="user icon"></i>
								<input type="text" name="emailLogin" placeholder="E-id" required>
							</div>
						</div>
						<div class="field">
							<div class="ui left icon input">
								<i class="lock icon"></i>
								<input type="password" name="passwordLogin" placeholder="Password" required>
							</div>
						</div>
						<!-- <div class="ui fluid jmuPurple submit button" id="loginSubmit">Log in</div> -->
						<input type="submit" value="Log In" class="ui fluid jmuPurple submit button"/>
					</div>
					<div class="ui error message"></div>
				</form>
			</div>
		</div>
	</div>
</div>