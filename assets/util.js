window.onload = function() {
    var videoBoxWidth = 0;

    var videoBox = document.getElementById('videofront-workaround');
    if (videoBox.offsetWidth) {
        videoBoxWidth = videoBox.offsetWidth;
    } else if (videoBox.clientWidth) {
        videoBoxWidth = videoBox.clientWidth;
    }

    var videofrontPlayer = document.getElementById('videofront-player');
    if (videofrontPlayer) {
        var videoBoxHeight = videoBoxWidth * 3 / 4;

        videofrontPlayer.style.width = videoBoxWidth + "px";
        videofrontPlayer.style.height = videoBoxHeight + "px";
    }
};
