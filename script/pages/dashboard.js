initContactBlock();

function initContactBlock() {
    //link row to read page
    $('#contact_dashboard_table td.linked').each(function(index, elem) {
        var id = $(elem).closest('tr').attr('data-id');
        $(elem).click(function() {
            $(document).ready( function() {
                url = "/contact/"+id+"/read";
                $( location ).attr("href", url);
            });
        });
    });
}