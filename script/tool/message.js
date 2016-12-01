/*                     */
/* MESSAGE SCRIPT FILE */
/*                     */

function displayAlertMessage(type, message) {
    if (type == 'error') type = 'danger';

    var $alertDiv = jQuery('<div/>', {'class': 'alert alert-' + type}),
        $closeLink = jQuery('<a/>', { 'href': '#', 'class': 'close', 'data-dismiss': 'alert', 'area-label': 'close', 'html': '×' }),
        $messageElem = jQuery('<span/>', {'class': 'alert-message', 'html': message});

    $alertDiv.append($closeLink);
    $alertDiv.append($messageElem);

    $('#messages').append($alertDiv);
}