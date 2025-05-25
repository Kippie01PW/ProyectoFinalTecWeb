$(document).ready(function() {
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        $('#loginMessage').html('');

        $.ajax({
            type: "POST",
            url: "/ProyectoFinalTecWeb/public/api/auth/login",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect; 
                } else {
                    $('#loginMessage').html(`
                        <div class="alert alert-danger">
                            ${response.message}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                $('#loginMessage').html(`
                    <div class="alert alert-danger">
                        Error: ${xhr.status} - ${xhr.statusText}
                    </div>
                `);
            }
        });
    });
});