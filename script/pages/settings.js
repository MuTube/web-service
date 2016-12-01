initSettingsPage();

function initSettingsPage() {
    //table rows click
    $('table#setting_table tr td').each(function(index, row) {
        $(row).click(function() {
            if($(this).attr('data-id')) openSettingWindowInPanel($(this).attr('data-id'));
        });
    });

    //open selected row
    var selectedSetting = $('table#setting_table tr td.active').attr('data-id');
    openSettingWindowInPanel(selectedSetting);
}

function openSettingWindowInPanel(setting) {
    $.ajax({
        type: "POST",
        url : "/settings/getWindow",
        data : { 'setting' : setting },
        success : function(content) {
            $('#setting_window').empty();
            $('#setting_window').append($.parseHTML(content));
            $('#setting_window_title').text(setting.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();}));

            $('table#setting_table tr td').removeClass('active');
            $('table#setting_table tr td[data-id="'+setting+'"]').addClass('active');

            //if exist, run window init function
            if(typeof window[setting+'WindowInit'] == 'function') window[setting+'WindowInit']();

            //refresh select2
            $('.select2').select2();
        }
    });
}

// Window init functions
function permissionsWindowInit() {
    //confirm role deletion
    var removeButtons = $('a.role-remove');
    $(removeButtons).attr('href', '#');
    $(removeButtons).click(function() {
        if(confirm('You will delete this role. Continue ?')) {
            window.location.replace('/permissions/'+$(this).attr('data-id')+'/removeRole')
        }
    });
}