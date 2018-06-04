$(document).ready(function () {
    "use strict";

    $('#fitem_id_showdescription').after('<div id="vidrofront-load"></div>');
    $('#vidrofront-load')
        .append('<ul id="vidrofront-breadcrumb" ><li onclick="loadVideos(1,0, false)">Pasta Raiz</li></ul>')
        .append('<div id="vidrofront-search" ><input type="text" id="vidrofront-titulo-search" placeholder="Buscar vídeos"></div>')
        .append('<div id="vidrofront-elementos"></div>')
        .append('<div id="vidrofront-pagination"></div>');

    $('#vidrofront-titulo-search')
        .val($("#id_identifier").val())
        .keyup(findVideo);

    loadVideos(1, 0, true);

    require(['core/url'], function (url) {
        loadingbars = url.imageUrl('icones/loading-bars', 'videofront');
        foldervideo = url.imageUrl('icones/folder-video', 'videofront');
    });
});

var timeSearchVideo = 0;
var loadingbars, foldervideo;

function loadVideos(page, pasta, paginate_offset) {
    var paginate = $('#vidrofront-elementos');
    paginate.html('<div style="text-align:center"><img height="80" src="' + loadingbars + '" ></div>');

    if (!paginate_offset) {
        $('html, body').animate({
            scrollTop : paginate.offset().top - 100
        }, 800);
    }
    require(['core/ajax'], function (ajax) {
        ajax.call([{
            methodname : 'mod_videofront_video_list',
            args       : {
                page   : page,
                pasta  : pasta,
                titulo : $('#vidrofront-titulo-search').val()
            },
            done       : processoListaCurso,
            fail       : processoListaCursoError
        }]);
    });
}

function processoListaCurso(videos) {

    var paginate = $('#vidrofront-elementos');

    if (videos.status == "success") {
        if (videos.pasta.PASTA_ID == 0) {
            htmlNav =
                '<li onclick="loadVideos(1, 0, false)">Pasta Raiz</li>';
        } else {
            htmlNav =
                '<li onclick="loadVideos(1, 0, false)">Pasta Raiz</li>' +
                '<li onclick="loadVideos(1, ' + videos.pasta.PASTA_ID + ', false)">' + videos.pasta.PASTA_TITULO + '</li>';
        }
        $('#vidrofront-breadcrumb').html(htmlNav);

        if (videos.videos.length) {
            paginate.html("");
            var iterate = $.each(videos.videos, function (key, video) {

                var linkThumb = videos.thumburl.replace(videos.thumbreplace, video.VIDEO_IDENTIFIER);
                var titulo = video.VIDEO_TITULO;
                if (!titulo) {
                    titulo = video.VIDEO_FILENAME;
                }

                var html = "";
                if (video.VIDEO_TIPO == "video") {
                    html =
                        '<div class="lista-itens-grid" id="video_identifier_' + video.VIDEO_IDENTIFIER + '">' +
                        '    <span class="itens" onclick="selectVideo(\'' + video.VIDEO_IDENTIFIER + '\', \'' + video.VIDEO_TITULO + '\')">' +
                        '        <img src="' + linkThumb + '" height="133" width="236"><br>' +
                        '        <span class="titulo">' + titulo + '</span>' +
                        '    </span>' +
                        '</div>';
                } else {
                    var pastaId = video.ITEM_ID.replace("p", "");
                    html =
                        '<div class="lista-itens-grid">' +
                        '    <span class="itens"  onclick="loadVideos(1, ' + pastaId + ', false)">' +
                        '        <img src="' + foldervideo + '" height="133" width="236"><br>' +
                        '        <span class="titulo">' + video.VIDEO_TITULO + '</span>' +
                        '    </span>' +
                        '</div>';
                }
                paginate.append(html);
            });

            $.when(iterate).done(function () {
                createPaginator(videos.page, videos.numvideos, videos.perpage, videos.pasta.PASTA_ID);

                var identifier = $('#id_identifier').val();
                if (identifier.length > 4) {
                    $('#video_identifier_' + identifier).find('.itens').addClass("selected");
                }
            });
        } else {
            paginate.html('<div class="alert alert-info">Nenhum vídeo localizado</div>');
            createPaginator(videos.page, videos.numvideos, videos.perpage, videos.pasta.PASTA_ID);
        }
    } else {
        processoListaCursoError(videos.error);
    }
}

function processoListaCursoError(error) {
    paginate.html('<div class="alert alert-danger">' + error + '</div>');
    createPaginator(0, 0, 0, 0);
}

function findVideo() {
    clearInterval(timeSearchVideo);
    timeSearchVideo = setTimeout(function () {
        loadVideos(1, 0, true);
    }, 500);
}

function createPaginator(page, numvideos, perpage, pasta) {
    var countpages = Math.floor(numvideos / perpage);
    var pagination = $('#vidrofront-pagination');

    if ((numvideos % perpage) != 0) {
        countpages += 1;
    }

    pagination.html('<span class="pagination-info">Página ' + page + ' de ' + countpages + '</span>');
    pagination.append('<ul class="pagination"></ul>');

    if (page != 1) {
        pagination.find('.pagination').append('<li class="clicked" onclick="loadVideos(1, ' + pasta + ', false)"><span>«</span></li>');
    }
    i = page - 4;
    if (i < 1) {
        i = 1;
    }

    if (i != 1) {
        pagination.find('.pagination').append('<li><span>...</span></li>');
    }

    loop = 0;
    for (; i <= countpages; i++) {
        if (i == page) {
            pagination.find('.pagination').append('<li class="active"><span>' + i + '</span></li>');
        } else {
            pagination.find('.pagination').append('<li class="clicked" onclick="loadVideos(' + i + ', ' + pasta + ', false)"><span>' + i + '</span></li>');
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
        pagination.find('.pagination').append('<li class="clicked" onclick="loadVideos(' + countpages + ', ' + pasta + ', false)"><span>»</span></li>');
    }
}

function selectVideo(identifier, titulo) {
    $("#id_identifier").val(identifier);
    if (titulo.length) {
        $("#id_name").val(titulo);
    }

    $('.lista-itens-grid').find('.itens').removeClass("selected");
    $('#video_identifier_' + identifier).find('.itens').addClass("selected");
}
