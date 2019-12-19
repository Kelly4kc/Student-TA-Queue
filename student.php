<?php if (!isset($_SESSION)) {
	session_start();
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<link rel="icon" href="/images/home-icon.png">
	<?php include("php_templates/HeadContents.php"); ?>
	<title>Student Queue</title>
	<script defer src="/js/student-queue-modal.js"></script>
	<script defer src="/js/student-likes.js"></script>
</head>

<body>
	<?php include("php_templates/DefaultMenu.php");
	include_once("php_helpers/Database.php");
	include_once("php_helpers/loginStatus.php");
	include_once("php_helpers/removeQueuer.php");
	include_once("php_helpers/Functions.php"); ?>
	<?php
	if (isset($_POST['StudentQueueSubmit'])) {
		$con = Database::open();
		$author = $_REQUEST['Name'];
		if ($_REQUEST['Course'] != '') {
			$courseId = $_REQUEST['Course'];
		} else {
			$courseId = 1;
		}
		if ($_REQUEST['Assignment'] != '') {
			$assignmentId = $_REQUEST['Assignment'];
		}
		$timeAdded = date('Y-m-d H:i:s');
		$query = "INSERT INTO queue(author, course_id, assignment_id, time_added)
							VALUES($1, $2, $3, $4)";
		pg_prepare($con, "", $query);
		pg_execute($con, "", array($author, $courseId, $assignmentId, $timeAdded))
		or die("Query failed: " . pg_last_error());
		$_POST = array();
	}
	?>
	<div class="ui main container">
		<!-- PUT ACTUAL PAGE CONTENT IN HERE SO THE MENU DOESN'T HIDE THINGS -->
		<div class="ui segment middle aligned center aligned">
			<h2>Add yourself to the Queue!</h2>
			<button class="ui button" id="student-queue-button">Add</button>
			<h3>There <?php
		  $con = Database::open();
		  $sql = 'SELECT count(*) from queue WHERE resolver_id IS NULL';
		  pg_prepare($con, "", $sql);
		  $rs = pg_execute($con, "", [])
		  or die("Query failed: " . pg_last_error());
		  pg_close($con);
		  $res = pg_fetch_result($rs, 0, 0);
		  if ($res != 1) {
			  echo "are " . $res . " people";
		  } else {
			  echo "is " . $res . " person";
		  }
		  ?> currently in the queue.</h3>
			<div class="ui divider"></div>
			<div class="ui grid centered">
				<div class="ui stacked segments">
					<h3 style="padding-top: 10px">
						Students Currently in the Queue
					</h3>
					<table style="width:100%;" class="ui celled collapsing table">
						<thead>
						<tr>
							<th class="center aligned two wide">Name</th>
							<th class="center aligned one wide">Course</th>
							<th class="center aligned two wide">Assignment</th>
				<?php if (isset($_SESSION['role'])) {
					if ($_SESSION['role'] == 'TA') {
						?>
											<th class="center aligned one wide">Remove</th><?php
					}
				} ?>
						</tr>
						</thead>
						<tbody>
			<?php
			$con = Database::open();
			$sql = 'SELECT author, subject, number, assignment.assignment_name, queue.id
							FROM queue
							LEFT JOIN course ON course_id = course.id
							LEFT JOIN assignment ON assignment.id = assignment_id
							WHERE resolver_id IS NULL
							ORDER BY time_added;';
			pg_prepare($con, "", $sql);
			$rs = pg_execute($con, "", [])
			or die("Query failed: " . pg_last_error());
			while ($row = pg_fetch_row($rs)) { ?>
						<tr>
							<td>
				  <?php echo $row[0]; ?>
							</td>
							<td>
				  <?php echo $row[1] . " " . $row[2]; ?>
							</td>
							<td>
				  <?php echo $row[3]; ?>
							</td>
				<?php if (isset($_SESSION['role']) and $_SESSION['role'] == 'TA') {
					?>
									<td>
					  <?php echo '<a class="ui button" href="../php_helpers/removeQueuer.php?queueId=' .
						  $row[4] . '"><i class="ui trash icon"></i></a>';
					  ?>
									</td>
					<?php
				}
				}
				?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="ui small modal" id="student-queue-modal">
				<div id="student-queue" class="ui middle aligned center aligned grid">
					<div class="column">
						<h2 class="ui jmuPurpleText image header">
							<div class="content">
								Add yourself to the queue
							</div>
						</h2>
						<form class="ui large form" id="student-queue-form" action="student.php" method="post">
							<div class="ui stacked segment">
								<div class="field">
									<div class="ui left icon input">
										<i class="user icon"></i>
										<input type="text" name="Name" placeholder="Name" id="name" required>
									</div>
								</div>
								<div class="field">
									<!-- <div class="ui left icon input"> -->

									<div class="ui selection dropdown">
										<i class="grey book icon"></i>
										<input type="hidden" name="Course">
										<i class="dropdown icon"></i>
										<div class="default text">Course</div>
										<div class="menu">
						<?php
						$con = Database::open();
						$sql = 'SELECT id, subject, number FROM course;';
						pg_prepare($con, "", $sql);
						$rs = pg_execute($con, "", [])
						or die("Query failed: " . pg_last_error());
						pg_close($con);
						while ($row = pg_fetch_row($rs)) {
							?>
													<div class="item" data-value="<?php echo $row[0] ?>">
							  <?php echo $row[1] . " " . $row[2] ?></div>
							<?php
						}
						?>
										</div>
									</div>
								</div>
								<div class="field">
									<div class="ui selection dropdown">
										<i class="grey book icon"></i>
										<input type="hidden" name="Assignment">
										<i class="dropdown icon"></i>
										<div class="default text">Assignment</div>
										<div class="menu">
						<?php
						$con = Database::open();
						$sql = 'SELECT assignment.id, course.number, course.subject, assignment_name
										FROM assignment
										JOIN course ON course.id = assignment.course_id
										ORDER BY course.number, assignment.due_date;';
						pg_prepare($con, "", $sql);
						$rs = pg_execute($con, "", [])
						or die("Query failed: " . pg_last_error());
						pg_close($con);
						while ($row = pg_fetch_row($rs)) {
							?>
													<div class="item" data-value="<?php echo $row[0] ?>">
							  <?php echo $row[1] . " " . $row[2] . ": " . $row[3]; ?></div>
							<?php
						}
						?>
										</div>
									</div>
								</div>
								<input class="ui fluid jmuPurple submit button" id="student-queue-submit"
								       type="submit" value="Submit" name="StudentQueueSubmit">
								<div class="ui error message"></div>

							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="ui small modal" id="student-follow-up-modal">
				<div id="student-queue" class="ui middle aligned center aligned grid">
					<div class="column">
						<h2 class="ui jmuPurpleText image header">
							<div class="content">
								Ask a Follow Up Question
							</div>
						</h2>
						<form class="ui large form" id="student-follow-up-form" action="student.php"
						      method="post">
							<div class="ui stacked segment">
								<div class="field">
									<input type="hidden" id="question_id" name="ParentQuestionId">
								</div>
								<div class="field">
									<div class="ui left icon input">
										<i class="user icon"></i>
										<input type="text" name="Name" placeholder="Name" id="name" required>
									</div>
								</div>
								<div class="field">
									<div class="ui left icon input">
										<i class="grey book icon"></i>
										<input value="Course: " id="follow_up_course" readonly>
									</div>
								</div>
								<div class="field">
									<div class="ui left icon input">
										<i class="grey book icon"></i>
										<input value="Assignment: " id="follow_up_assignment"
										       readonly>
									</div>
								</div>
								<div class="field">
									<textarea name="FollowUp" rows="5" placeholder="Follow Up" id="followUp"
									          required></textarea>
								</div>
								<input class="ui fluid jmuPurple submit button" id="student-follow-up-submit"
								       type="submit" value="Submit" name="FollowUpSubmit">
								<div class="ui error message"></div>

							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="ui small modal" id="ta-answer-modal">
				<div id="student-queue" class="ui middle aligned center aligned grid">
					<div class="column">
						<h2 class="ui jmuPurpleText image header">
							<div class="content">
								Resolve a Question
							</div>
						</h2>
						<form class="ui large form" id="ta-answer-form" action="student.php"
						      method="post">
							<div class="ui stacked segment">
								<div class="field">
									<input type="hidden" id="ta_answer_question_id" name="QuestionId">
								</div>
								<div class="field">
									<div class="ui left icon input">
										<i class="user icon"></i>
										<input type="hidden" name="ta_id" value="<?php echo $_SESSION['id'] ?>"
										       readonly="readonly" required>
										<input type="text" name="Name" placeholder="Name" id="name"
										       value="<?php echo $_SESSION['user_id'] ?>" readonly="readonly" required>
									</div>
								</div>
								<div class="field">
									<div class="ui left icon input">
										<i class="grey book icon"></i>
										<input value="Course: " id="ta_answer_course" readonly>
									</div>
								</div>
								<div class="field">
									<div class="ui left icon input">
										<i class="grey book icon"></i>
										<input value="Assignment: " id="ta_answer_assignment"
										       readonly>
									</div>
								</div>
								<div class="field">
									<textarea name="Resolution" rows="5" placeholder="Answer" id="answer"
									          required></textarea>
								</div>
								<input class="ui fluid jmuPurple submit button" id="ta-answer-submit"
								       type="submit" value="Submit" name="TAAnswerSubmit">
								<div class="ui error message"></div>

							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>