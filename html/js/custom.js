$( document ).ready(function() {
    var link = $('#viewEmails');

    link.on('click', function(e) {
        e.preventDefault();
        var id = link.data('id');
        $.ajax({
            type: "POST",
            url: '/view',
            data: {id: id},
            success: function(data) {
                $('#parseResult').html(data);
            },
            error: function(e) {
              console.log(e);
            },
        });
    });

});
