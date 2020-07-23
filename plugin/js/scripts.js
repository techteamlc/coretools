$(function() {
    $('form').on('submit', function(e) {
        e.preventDefault();
        let form = $(this),
            url = $(form).attr('data-url'),
            data = $(form).serialize();

        // $(this).attr('data-url')
        // $(this).data('url')

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            beforeSend: function() {
                $('body').addClass('loading');
            }
        }).done(function(response) {
            if (response) {
                //$(form).find('button').addClass('success');
                //$('button', form).addClass('success');
                $('body').addClass('success');
            } else {
                $('body').addClass('error');
            }
        }).always(function() {
            $('body').removeClass('loading');
            setTimeout(function() {
                $('body').removeClass('success error');
            }, 2000);
        });
    });
});