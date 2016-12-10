initTrackListActions();

function initTrackListActions() {
    //link row to read page
    $('#track_table td.linked').each(function(index, elem) {
        var id = $(elem).closest('tr').attr('data-id');
        $(elem).click(function() {
            $(document).ready( function() {
                url = "/track/"+id+"/read";
                $( location ).attr("href", url);
            });
        });
    });

    //"remove selected" button action
    $('.remove_selected').click(function() {
        var ids = getSelectedTrackIds();
        if(ids.length > 0) {
            if(confirm("You will delete all these selected tracks... Continue ?")) window.location.replace('/track/'+ids.join('-')+'/remove')
        }
    });
}

function getSelectedTrackIds() {
    var rows = $('.checkbox-selector.checked').closest('tr'), ids = [];
    $(rows).each(function(index, row) {
        ids[index] = $(row).attr('data-id');
    });

    return ids;
}