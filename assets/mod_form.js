require(['jquery'], function($) {
    "use strict";

    var loadingbars, foldervideo;
    var timeSearchVideo = 0;
    var elements = $('#vidrofront-elements');

    $('#vidrofront-title-search')
        .val($("#id_identifier").val())
        .keyup(findVideo);

    loadVideos(1, 0, true);

    require(['core/url'], function(url) {
        loadingbars = url.imageUrl('icones/loading-bars', 'videofront');
        foldervideo = url.imageUrl('icones/folder-video', 'videofront');
    });

    /**
     * Load videos.
     *
     * @param {number} page           actual page
     * @param {number} folder         folder actual
     * @param {number} paginateoffset roll on top list
     */
    function loadVideos(page, folder, paginateoffset) {

        elements.html('<div style="text-align:center"><img height="80" src="' + loadingbars + '" ></div>');

        if (!paginateoffset) {
            $('html, body').animate({
                scrollTop:elements.offset().top - 100
            }, 800);
        }
        require(['core/ajax'], function(ajax) {
            ajax.call([{
                methodname:'mod_videofront_video_list', args:{
                    page:page, pasta:folder, titulo:$('#vidrofront-title-search').val()
                }, done   :processListCourse, fail:processListCourseError
            }]);
        });
    }

    /**
     * Process list course.
     *
     * @param {array} videos
     */
    function processListCourse(videos) {

        if (videos.status == "success") {
            var htmlNav;
            if (videos.pasta.PASTA_ID == 0) {
                htmlNav = '<li onclick="loadVideos(1, 0, false)">Pasta Raiz</li>';
            } else {
                htmlNav = '<li onclick="loadVideos(1, 0, false)">Pasta Raiz</li>' +
                    '<li onclick="loadVideos(1, ' + videos.pasta.PASTA_ID + ', false)">' + videos.pasta.PASTA_TITULO + '</li>';
            }
            $('#vidrofront-breadcrumb').html(htmlNav);

            if (videos.videos.length) {
                elements.html("");
                var iterate = $.each(videos.videos, function(key, video) {

                    var linkThumb = videos.thumburl.replace(videos.thumbreplace, video.VIDEO_IDENTIFIER);
                    var title = video.VIDEO_TITULO;
                    if (!title) {
                        title = video.VIDEO_FILENAME;
                    }

                    var html = "";
                    if (video.VIDEO_TIPO == "video") {
                        html = '<div class="list-itens-grid" id="video_identifier_' + video.VIDEO_IDENTIFIER + '">' +
                            '    <span class="itens" onclick="selectVideo(\'' + video.VIDEO_IDENTIFIER + '\', \'' + video.VIDEO_TITULO + '\')">' +
                            '        <img src="' + linkThumb + '" height="133" width="236"><br>' +
                            '        <span class="title">' + title + '</span>' +
                            '    </span>' +
                            '</div>';
                    } else {
                        var folderId = video.ITEM_ID.replace("p", "");
                        html = '<div class="list-itens-grid">' +
                            '    <span class="itens"  onclick="loadVideos(1, ' + folderId + ', false)">' +
                            '        <img src="' + foldervideo + '" height="133" width="236"><br>' +
                            '        <span class="title">' + video.VIDEO_TITULO + '</span>' +
                            '    </span>' +
                            '</div>';
                    }
                    elements.append(html);
                });

                $.when(iterate).done(function() {
                    createPaginator(videos.page, videos.numvideos, videos.perpage, videos.pasta.PASTA_ID);

                    var identifier = $('#id_identifier').val();
                    if (identifier.length > 4) {
                        $('#video_identifier_' + identifier).find('.itens').addClass("selected");
                    }
                });
            } else {
                elements.html('<div class="alert alert-info">Nenhum vídeo localizado</div>');
                createPaginator(videos.page, videos.numvideos, videos.perpage, videos.pasta.PASTA_ID);
            }
        } else {
            processListCourseError(videos.error);
        }
    }

    /**
     * Process list course error.
     *
     * @param {object} error message errror
     */
    function processListCourseError(error) {
        elements.html('<div class="alert alert-danger">' + error + '</div>');
        createPaginator(0, 0, 0, 0);
    }

    /**
     * Find video.
     */
    function findVideo() {
        clearInterval(timeSearchVideo);
        timeSearchVideo = setTimeout(function() {
            loadVideos(1, 0, true);
        }, 500);
    }

    /**
     * Create video paginator.
     *
     * @param {number} page      actual page
     * @param {number} numvideos num total videos
     * @param {number} perpage   num video per page
     * @param {number} folder    folder actual
     */
    function createPaginator(page, numvideos, perpage, folder) {
        var countpages = Math.floor(numvideos / perpage);
        var pagination = $('#vidrofront-pagination');

        if ((numvideos % perpage) != 0) {
            countpages += 1;
        }

        pagination.html('<span class="pagination-info">Página ' + page + ' de ' + countpages + '</span>');
        pagination.append('<ul class="pagination"></ul>');

        if (page != 1) {
            pagination.find('.pagination').append('<li class="clicked" onclick="loadVideos(1, ' + folder + ', false)"><span>«</span></li>');
        }
        var i = page - 4;
        if (i < 1) {
            i = 1;
        }

        if (i != 1) {
            pagination.find('.pagination').append('<li><span>...</span></li>');
        }

        var loop = 0;
        for (; i <= countpages; i++) {
            if (i == page) {
                pagination.find('.pagination').append('<li class="active"><span>' + i + '</span></li>');
            } else {
                pagination.find('.pagination').append('<li class="clicked" onclick="loadVideos(' + i + ', ' + folder + ', false)"><span>' + i + '</span></li>');
            }

            loop++;
            if (loop == 7) {
                if (i != countpages) {
                    pagination.find('.pagination').append('<li><span>...</span></li>');
                }
                break;
            }
        }
        if (page != countpages && countpages > 1) {
            pagination.find('.pagination').append('<li class="clicked" onclick="loadVideos(' + countpages + ', ' + folder + ', false)"><span>»</span></li>');
        }
    }

    /**
     * Funcion mark current video.
     *
     * @param {string} identifier video identifier
     * @param {string} title      video title
     */
    function selectVideo(identifier, title) {
        $("#id_identifier").val(identifier);
        if (title.length) {
            $("#id_name").val(title);
        }

        $('.list-itens-grid').find('.itens').removeClass("selected");
        $('#video_identifier_' + identifier).find('.itens').addClass("selected");
    }
});