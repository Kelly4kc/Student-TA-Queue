function removeTACourse(ta_id, course_id) {
    $.ajax({
        async: false,
        type: "POST",
        url: "php_helpers/removeTACourse.php",
        data: { ta_id: ta_id, course_id: course_id },
        dataType: 'json',
        success: function() {
            location.reload()
        },
        error: console.error
    });
}

$('.label.ui.dropdown')
    .dropdown();

$('.no.label.ui.dropdown')
    .dropdown({
        useLabels: false
    });
/*
$('.ui.button').on('click', function () {
    $('.ui.dropdown')
        .dropdown('restore defaults')
});*/