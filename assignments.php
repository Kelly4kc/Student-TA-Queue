<?php if (!isset($_SESSION)) {
    session_start();
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" href="/images/home-icon.png">
    <?php include("php_templates/HeadContents.php"); ?>
    <title>Assignments</title>
    <script defer src="/js/assignment-modal.js"></script>
</head>

<body>
    <?php include("php_templates/DefaultMenu.php");
    include_once("php_helpers/Database.php");
    include_once("php_helpers/loginStatus.php");
    include_once("php_helpers/removeAssignment.php");
    include_once("php_helpers/Functions.php");?>
    <?php
    if (isset($_POST['SubmitAssignment'])) {
        $con = Database::open();
        $Professor = $_SESSION['id'];
        $Course = $_REQUEST['Course'];
        $Assignment = $_REQUEST['Assignment'];
        $AssignmentLink = $_REQUEST['AssignmentLink'];
        $DueDate = $_REQUEST['AssignmentDate'];
        $semesterId = getSemesterID();
        $query = "INSERT INTO assignment(professor_id, course_id, assignment_name, assignment_link, due_date, semester_id)
							VALUES($1, $2, $3, $4, $5, $6)";
        pg_prepare($con, "", $query);
        pg_execute($con, "", array($Professor, $Course, $Assignment, $AssignmentLink, $DueDate, $semesterId))
            or die("Query failed: " . pg_last_error());
        $_POST = array();
    }
    ?>
    <div class="ui main container">
        <div class="ui segment middle aligned center aligned">
            <h1>Assignments</h1>
            <?php if (isset($_SESSION['role'])) {
                if ($_SESSION['role'] == 'Professor') {
                    ?>
                    <button class="ui button" id="assignment-button">Add an assignment</button>
            <?php
                }
            } ?>

            <div class="ui divider"></div>
            <div class="ui grid centered">
                <table style="width:80%;" class="ui celled collapsing table">
                    <thead>
                        <tr>
                            <th class="center aligned two wide">Professor</th>
                            <th class="center aligned two wide">Course</th>
                            <th class="center aligned two wide">Assignment</th>
                            <th class="center aligned one wide">Link</th>
                            <th class="center aligned one wide">Due Date</th>
                            <?php if (isset($_SESSION['role'])) {
                                if ($_SESSION['role'] == 'Professor') {
                                    ?>
                                    <th class="center aligned one wide">Remove</th>
                            <?php
                                }
                            } ?>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $con = Database::open();
                        $sql = 'SELECT last_name, number, assignment_name, assignment_link, due_date, assignment.id
																FROM assignment JOIN course ON course_id = course.id
																JOIN person ON professor_id = person.id
                                ORDER BY due_date';
                        pg_prepare($con, "", $sql);
                        $rs = pg_execute($con, "", [])
                            or die("Query failed: " . pg_last_error());
                        pg_close($con);
                        while ($row = pg_fetch_row($rs)) {
                            ?>
                            <tr>
                                <td>
                                    <h4 class="ui image header">
                                        <div class="content">
                                            <?php echo $row[0] ?>
                                        </div>
                                    </h4>
                                </td>
                                <td>
                                    <?php echo $row[1] ?>
                                </td>
                                <td>
                                    <?php echo $row[2] ?>
                                </td>
                                <td>
                                    <a href="<?php echo $row[3] ?>"><?php echo $row[3] ?></a>

                                </td>
                                <td>
                                    <?php echo $row[4] ?>
                                </td>
                                <?php if (isset($_SESSION['role'])) {
                                        if ($_SESSION['role'] == 'Professor') {
                                            ?>
                                        <td>
                                            <?php
                                                        echo '<a class="ui button" href="../php_helpers/removeAssignment.php?assignmentId=' . $row[5] . '"><i class="ui trash icon"></i></a>';
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

            <div class="ui modal" id="assignment-modal">
                <i class="close icon"></i>
                <div class="description">
                    <form class="ui large form" id="assignment-form" action="assignments.php" method="post">
                        <div class="ui stacked segment">
                            <div class="field">
                                <div class="ui left icon input">
                                    <i class="user icon"></i>
                                    <input type="text" name="Name" placeholder="Name" id="name" value="<?php echo $_SESSION['user_id'] ?>" readonly="readonly" required>
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
                                        <!-- <div class="item" data-value="0">Female</div> -->
                                    </div>
                                </div>
                                <!-- <input type="text" name="Course" placeholder="Course" id="course" required> -->
                                <!-- </div> -->
                            </div>
                            <div class="field">
                                <input type="text" name="Assignment" placeholder="Assignment Name" id="assignment" required>
                            </div>
                            <div class="field">
                                <input type="text" name="AssignmentLink" placeholder="Assignment Link" id="assignmentLink" required>
                            </div>
                            <div class="field">
                                <input type="date" name="AssignmentDate" id="assignmentDate" required>
                                <div class="ui pointing label">
                                    Please enter a due date
                                </div>
                            </div>
                            <input class="ui fluid jmuPurple submit button" id="assignment-submit" type="submit" value="Submit" name="SubmitAssignment">
                            <div class="ui error message"></div>
                    </form>
                </div>
            </div>
</body>

</html>