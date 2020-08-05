var slugify = function(text) {
    return text
        .toLowerCase()
        .replace(/ /g,'-')
        .replace(/[^\w-]+/g,'');
};

var sendMedia = function(idx, config) {

    if (idx >= files.length) {
        return;
    }

    const fileReader = new FileReader();
    let file = files[idx];

    fileReader.readAsDataURL(file);
    
    fileReader.onloadend = function() {
        let name = slugify(file.name);

        $.ajax({
            url: './sendupload.php',
            type: 'post',
            data: {
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
        }).fail( (error) => {
            $(`div.${name}`).addClass('error');
            $('#callback').append(`<div>Erro na imagem ${name}</div>`);
        }).always( () => {
            $(`div.${name}`).removeClass('loading');
            let processed = $('#photos > div.error,#photos > div.success').length;
            $('.box-photos .progress .from').text(processed);
            $('.box-photos .progress .bar span').css('width', (100 * processed)/totalFiles + '%');

            sendMedia(idx + 1, config);
        });
    }
    
};

let totalFiles = 0,
    files;

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
        //$('.box-photos .total em').text(files.length);
        //$('.box-photos .total').show();
    });

    $('#formUpload').on('submit', function(e) {
        e.preventDefault();
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

        //$.each(files, function(k, i) {

            sendMedia(0, {
                keep,
                replace,
                field,
                separator
            });
            
        //});
    });

    $('#formLogin').on('submit', function(e) {
        e.preventDefault();
        let data = $(this).serialize();
        $.ajax({
            url: './checkcredential.php',
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