<?php
function getSemesterID() {
	
	$con = Database::open();
	$date = date('Y-m-d');
	$query = "SELECT id
			  FROM semester
			  WHERE $1 BETWEEN start_date AND end_date";
	pg_prepare($con, "", $query);
	$rs = pg_execute($con, "", array($date))
	or die("Query failed: " . pg_last_error());
	$row = pg_fetch_row($rs);
	return $row[0];
}


function addQuestionRow($author, $subject, $number, $assignment_name, $question, $likes,
                        $question_id, $resolution, $child) {
	$styleColor = '#450084';
	if ($child) {
		$styleColor = '#CBB677';
	}
	?>
	<tr>
		<td style="border-left: solid <?php echo $styleColor ?>; border-top: solid <?php echo
	$styleColor ?>; border-bottom:solid <?php echo $styleColor ?>;">
			<h4 class="ui image header">
				<div class="content">
			<?php echo $author; ?>
				</div>
			</h4>
		</td>
		<td style="border-top: solid <?php echo $styleColor ?>; border-bottom:solid <?php echo
	$styleColor ?>;">
		<?php echo $subject . " " . $number ?>
		</td>
		<td style="border-top: solid <?php echo $styleColor ?>; border-bottom:solid <?php echo
	$styleColor ?>;">
		<?php echo $assignment_name ?>
		</td>
		<td style="border-top: solid <?php echo $styleColor ?>; border-bottom:solid <?php echo
	$styleColor ?>;">
		<?php echo $question ?>
		</td>
		<td style="border-top: solid <?php echo $styleColor ?>; border-bottom:solid <?php echo
	$styleColor ?>;">
			<button class="like" onclick="like(<?php echo $question_id . ',' . $likes
	  ?>)"><i
						class="ui grey thumbs up icon"></i><?php echo $likes ?></button>
		</td>
		<td style="border-top: solid <?php echo $styleColor ?>; border-bottom:solid <?php echo
	$styleColor ?>;">
		<?php echo $resolution ?>
		</td>
	  <?php if (isset($_SESSION['role'])) {
		  if ($_SESSION['role'] == 'TA') {
			  ?>
						<td style="border-top: solid <?php echo $styleColor ?>; border-bottom:solid <?php echo
			$styleColor ?>;">
							<button class="ui button follow_up_button"
							        onclick="showFollowUpModal(<?php echo $question_id . ", '" .
				          $assignment_name . "', '" . $subject . ' ' . $number . "'" ?>);">Follow Up
							</button>
						</td>
						<td style="border-right: solid <?php echo $styleColor ?>; border-top:
								solid <?php echo $styleColor ?>; border-bottom:solid <?php echo $styleColor ?>;">
			  <button class="ui button follow_up_button"
			          onclick="showAnswerModal(<?php echo $question_id . ", '" .
		            $assignment_name . "', '" . $subject . ' ' . $number . "'" ?>);">Resolve
			  </button>
			  <?php
		  } else { ?>
						<td style="border-right: solid <?php echo $styleColor ?>; border-top: solid
			<?php
			echo $styleColor ?>; border-bottom:solid <?php echo
			$styleColor ?>;">
							<button class="ui button follow_up_button"
							        onclick="showFollowUpModal(<?php echo $question_id . ", '" .
				          $assignment_name . "', '" . $subject . ' ' . $number . "'" ?>);">Follow Up
							</button>
						</td>
			  <?php
		  }
	  } else { ?>
				<td style="border-right: solid <?php echo $styleColor ?>; border-top: solid <?php
		echo $styleColor ?>; border-bottom:solid <?php echo
		$styleColor ?>;">
					<button class="ui button follow_up_button"
					        onclick="showFollowUpModal(<?php echo $question_id . ", '" .
			          $assignment_name . "', '" . $subject . ' ' . $number . "'" ?>);">Follow Up
					</button>
				</td>
		  <?php
	  } ?>
	</tr>
	<?php
	$con = Database::open();
	$sql = 'SELECT c.author, subject, number, assignment.assignment_name, c.question, c.likes, c.id, c.resolution
							FROM question AS pq
							JOIN parent_child_question AS pc ON pc.parent_question_id = pq.id
							JOIN question AS c ON c.id = pc.child_question_id
							LEFT JOIN course ON c.course_id = course.id
							LEFT JOIN assignment ON c.assignment_id = assignment.id
							WHERE pq.id = $1
							ORDER BY c.time_asked;';
	pg_prepare($con, "", $sql);
	$rs = pg_execute($con, "", array($question_id))
	or die("Query failed: " . pg_last_error());
	while ($row = pg_fetch_row($rs)) {
		addQuestionRow($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], true);
	}
	pg_close($con);
	?>
<?php } ?>