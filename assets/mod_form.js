jQuery(document).ready(function () {
    "use strict";

    jQuery('#vidrofront-title-search')
        .val(jQuery("#id_identifier").val())
        .keyup(find_video);

    elements = jQuery('#vidrofront-elements');

    load_videos(1, 0, true);

    require(['core/url'], function (url) {
        loadingbars = url.imageUrl('icones/loading-bars', 'videofront');
        foldervideo = url.imageUrl('icones/folder-video', 'videofront');
    });
});

var timeSearchVideo = 0;
var loadingbars, foldervideo, elements;

function load_videos(page, folder, paginate_offset) {

    elements.html('<div style="text-align:center"><img height="80" src="' + loadingbars + '" ></div>');

    if (!paginate_offset) {
        jQuery('html, body').animate({
            scrollTop : elements.offset().top - 100
        }, 800);
    }
    require(['core/ajax'], function (ajax) {
        ajax.call([{
            methodname : 'mod_videofront_video_list', args : {
                page : page, pasta : folder, titulo : jQuery('#vidrofront-title-search').val()
            }, done    : process_list_course, fail : process_list_course_error
        }]);
    });
}

function process_list_course(videos) {

    if (videos.status == "success") {
        if (videos.pasta.PASTA_ID == 0) {
            htmlNav = '<li onclick="load_videos(1, 0, false)">Pasta Raiz</li>';
        } else {
            htmlNav = '<li onclick="load_videos(1, 0, false)">Pasta Raiz</li>' +
                '<li onclick="load_videos(1, ' + videos.pasta.PASTA_ID + ', false)">' + videos.pasta.PASTA_TITULO + '</li>';
        }
        jQuery('#vidrofront-breadcrumb').html(htmlNav);

        if (videos.videos.length) {
            elements.html("");
            var iterate = jQuery.each(videos.videos, function (key, video) {

                var linkThumb = videos.thumburl.replace(videos.thumbreplace, video.VIDEO_IDENTIFIER);
                var title = video.VIDEO_TITULO;
                if (!title) {
                    title = video.VIDEO_FILENAME;
                }

                var html = "";
                if (video.VIDEO_TIPO == "video") {
                    html = '<div class="list-itens-grid" id="video_identifier_' + video.VIDEO_IDENTIFIER + '">' +
                        '    <span class="itens" onclick="select_video(\'' + video.VIDEO_IDENTIFIER + '\', \'' + video.VIDEO_TITULO + '\')">' +
                        '        <img src="' + linkThumb + '" height="133" width="236"><br>' +
                        '        <span class="title">' + title + '</span>' +
                        '    </span>' +
                        '</div>';
                } else {
                    var folderId = video.ITEM_ID.replace("p", "");
                    html = '<div class="list-itens-grid">' +
                        '    <span class="itens"  onclick="load_videos(1, ' + folderId + ', false)">' +
                        '        <img src="' + foldervideo + '" height="133" width="236"><br>' +
                        '        <span class="title">' + video.VIDEO_TITULO + '</span>' +
                        '    </span>' +
                        '</div>';
                }
                elements.append(html);
            });

            jQuery.when(iterate).done(function () {
                create_paginator(videos.page, videos.numvideos, videos.perpage, videos.pasta.PASTA_ID);

                var identifier = jQuery('#id_identifier').val();
                if (identifier.length > 4) {
                    jQuery('#video_identifier_' + identifier).find('.itens').addClass("selected");
                }
            });
        } else {
            elements.html('<div class="alert alert-info">Nenhum vídeo localizado</div>');
            create_paginator(videos.page, videos.numvideos, videos.perpage, videos.pasta.PASTA_ID);
        }
    } else {
        process_list_course_error(videos.error);
    }
}

function process_list_course_error(error) {
    elements.html('<div class="alert alert-danger">' + error + '</div>');
    create_paginator(0, 0, 0, 0);
}

/**
 * Find video.
 */
function find_video() {
    clearInterval(timeSearchVideo);
    timeSearchVideo = setTimeout(function () {
        load_videos(1, 0, true);
    }, 500);
}

/**
 * Create video paginator.
 *
 * @param page
 * @param numvideos
 * @param perpage
 * @param folder
 */
function create_paginator(page, numvideos, perpage, folder) {
    var countpages = Math.floor(numvideos / perpage);
    var pagination = jQuery('#vidrofront-pagination');

    if ((numvideos % perpage) != 0) {
        countpages += 1;
    }

    pagination.html('<span class="pagination-info">Página ' + page + ' de ' + countpages + '</span>');
    pagination.append('<ul class="pagination"></ul>');

    if (page != 1) {
        pagination.find('.pagination').append('<li class="clicked" onclick="load_videos(1, ' + folder + ', false)"><span>«</span></li>');
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
            pagination.find('.pagination').append('<li class="clicked" onclick="load_videos(' + i + ', ' + folder + ', false)"><span>' + i + '</span></li>');
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
        pagination.find('.pagination').append('<li class="clicked" onclick="load_videos(' + countpages + ', ' + folder + ', false)"><span>»</span></li>');
    }
}

/**
 * Funcion mark current video.
 *
 * @param identifier
 * @param title
 */
function select_video(identifier, title) {
    jQuery("#id_identifier").val(identifier);
    if (title.length) {
        jQuery("#id_name").val(title);
    }

    jQuery('.list-itens-grid').find('.itens').removeClass("selected");
    jQuery('#video_identifier_' + identifier).find('.itens').addClass("selected");
}
