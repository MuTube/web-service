initUserListActions();

function initUserListActions() {
    //link row to read page
    $('#user_table td.linked').each(function(index, elem) {
        var id = $(elem).closest('tr').attr('data-id');
        $(elem).click(function() {
            $(document).ready( function() {
                url = "/user/"+id+"/read";
                $( location ).attr("href", url);
            });
        });
    });

    //"remove selected" button action
    $('.remove_selected').click(function() {
        var ids = getSelectedUserIds();
        if(ids.length > 0) {
            if(confirm("You will delete all these selected users... Continue ?")) window.location.replace('/user/'+ids.join('-')+'/remove')
        }
    });
}

function getSelectedUserIds() {
    var rows = $('.checkbox-selector.checked').closest('tr'), ids = [];
    $(rows).each(function(index, row) {
        ids[index] = $(row).attr('data-id');
    });

    return ids;
}