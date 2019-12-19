<?php
if (!isset($_SESSION)) {
	session_start();
}
?>
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
			<h1>Welcome to the JMU CS TA Queue!</h1>
			<?php if (!isset($_SESSION['user_id'])) {
				?><h3>
					If you are a TA, please <a href="#" class="login-link">log in</a>.
				</h3>
				<h3>
					If you are a student, please take a look at the <a href="calendar.php">calendar</a>
					and <a href="student.php">queue</a> to check availabilities.
				</h3>
			<?php
			} else { ?>

				<h2 id="information_for_tas">Information for TAs</h2>
				<div>
					<p>
						Survey for Wed.: <a href="http://survey.perts.net/take/dlmooc?public_key=4132981d1" title="http://survey.perts.net/take/dlmooc?public_key=4132981d1">http://survey.perts.net/take/dlmooc?public_key=4132981d1</a>
					</p>
					<p>
						Lab Hours (ISAT/CS 248 and 250)
					</p>
					<ul>
						<li>
							<div> Mon-Thu 5:00pm – 11:00pm (Wednesdays 6:00pm - 11:00pm)</div>
						</li>
						<li>
							<div> Sunday 1:00pm – 11:00pm</div>
						</li>
					</ul>

					<h3>ISAT/CS Building Hours:</h3>
					</p>
					<ul>
						<li>
							<div> Mon-Thu 8:00am – 11:00pm</div>
						</li>
						<li>
							<div> Friday 8:00am – 5:00pm</div>
						</li>
						<li >
							<div> Saturday 1:00pm – 5:00pm</div>
						</li>
						<li>
							<div> Sunday 1:00pm – 11:00pm</div>
						</li>
					</ul>

				</div>
				
				<h3 id="ta_information_2019-2020">TA Information 2019-2020</h3>
				<div>

					<p>
						<a href="/ta/ta_guidelines_2019-2020"  title="ta:ta_guidelines_2019-2020">TA Guidelines 2019-2020</a>
					</p>

					<p>
						<a href="/ta/ta_meeting_notes_2018-19"  title="ta:ta_meeting_notes_2018-19">TA Meeting Notes 2018-19</a>
					</p>

					<p>
						<a href="/ta/lead_ta_qualtrics_reporting_link"  title="ta:lead_ta_qualtrics_reporting_link">Lead TA Qualtrics Reporting Link</a>
					</p>

					<p>
						<a href="/ta/2019_ta_schedule"  title="ta:2019_ta_schedule">2019 TA Schedule</a>
					</p>

					<p>
						<a href="/ta/spring_2019_programming_assignment_specifications"  title="ta:spring_2019_programming_assignment_specifications">Spring 2019 Programming Assignment Specifications</a>
					</p>

				<?php
				}
				?>
				</div>
		</div>
</body>

</html>