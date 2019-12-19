<?php if (!isset($_SESSION)) {
	session_start();
}
include_once("php_helpers/loginStatus.php");
include_once("php_helpers/approveCover.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<link rel="icon" href="/images/calendar-flat.png">
	<script src="https://unpkg.com/vue"></script>
	<script defer src="https://unpkg.com/vue-cal"></script>
	<script src="js/get-events.js"></script>
	<?php include("php_templates/HeadContents.php"); ?>
	<title>Calendar</title>
	<link href="https://unpkg.com/vue-cal/dist/vuecal.css" rel="stylesheet">

	<script>
		<?php if (isset($_SESSION['user_id'])) {
			if ($_SESSION['role'] == 'Manager') {
				$editableEvents = 'editable-events';
			} else {
				$editableEvents = '';
			}
			?>
			get_events("ta");
		<?php } else {
			$editableEvents = '';
			?>
			get_events("student");
		<?php
		} ?>
	</script>
</head>

<body>
	<?php include("php_templates/DefaultMenu.php"); ?>
	<?php
	if (isset($_POST['SubmitCover'])) {
		$con = Database::open();
		$id = $_SESSION['id'];
		$date = $_POST['date'];
		$start = $_POST['start' . $date];
		$end = $_POST['end' . $date];
		$date = gmdate("Y-m-d", strtotime($date . ' +1 day'));
		$query = "INSERT INTO cover(unavailable_ta_id, date, start_time, end_time)
									VALUES($1, date($2), $3, $4)";
		pg_prepare($con, "", $query);
		pg_execute($con, "", array($id, $date, $start, $end))
			or die("Query failed: " . pg_last_error());
		$_POST = array();
	}
	if (isset($_POST['SubmitFulfill'])) {
		$con = Database::open();
		$coverRequestId = $_REQUEST['CoverFulfill'];
		$potentialTaId = $_REQUEST['ta_id'];
		$query = "INSERT INTO potential_cover(cover_id, potential_ta_id)
									VALUES($1, $2)";
		pg_prepare($con, "", $query);
		pg_execute($con, "", array($coverRequestId, $potentialTaId))
			or die("Query failed: " . pg_last_error());
		$_POST = array();
	}
	?>
	<div class="ui main container">
		<!-- PUT ACTUAL PAGE CONTENT IN HERE SO THE MENU DOESN'T HIDE THINGS -->
		<h2 class="ui center aligned header">TA Schedule</h2>
		<?php if (isset($_SESSION['role'])) {
			if ($_SESSION['role'] == 'TA') { ?>
				<div class="ui jmuPurple right aligned button" onclick="$('#requestModal').modal('show');">
					Request Cover
				</div>
				<div class="ui jmuPurple right aligned button" onclick="$('#fulfillModal').modal('show');">
					Fulfill Cover Request
				</div>
			<?php }
				if ($_SESSION['role'] == 'Manager') { ?>
				<div class="ui jmuPurple right aligned button" onclick="$('#coverModal').modal('show');">
					Approve Covers
				</div>
				<div class="ui jmuPurple right aligned button" onclick="$('#scheduleModal').modal('show');">
					Set Schedule
				</div>
		<?php
			}
		} ?>
		<div id='calendar' style="height: 100%;">
			<vue-cal class="vuecal--purple-theme" small :events="events" start-week-on-sunday :disable-views="['years', 'year', 'month']" :time-from="13 * 60" :time-to="23 *
			         60" :time-step="60" default-view="week" <?php echo $editableEvents ?> :show-all-day-events='true'>
			</vue-cal>
		</div>
	</div>
	<?php if (isset($_SESSION['role'])) {
		if ($_SESSION['role'] == 'Manager') { ?>
			<div class="ui small modal" id="coverModal">
				<div id="cover" class="ui middle aligned center aligned grid">
					<div class="column">
						<h2 class="ui jmuPurpleText image header">
							<div class="content">
								Approve Covers
							</div>
						</h2>
						<table style="width:80%;" class="ui celled collapsing table">
							<thead>
								<tr>
									<th class="center aligned two wide">Name</th>
									<th class="center aligned one wide">Date</th>
									<th class="center aligned two wide">Start Time</th>
									<th class="center aligned three wide">End Time</th>
									<th class="center aligned one wide">Potential Cover</th>
									<th class="center aligned one wide">Approve</th>
								</tr>
							</thead>
							<tbody>
								<?php
										$con = Database::open();
										$sql = 'SELECT cover.id, p1.first_name || \' \' || p1.last_name AS "Name", date, start_time, end_time, p2.first_name ||\' \' || p2.last_name AS "Cover Name", p2.id
										FROM cover
										JOIN person p1 ON p1.id = cover.unavailable_ta_id
										JOIN potential_cover ON potential_cover.cover_id = cover.id
										JOIN person p2 ON p2.id = potential_cover.potential_ta_id
										WHERE cover.cover_ta_id IS NULL;';
										pg_prepare($con, "", $sql);
										$rs = pg_execute($con, "", [])
											or die("Query failed: " . pg_last_error());
										pg_close($con);
										while ($row = pg_fetch_row($rs)) {
											?>
									<tr>
										<td>
											<?php echo $row[1] ?>
										</td>
										<td>
											<?php echo $row[2] ?>
										</td>
										<td>
											<?php echo $row[3] ?>
										</td>
										<td>
											<?php echo $row[4] ?>
										</td>
										<td>
											<?php echo $row[5] ?>
										</td>
										<?php if (isset($_SESSION['role'])) {
														if ($_SESSION['role'] == 'Manager') {
															?>
												<td>
													<?php
																		echo '<a class="ui button" href="../php_helpers/approveCover.php?coverId=' . $row[0] . '&tacoverId=' . $row[6] . '"><i class="ui green check icon"></i></a>';
																		?>
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
			<div class="ui small modal" id="scheduleModal">
				<div id="schedule-thing" class="ui middle aligned center aligned grid">
					<div class="column">
						<h2 class="ui jmuPurpleText image header">
							<div class="content">
								Set Schedule
							</div>
							<div class="ui divider"></div>
							<div class="ui grid centered">
						</h2>
						<table style="width:80%;" class="ui celled collapsing table">
							<thead>
								<tr>
									<th class="center aligned two wide">Name</th>
									<th class="center aligned one wide">Day</th>
									<th class="center aligned two wide">Start Time</th>
									<th class="center aligned two wide">End Time</th>
									<th class="center aligned three wide">Course Experience</th>
									<th class="center aligned two wide">Approve</th>
								</tr>
							</thead>
							<tbody>
								<?php
										$con = Database::open();
										$query = 'SELECT first_name || \' \' || last_name AS name, day, start_time, end_time, ta_availability.ta_id, ta_availability.semester_id, STRING_AGG(\'CS\' || course.number::varchar(255), \',\')
										FROM ta_availability
										JOIN person ON person.id = ta_availability.ta_id
										LEFT JOIN ta_course_experience ON ta_course_experience.ta_id = ta_availability.ta_id
										LEFT JOIN course ON course.id = ta_course_experience.course_id
										GROUP BY first_name, last_name, day, start_time, end_time, ta_availability.ta_id, ta_availability.semester_id
										ORDER BY day ASC';
										pg_prepare($con, "", $query);
										$rs = pg_execute($con, "", array())
											or die("Query failed: " . pg_last_error());
										while ($row = pg_fetch_row($rs)) {
											echo '<tr>';
											echo '<td>' . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[6] . "</td>";
											echo '<td><a class="ui button" href="../php_helpers/approveSchedule.php?taId=' . $row[4] . '&semesterId=' . $row[5] . '&day=' . $row[1] . '&startTime=' . $row[2] . '&endTime=' . $row[3] .'"><i class="ui green check icon"></i></a></td>';
											echo '</tr>';
										}
										?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			</div>
	<?php }
	} ?>
	<?php if (isset($_SESSION['role'])) {
		if ($_SESSION['role'] == 'TA') { ?>
			<div class="ui small modal" id="requestModal">
				<div id="request-cover" class="ui middle aligned center aligned grid">
					<div class="column">
						<h2 class="ui jmuPurpleText image header">
							<div class="content">
								Request a Cover
							</div>
						</h2>
						<form class="ui large form" id="request-cover-form" action="calendar.php" method="post">
							<div class="ui stacked segment">
								<div class="field">
									<div class="ui left icon input">
										<i class="user icon"></i>
										<input type="text" name="Name" placeholder="Name" id="name" value="<?php echo $_SESSION['user_id'] ?>" readonly="readonly" required>
									</div>
								</div>
								<div class="field">

									<?php
											$con = Database::open();
											$user_id = $_SESSION['id'];
											$query = "SELECT day, start_time, end_time FROM schedule WHERE ta_id = $1";
											pg_prepare($con, "", $query);
											$rs = pg_execute($con, "", array($user_id))
												or die("Query failed: " . pg_last_error());
											pg_close($con);
											$count = 0;
											while ($row = pg_fetch_row($rs)) {

												?>

										<div class="ui 3 column horizontal segments">
											<div class="ui segment alignMiddle">
												<div class="field">
													<div class="ui radio checkbox">
														<input type="radio" name="date" id="<?php echo $row[0] ?>" value="<?php echo $row[0] ?>">
														<label for="<?php echo $row[0] ?>"><?php echo $row[0] ?></label>
													</div>
													<input type="hidden" value="<?php echo $row[1] ?>" name="start<?php echo $row[0] ?>">
													<input type="hidden" value="<?php echo $row[2] ?>" name="end<?php echo $row[0] ?>">
												</div>
											</div>
											<div class="ui left aligned segment">
												<div class="field">
													<input type="time" step="900" value="<?php echo $row[1] ?>:00" readonly>
												</div>
											</div>
											<div class="ui left aligned segment">
												<div class="field">
													<input type="time" step="900" value="<?php echo $row[2] ?>:00" readonly>
												</div>
											</div>
										</div>
									<?php

											}
											?>
								</div>
								<input class="ui fluid jmuPurple submit button" id="cover-submit" type="submit" value="Submit" name="SubmitCover">
								<div class="ui error message"></div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="ui small modal" id="fulfillModal">
				<div id="request-cover" class="ui middle aligned center aligned grid">
					<div class="column">
						<h2 class="ui jmuPurpleText image header">
							<div class="content">
								Fulfill a Cover Request
							</div>
						</h2>
						<form class="ui large form" id="request-cover-form" action="calendar.php" method="post">
							<div class="ui stacked segment">
								<div class="field">
									<div class="ui left icon input">
										<i class="user icon"></i>
										<input type="hidden" name="ta_id" value="<?php echo $_SESSION['id'] ?>" readonly="readonly" required>
										<input type="text" name="Name" placeholder="Name" id="name" value="<?php echo $_SESSION['user_id'] ?>" readonly="readonly" required>
									</div>
								</div>
								<div class="field">
									<div class="ui selection dropdown">
										<i class="grey book icon"></i>
										<input type="hidden" name="CoverFulfill">
										<i class="dropdown icon"></i>
										<div class="default text">Cover Request</div>
										<div id="dropdown" class="menu">
											<?php
													$con = Database::open();
													$sql = 'SELECT cover.id, first_name, last_name, date, start_time, end_time
										FROM cover
										JOIN person ON person.id = cover.unavailable_ta_id
										WHERE $1 NOT IN (SELECT potential_ta_id
								                    FROM potential_cover
								                    WHERE potential_cover.cover_id = cover.id)
											AND cover_ta_id is NULL AND cover.unavailable_ta_id != $1;';
													pg_prepare($con, "", $sql);
													$rs = pg_execute($con, "", array($_SESSION['id']))
														or die("Query failed: " . pg_last_error());
													pg_close($con);
													while ($row = pg_fetch_row($rs)) {
														?>
												<div class="item" data-value="<?php echo $row[0] ?>">
													<?php echo $row[1] . " " . $row[2] . " " . gmdate("m/d/Y", strtotime($row[3])) .
																	" " . ($row[4] - 12) . ":00-" . ($row[5] - 12) . ":00 PM";
																?></div>
											<?php
													}
													?>
										</div>
									</div>
								</div>
								<input class="ui fluid jmuPurple submit button" id="fulfill-submit" type="submit" value="Submit" name="SubmitFulfill">
								<div class="ui error message"></div>
							</div>
						</form>
					</div>
				</div>
			</div>
	<?php }
	} ?>
	<script>
		$('.ui.selection.dropdown').dropdown();
		// $('.ui.radio.checkbox')
		// 	.checkbox();
	</script>
</body>

</html>