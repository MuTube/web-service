/*                  */
/* MAIN SCRIPT FILE */
/*                  */

runMainFunctions();

function runMainFunctions() {
    var functions = loadMainFunctions();

    $(functions).each(function(index, action) {
        action();
    });
}

function loadMainFunctions() {
    var functions = [];

    //general
    var confirmLogout = function() {
        $('#logout').click(function() {
            if(confirm("You will exit your session. Continue ?")) {
                window.location.replace('/logout');
            }
        });
    };

    functions.push(confirmLogout);

    //table
    var removeButtonAction = function() {
        $('.table .actionRow .remove').each(function (index, rmButton) {
            $(rmButton).click(function() {
                var data = $(this).attr('data-id').split('_');
                if(confirm("You will delete this item... Continue ?")) {
                    window.location.replace('/'+data[0]+'/'+data[1]+'/remove');
                }
            });
        });
    };

    functions.push(removeButtonAction);

    //checkbox selector
    var initCheckboxSelector = function() {
        $('.checkbox-selector').closest('a').click(function() {
            $(this).find('.checkbox-selector').toggleClass('checked');
        });
        $('.checkbox-selector-all').click(function() {
            if($('.checkbox-selector.checked').length > 0) {
                $('.checkbox-selector').removeClass('checked');
            }
            else {
                $('.checkbox-selector').addClass('checked');
            }
        });
    };

    functions.push(initCheckboxSelector);

    return functions;
}