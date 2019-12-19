<?php if (!isset($_SESSION)) {
	session_start();
}
if (!isset($_SESSION['role']) or $_SESSION['role'] != 'TA') { ?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<link rel="icon" href="/images/home-icon.png">
	  <?php include_once("php_templates/HeadContents.php"); ?>
		<title>Home</title>
		<style>
			ul {
				list-style-type: none;
			}
		</style>
	</head>

	<body>
	  <?php include_once("php_templates/DefaultMenu.php");
	  include_once("php_helpers/Database.php");
	  include_once("php_helpers/loginStatus.php")
	  ?>
		<div class="ui main container">
			<!-- PUT ACTUAL PAGE CONTENT IN HERE SO THE MENU DOESN'T HIDE THINGS -->
			<div class="ui segment middle aligned center aligned">
				<h3>
					Sorry, only TAs may access the TA preferences page
				</h3>
			</div>
		</div>
	</body>
<?php } else { ?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<link rel="icon" href="/images/home-icon.png">
	  <?php include("php_templates/HeadContents.php");
	  include_once("php_helpers/Functions.php");
	  include_once("php_helpers/loginStatus.php"); ?>
		<script defer src="js/ta-pref.js"></script>
		<title>Home</title>
	</head>

	<body>
	  <?php
	  $con = Database::open();
	  $userId = $_SESSION['id'];
	  $semesterId = getSemesterID();
	  $query = "SELECT day
						FROM semester_schedule
						WHERE semester_id = $1;";
	  pg_prepare($con, "", $query);
	  $rs = pg_execute($con, "", array($semesterId))
	  or die("Query failed: " . pg_last_error());
	  $days = array();
	  while ($row = pg_fetch_row($rs)) {
		  $days[] = $row[0];
	  }
	  if (isset($_POST['submitPrefs'])) {
		  foreach ($days as $day) {
			  if (isset($_REQUEST[strtolower($day) . 'CheckBox'])) {
				  $startTime = str_replace(":00", "", $_REQUEST[strtolower($day) . 'StartTime']);
				  $endTime = str_replace(":00", "", $_REQUEST[strtolower($day) . 'EndTime']);
				  $query = "INSERT INTO ta_availability(ta_id, day, start_time, end_time, semester_id)
									VALUES($1, $2, $3, $4, $5);";
				  pg_prepare($con, "", $query);
				  $rs = pg_execute($con, "", array($userId, $day, $startTime, $endTime, $semesterId))
				  or die("Query failed: " . pg_last_error());
			  }
		  }
		  if (isset($_REQUEST['max-hours'])) {
			  $maxHours = $_REQUEST['max-hours'];
			  $query = "UPDATE person
		            SET max_hours = $1
								WHERE id = $2;";
			  pg_prepare($con, "", $query);
			  $rs = pg_execute($con, "", array($maxHours, $userId))
			  or die("Query failed: " . pg_last_error());
		  }
		  if (isset($_REQUEST['courses'])) {
			  foreach ($_REQUEST['courses'] as $courseID) {
				  $query = "INSERT INTO ta_course_experience
											  VALUES($1, $2)";
				  pg_prepare($con, "", $query);
				  $rs = pg_execute($con, "", array($userId, $courseID))
				  or die("Query failed: " . pg_last_error());
			  }
		  }
		  
		  $_POST = array();
	  }
	  pg_close($con);
	  ?>
	  <?php include("php_templates/DefaultMenu.php"); ?>
		<div class="ui main container">
			<div class="ui center aligned grid">
				<div class="six wide column">
					<h2 class="ui jmuPurpleText image header">
						<div class="content">
							Set Availability
						</div>
					</h2>
					<form class="ui large form" method="POST">
						<div class="ui stacked segment">
							<div class="content">
								Please select days and times you will be available this semester from the checkboxes
								below.
							</div>
				<?php
				foreach ($days as $day) { ?>
									<div class="ui 3 column horizontal segments">
										<div class="ui segment alignMiddle">
											<div class="field">
												<div class="ui checkbox">
													<input type="checkbox" id="<?php echo strtolower($day) ?>"
													       autocomplete="off" name="<?php echo strtolower($day) ?>CheckBox">
													<label for="<?php echo strtolower($day) ?>"><?php echo $day ?></label>
												</div>
											</div>
										</div>
										<div class="ui left aligned segment">
											<div class="field">
						  <?php
						  $con = Database::open();
						  $userId = $_SESSION['id'];
						  $query = "SELECT start_time, end_time FROM ta_availability WHERE day = $1 AND ta_id = $2";
						  pg_prepare($con, "", $query);
						  $rs = pg_execute($con, "", array($day, $userId))
						  or die("Query failed: " . pg_last_error());
						  $query = "SELECT start_time, end_time
											FROM semester_schedule
											WHERE day = $1";
						  pg_prepare($con, "", $query);
						  $rs = pg_execute($con, "", array($day))
						  or die("Query failed: " . pg_last_error());
						  $row = pg_fetch_row($rs);
						  $defaultStartTime = $row[0];
						  $defaultEndTime = $row[1];
						  pg_close($con);
						  if ($row = pg_fetch_row($rs)) {
							  $startTime = $row[0];
							  $endTime = $row[1];
						  } else {
							  $startTime = $defaultStartTime;
							  $endTime = $defaultEndTime;
						  }
						  ?>
												<input name="<?php echo strtolower($day) ?>StartTime" type="time"
												       step="3600"
												       value="<?php echo $startTime ?>:00"
												       autocomplete="off" min="<?php echo $defaultStartTime ?>:00"
												       max="<?php echo($defaultEndTime - 1) ?>:00">
											</div>
										</div>
										<div class="ui left aligned segment">
											<div class="field">
												<input name="<?php echo strtolower($day) ?>EndTime" type="time"
												       step="3600"
												       value="<?php echo $endTime ?>:00"
												       autocomplete="off" min="<?php echo($defaultStartTime + 1) ?>:00"
												       max="<?php echo $defaultEndTime ?>:00">
											</div>
										</div>
									</div>
					<?php
				}
				?>
							<div class="content">
								Please enter the maximum hours you would like to work in a week.
							</div>
							<input type="number" name="max-hours" min="1" max="10" required>
							<div class="content">
								Please select any courses you have taken from the dropdown below.
							</div>
							<select name="courses[]" multiple="" class="label ui selection fluid dropdown">
								<option value="">Course Experience</option>
				  <?php
				  $con = Database::open();
				  $sql = 'SELECT *
								FROM course
								EXCEPT (SELECT course.id, course.subject, course.number
								        FROM course
								        JOIN ta_course_experience ON course.id = ta_course_experience.course_id
								        JOIN person ON $1 = ta_course_experience.ta_id)
								ORDER BY number';
				  pg_prepare($con, "", $sql);
				  $rs = pg_execute($con, "", array($userId))
				  or die("Query failed: " . pg_last_error());
				  pg_close($con);
				  while ($row = pg_fetch_row($rs)) {
					  ?>
										<option value="<?php echo $row[0] ?>">
						<?php echo $row[1] . " " . $row[2] ?></option>
					  <?php
				  }
				  ?>
							</select>
							<input type="submit" class="ui fluid jmuPurple submit button" value="Submit"
							       name="submitPrefs">
						</div>
						<div class="ui error message"></div>
					</form>
				</div>
				<div class="four wide column">
					<h2 class="ui jmuPurpleText image header">
						<div class="content">
							Current Course Experience
						</div>
					</h2>
					<div class="ui grid centered" style="margin: 0">
						<div class="ui stacked segment">
							<div class="content">
								This table contains the courses that you have previously selected. If you selected
								one by mistake, you can remove it by clicking the trash can icon next to it.
							</div>
							<table class="ui celled collapsing table">
								<thead>
								<tr>
									<th class="center aligned two wide">Course</th>
									<th class="center aligned two wide">Remove</th>
								</tr>
								</thead>
								<tbody>
				<?php
				$con = Database::open();
				$sql = 'SELECT course.subject, course.number, course.id
							FROM course
							JOIN ta_course_experience ON course.id = ta_course_experience.course_id
								AND ta_course_experience.ta_id = $1;';
				pg_prepare($con, "", $sql);
				$rs = pg_execute($con, "", array($userId))
				or die("Query failed: " . pg_last_error());
				pg_close($con);
				while ($row = pg_fetch_row($rs)) {
					?>
									<tr>
										<td>
						<?php echo $row[0] . " " . $row[1] ?>
										</td>
					  <?php if (isset($_SESSION['role'])) {
						  if ($_SESSION['role'] == 'TA') {
							  ?>
														<td>
															<button class="remove"
															        onclick="removeTACourse(<?php echo $userId . ", " .
								          $row[2] ?>);
																	        window.location.reload();"><i class="ui trash icon"></i>
															</button>
														</td>
							  <?php
						  }
					  } ?>
									</tr>
					<?php
				}
				?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>

	</html>
<?php } ?>