$('#student-queue-button').click(function (e) {
    // e.preventDefault();
    $('#student-queue-modal').modal('show');
});

function showFollowUpModal(questionId, assignment, course) {
    $('#student-follow-up-modal').modal('show');
    $('#question_id').attr('value', questionId);
    $('#follow_up_assignment').attr('value', 'Assignment: ' + assignment);
    $('#follow_up_course').attr('value', 'Course: ' + course);
}

function showAnswerModal(questionId, assignment, course) {
    $('#ta-answer-modal').modal('show');
    $('#ta_answer_question_id').attr('value', questionId);
    $('#ta_answer_assignment').attr('value', 'Assignment: ' + assignment);
    $('#ta_answer_course').attr('value', 'Course: ' + course);
}

$('.ui.selection.dropdown').dropdown();