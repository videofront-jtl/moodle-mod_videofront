jQuery(document).ready(function() {
    "use strict";

    jQuery('#fitem_id_showdescription').after('<div id="vidrofront-load"></div>');
    jQuery('#vidrofront-load')
        .append('<ul id="vidrofront-breadcrumb" ><li onclick="load_videos(1,0, false)">Pasta Raiz</li></ul>')
        .append('<div id="vidrofront-search" ><input type="text" id="vidrofront-titulo-search" placeholder="Buscar vídeos"></div>')
        .append('<div id="vidrofront-elementos"></div>')
        .append('<div id="vidrofront-pagination"></div>');

    jQuery('#vidrofront-titulo-search')
        .val(jQuery("#id_identifier").val())
        .keyup(find_video);

    load_videos(1, 0, true);

    require(['core/url'], function(url) {
        loadingbars = url.imageUrl('icones/loading-bars', 'videofront');
        foldervideo = url.imageUrl('icones/folder-video', 'videofront');
    });
});

var timeSearchVideo = 0;
var loadingbars, foldervideo;

function load_videos(page, pasta, paginate_offset) {
    var paginate = jQuery('#vidrofront-elementos');
    paginate.html('<div style="text-align:center"><img height="80" src="' + loadingbars + '" ></div>');

    if (!paginate_offset) {
        jQuery('html, body').animate({
            scrollTop : paginate.offset().top - 100
        }, 800);
    }
    require(['core/ajax'], function(ajax) {
        ajax.call([{
            methodname : 'mod_videofront_video_list', args : {
                page : page, pasta : pasta, titulo : jQuery('#vidrofront-titulo-search').val()
            }, done : process_list_course, fail : process_list_course_error
        }]);
    });
}

function process_list_course(videos) {

    var paginate = jQuery('#vidrofront-elementos');

    if (videos.status == "success") {
        if (videos.pasta.PASTA_ID == 0) {
            htmlNav = '<li onclick="load_videos(1, 0, false)">Pasta Raiz</li>';
        } else {
            htmlNav = '<li onclick="load_videos(1, 0, false)">Pasta Raiz</li>' + '<li onclick="load_videos(1, ' + videos.pasta.PASTA_ID + ', false)">' + videos.pasta.PASTA_TITULO + '</li>';
        }
        jQuery('#vidrofront-breadcrumb').html(htmlNav);

        if (videos.videos.length) {
            paginate.html("");
            var iterate = jQuery.each(videos.videos, function(key, video) {

                var linkThumb = videos.thumburl.replace(videos.thumbreplace, video.VIDEO_IDENTIFIER);
                var titulo = video.VIDEO_TITULO;
                if (!titulo) {
                    titulo = video.VIDEO_FILENAME;
                }

                var html = "";
                if (video.VIDEO_TIPO == "video") {
                    html = '<div class="lista-itens-grid" id="video_identifier_' + video.VIDEO_IDENTIFIER + '">' + '    <span class="itens" onclick="select_video(\'' + video.VIDEO_IDENTIFIER + '\', \'' + video.VIDEO_TITULO + '\')">' + '        <img src="' + linkThumb + '" height="133" width="236"><br>' + '        <span class="titulo">' + titulo + '</span>' + '    </span>' + '</div>';
                } else {
                    var pastaId = video.ITEM_ID.replace("p", "");
                    html = '<div class="lista-itens-grid">' + '    <span class="itens"  onclick="load_videos(1, ' + pastaId + ', false)">' + '        <img src="' + foldervideo + '" height="133" width="236"><br>' + '        <span class="titulo">' + video.VIDEO_TITULO + '</span>' + '    </span>' + '</div>';
                }
                paginate.append(html);
            });

            jQuery.when(iterate).done(function() {
                create_paginator(videos.page, videos.numvideos, videos.perpage, videos.pasta.PASTA_ID);

                var identifier = jQuery('#id_identifier').val();
                if (identifier.length > 4) {
                    jQuery('#video_identifier_' + identifier).find('.itens').addClass("selected");
                }
            });
        } else {
            paginate.html('<div class="alert alert-info">Nenhum vídeo localizado</div>');
            create_paginator(videos.page, videos.numvideos, videos.perpage, videos.pasta.PASTA_ID);
        }
    } else {
        process_list_course_error(videos.error);
    }
}

function process_list_course_error(error) {
    paginate.html('<div class="alert alert-danger">' + error + '</div>');
    create_paginator(0, 0, 0, 0);
}

/**
 * Find video.
 */
function find_video() {
    clearInterval(timeSearchVideo);
    timeSearchVideo = setTimeout(function() {
        load_videos(1, 0, true);
    }, 500);
}

/**
 * Create video paginator.
 *
 * @param page
 * @param numvideos
 * @param perpage
 * @param pasta
 */
function create_paginator(page, numvideos, perpage, pasta) {
    var countpages = Math.floor(numvideos / perpage);
    var pagination = jQuery('#vidrofront-pagination');

    if ((numvideos % perpage) != 0) {
        countpages += 1;
    }

    pagination.html('<span class="pagination-info">Página ' + page + ' de ' + countpages + '</span>');
    pagination.append('<ul class="pagination"></ul>');

    if (page != 1) {
        pagination.find('.pagination').append('<li class="clicked" onclick="load_videos(1, ' + pasta + ', false)"><span>«</span></li>');
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
            pagination.find('.pagination').append('<li class="clicked" onclick="load_videos(' + i + ', ' + pasta + ', false)"><span>' + i + '</span></li>');
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
        pagination.find('.pagination').append('<li class="clicked" onclick="load_videos(' + countpages + ', ' + pasta + ', false)"><span>»</span></li>');
    }
}

/**
 * Funcion mark current video.
 *
 * @param identifier
 * @param titulo
 */
function select_video(identifier, titulo) {
    jQuery("#id_identifier").val(identifier);
    if (titulo.length) {
        jQuery("#id_name").val(titulo);
    }

    jQuery('.lista-itens-grid').find('.itens').removeClass("selected");
    jQuery('#video_identifier_' + identifier).find('.itens').addClass("selected");
}
