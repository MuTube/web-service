if (matchMedia('(min-width: 993px)').matches) {
    initYoutubeAutoComplete('#youtube_search');
}

function initYoutubeAutoComplete(selector) {
    var $searchField = $(selector),
        searchField = document.querySelector(selector);

    if (!searchField) {
        return;
    }

    var awesomplete = new Awesomplete(searchField);

    // setup awesomplete
    awesomplete.filter = function () {
        return true;
    };
    awesomplete.sort = function () {
        return false;
    };

    window.addEventListener('awesomplete-select', awesompleteSelectEventFunction, false);

    // setup awesomplete keyup event
    $searchField.keyup(function (e) {
        var fieldValue = $searchField.val();

        if (fieldValue !== '' && ['ArrowDown', 'ArrowUp', 'Enter'].indexOf(e.key) == -1) {
            $.ajax({
                url: '/track/search/autocomplete',
                type: 'GET',
                data: {
                    searchTerm : encodeURIComponent(fieldValue)
                }
            }).success(function (data) {
                awesomplete.list = data;
            });
        }
    });
}

function awesompleteSelectEventFunction(e) {
    window.location.replace('/track/search?searchTerm=' + e.text);
}