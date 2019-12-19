// brings up the log in modal
$("#login-button").click(function (e) {
    // e.preventDefault();
    $('#login-modal').modal('show');
    // $('#signupTab').attr('class', 'item');
    // $('#signupTabSegment').attr('class', 'ui bottom attached tab segment');
    // $('#loginTab').attr('class', 'item active');
    // $('#loginTabSegment').attr('class', 'ui bottom attached tab segment active');
});

// brings up the log in modal if the user clicks a log in link
$(".login-link").click(function (e) {
    // e.preventDefault();
    $(' #login-modal').modal('show');
});

$('.menu .item')
    .tab();