initUserProfilePage();

function initUserProfilePage() {
    console.log("salut");
    $('#permissions-view-all').click(function() {
        alert("salut");
        $('#block-all-permissions').toggleClass('hidden-xs-up');
    });
}