
$(document).ready(function() {
    function toggleEmbarcador() {
        if ($('#fornecedor_id').val() == '2') {
            $('#div_embarcador').show();
        } else {
            $('#div_embarcador').hide();
            $('#embarcador_id').val('');
        }
    }

    $('#fornecedor_id').change(toggleEmbarcador);

    // Run on init in case of page reload or default selection
    toggleEmbarcador();
});
