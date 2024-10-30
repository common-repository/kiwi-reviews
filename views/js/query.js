jQuery(document).ready(function ($) {

    $( "#query" ).change(function() {
        let query = $('#query').val();

        $.get('admin.php?page=google-reviews', {query: query}, function (data) {
            console.log(data)
        })
    });
});

