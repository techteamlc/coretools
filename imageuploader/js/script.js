var slugify = function(text) {
    return text
        .toLowerCase()
        .replace(/ /g,'-')
        .replace(/[^\w-]+/g,'');
};

var getParameters = function(param) {
    let url = new URL(window.location.href);
    return url.searchParams.get(param);
};

const URL_SERVICE = 'https://coretools.tech/coretools/imageuploader/';
const THREADS = getParameters('threads') != null ? Number(getParameters('threads')) : 1;

let orderGroup   = 0,
    totalFiles   = 0,
    orderControl = [],
    orderFiles   = [],
    checkUpload  = 0,
    tenant;


var sendMedia = function(idx, config, pos, multiple = THREADS) {

    if (checkUpload == orderFiles[pos].length) {
        orderGroup++;
        checkUpload = 0;
        $.publish('/run/' + orderControl[orderGroup], { pos: orderControl[orderGroup] });
        return;
    }

    const fileReader = new FileReader();
    let file = orderFiles[pos][idx];

    if (file != undefined) {
        fileReader.readAsDataURL(file);
    }
    
    fileReader.onloadend = function() {
        let name = slugify(file.name);

        $.ajax({
            url: URL_SERVICE + 'sendupload.php',
            type: 'post',
            data: {
                tenant: tenant,
                user: localStorage.getItem('_imgup_u'),
                pass: localStorage.getItem('_imgup_p'),
                field: config.field,
                separator: config.separator,
                replace: config.replace,
                keep: config.keep,
                name: name,
                type: file.type,
                size: file.size,
                base64: fileReader.result
            },
            beforeSend: () => {
                $(`div.${name}`).addClass('loading');
            }
        }).done( (response) => {
            console.log('enviou a ' + name);
            checkUpload++;
            if (response.hasOwnProperty('APIresponseSuccess')) {
                if (response.APIresponseSuccess) {
                    $(`div.${name}`).addClass('success');
                } else {
                    $(`div.${name}`).addClass('error');
                    $('#callback').append(`<div>Erro na imagem ${response.FileName}</div>`);
                }
            } else {
                $('#callback').append(`<div>Erro na imagem ${name}</div>`);
            }
            sendMedia(idx + multiple, config, pos);
        }).fail( (error) => {
            $(`div.${name}`).addClass('error');
            $('#callback').append(`<div>Erro na imagem ${name}</div>`);
            sendMedia(idx + multiple, config, pos);
        }).always( () => {
            $(`div.${name}`).removeClass('loading');
            let processed = $('#photos > div.error,#photos > div.success').length;
            $('.box-photos .progress .from').text(processed);
            $('.box-photos .progress .bar span').css('width', (100 * processed)/totalFiles + '%');
        });
    };
    
};


$(function() {
    if (localStorage.getItem('_imgup_u') == null || localStorage.getItem('_imgup_p') == null) {
        $('body').addClass('no-auth');
    } else {
        
    }
    $('body').removeClass('loading');
    $('#upload').change(function() {
        $('#photos').empty();
        let files = $('#upload')[0].files;
        $.each(files, function(k, i) {
            let file = files[k];
            $('#photos').append(`<div class="${slugify(i.name)}"><span class="spinner"></span>${i.name}</div>`);
        });
    });

    $('#formUpload').on('submit', function(e) {
        e.preventDefault();

        /**
         * seta o tenant
         */
        tenant = $('[name="tenant"]').val();
        
        /**
         * reseta controles
         */
        orderFiles   = [];
        orderControl = [];
        orderGroup   = 0;
        checkUpload  = 0;

        $('#callback').empty();
        $('#photos > div').removeClass('success error');

        $('.box-photos .progress .from').text('1');
        $('.box-photos .progress .to').text($('#photos > div').length);
        $('.box-photos .progress').show();

        $('.box-photos .progress .bar span').css('width', '0%');
       
        var keep = $('[name="keep"]:checked').val(),
            replace = $('[name="replace"]:checked').val(),
            field = $('[name="field"]:checked').val();
            separator = $('[name="separator"]:checked').val();

        files = $('#upload')[0].files;
        totalFiles = files.length;

        /**
         * monta os grupos ordenados
         */
        $.each($('#upload')[0].files, (k,i) => {
            let parts = i.name.split(separator);
            let pos = Number(parts[parts.length - 1].split('.')[0]);
            if ($.inArray(pos, orderControl) == -1) {
                orderControl.push(Number(pos));
                orderFiles.push(pos);
                orderFiles[pos] = [];
            }
            orderFiles[pos].push(i);
        });

        /**
         * ordena os grupos
         */
        orderControl.sort(function(a, b){
            if(a < b) { return -1; }
            if(a > b) { return 1; }
            return 0;
        });        

        for (let x = 0; x < orderControl.length; x++) { // varre as raias
            let pos = orderControl[x];
            $.unsubscribe('/run/' + pos); // remove registro de eventos anteriores
            $.subscribe('/run/' + pos, function(e, args) { // registra evento para rodar grupo a grupo
                let limit = orderFiles[pos].length;
                if (limit > THREADS) {
                    limit = THREADS;
                }
                for (let z = 0; z < limit; z++) { // executa o limite de threads
                    sendMedia(z, {
                        keep,
                        replace,
                        field,
                        separator
                    },
                    args.pos);
                }
            });
        }
        
        /**
         * publica o primeiro evento - primeiro grupo
         */
        $.publish('/run/' + orderControl[orderGroup], { 
            pos: orderControl[orderGroup] 
        });
    });

    $('#formLogin').on('submit', function(e) {
        e.preventDefault();
        let data = $(this).serialize();
        $.ajax({
            url: URL_SERVICE + 'checkcredential.php',
            type: 'post',
            data: data,
            beforeSend: () => {
                $('#login').addClass('loading');
            }
        }).done( (response) => {
            if (response.response != null) {
                if (response.response.IsValid) {
                    localStorage.setItem('_imgup_u', $('#formLogin [name="login"]').val());
                    localStorage.setItem('_imgup_p', $('#formLogin [name="pass"]').val());
                    $('#login').fadeOut( () => {
                        $('body').removeClass('no-auth');
                    });
                }
            } else {
                $('#login').addClass('shake');
                $('#login .callback').html('Dados inválidos!');
                setTimeout( () => {
                    $('#login').removeClass('shake');
                }, 300);
                setTimeout( () => {
                    $('#login .callback').empty();
                }, 3000);
            }
        }).fail( (error) => {
            console.log('error', error);
        }).always( () => {
            $('#login').removeClass('loading');
        });
    });
});