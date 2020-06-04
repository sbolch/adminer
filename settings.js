$(document).ready(function() {
    $('#add-database').click(function(e) {
        e.preventDefault();
        $('#database-list').append($('#database-list').data('prototype').replace(/_ID_/g, Math.random().toString(36).substr(2, 12)));
    });

    $(document).on('click', '.remove-database', function(e) {
        e.preventDefault();
        $(this).closest('fieldset').remove();
    });
});
