initUserProfilePage();

function initUserProfilePage() {
    $('.permissions-view-all').click(function() {
        $('.block-all-permissions').toggleClass('hidden');
    });
}