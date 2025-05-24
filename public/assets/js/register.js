$(document).ready(function() {
    $('#registerForm').submit(function(e) {
        e.preventDefault();
        $('#message').html('');

        $.ajax({
            type: "POST",
            url: "/?action=auth/register",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $('#message').html(`
                        <div class="alert alert-success">
                            ${response.message} Redirigiendo...
                        </div>
                    `);
                    setTimeout(() => {
                        window.location.href = "/?action=auth/login";
                    }, 1500);
                } else {
                    $('#message').html(`
                        <div class="alert alert-danger">
                            ${response.message}
                        </div>
                    `);
                }
            },
            error: function() {
                $('#message').html(`
                    <div class="alert alert-danger">
                        Error de conexión con el servidor.
                    </div>
                `);
            }
        });
    });
});