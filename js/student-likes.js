//sends request to like a question
function like(id, curlikes) {
    $.ajax({
        type: "POST",
        url: "php_helpers/like.php",
        data: { id: id, likes: curlikes },
        dataType: 'json',
        success: function() {
            location.reload()
        },
        error: console.error
    });

}