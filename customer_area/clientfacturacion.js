$(function(){
    String.prototype.capitalize = function() {
        return this.replace(/(?:^|\s)\S/g, function(a) { return a.toUpperCase(); });
    };

    $('#fiscal-rfc').bind('keypress', function (event) {
        var keyCode = event.keyCode || event.which
        // Don't validate the input if below arrow, delete and backspace keys were pressed
        if (keyCode == 8 || (keyCode >= 35 && keyCode <= 40)) { // Left / Up / Right / Down Arrow, Backspace, Delete keys
        return;
        }

        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);

        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }
    });

    $(".input-upper").on('input', function(evt) {
        var input = $(this);
        var start = input[0].selectionStart;
        $(this).val(function(_, val){
            return val.toUpperCase();
        });
        input[0].selectionStart = input[0].selectionEnd = start;
    });


    $('#modalForm').unbind().on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var recipient = button.data('uid');
        var items = button.data('items');
        var modal = $(this);
        modal.find('.modal-title').text('Facturar pedido #' + recipient);
        $('#orderNum').val(recipient);

        $('#orderItems').val(JSON.stringify(items));
    });

    $('#fiscal-cp').change(function(){
        $('#cp-loading').fadeIn();

        var cp = $(this).val();
        var systemURL = $('#systemURL').val();
        var parameters = {
            'function' : 'getLocation',
            'cp' : cp
        };

        $.ajax({
            data:  parameters,
            url:   systemURL + 'modules/addons/facturacom/lib/apihandler.php',
            type:  'post',
            dataType: 'json',
            success:  function (response) {
                $('#cp-loading').fadeOut();
                console.log(response);
                /*
                if(response.status == 'success'){
                    console.log(response);
                    $('.f-edit').attr('disabled', false);
                }else{
                    $('.f-edit').attr('disabled', true);
                    resetModalForm(1);
                }
                */
            }
        });

    });

    $('#fiscal-rfc').change(function(){
        $('#rfc-loading').fadeIn();
        var rfc = $(this).val();
        var systemURL = $('#systemURL').val();
        var parameters = {
            'function' : 'getClient',
            'rfc' : rfc
        };

        $.ajax({
            data:  parameters,
            url:   systemURL + 'modules/addons/facturacom/lib/apihandler.php',
            type:  'post',
            dataType: 'json',
            success:  function (response) {
                $('#rfc-loading').fadeOut();
                console.log(response);
                if(response.status == 'success'){
                    var client = response.Data;
                    fillModalForm(client);
                    $('.f-edit').attr('disabled', false);
                }else{
                    $('.f-edit').attr('disabled', true);
                    resetModalForm(0);
                }

            }
        });

	});

    $('#fiscalDataForm').unbind().on('click', function(e){
        e.preventDefault();

        $(this).validate();

        if(!$(this).isValid()){
            return false;
        }

        $(this).attr('disabled', true);
        $(this).val('Procesando');
        var dataForm = $('#fiscalDataForm').serializeArray();
        var clientData = [];

        for (var i = 0; i < dataForm.length; i++) {
            var value = dataForm[i].value;
            clientData.push({value});
        }

        var systemURL     = $('#systemURL').val();
        var serieInvoices = $('#serieInvoices').val();
        var clientW       = $('#clientW').val();
        var orderNum      = $('#orderNum').val();

        var orderItems    = $('#orderItems').val();

        var parameters    = {
            'function'      : 'createInvoice',
            'clientData'    : clientData,
            'serieInvoices' : serieInvoices,
            'orderNum'      : orderNum,
            'orderItems'    : orderItems,
            'clientW'       : clientW,
        };

        $.ajax({
            data: parameters,
            url: systemURL + 'modules/addons/facturacom/lib/apihandler.php',
            type: 'post',
            dataType: 'json',
            success: function(response){
                if(response.status == 'success'){
                    $('#error_message').hide();
                    $('#error_message').html(response.message);

                }else{
                    $('#error_message').html(response.Error);
                    $('#error_message').show();
                }
                $(this).val('Facturar');
                $(this).attr('disabled', false);
                window.setTimeout(function(){location.reload()},3000);
            }
        });

    });

    function resetModalForm(location){
        var modalForm = $('#modalForm');

        if(location == 0){
            modalForm.find('#clientUID').val('');
            modalForm.find('#general-nombre').val('');
            modalForm.find('#general-apellidos').val('');
            modalForm.find('#general-email').val('');
            modalForm.find('#fiscal-telefono').val('');

            modalForm.find('#fiscal-nombre').val('');
            modalForm.find('#fiscal-calle').val('');
            modalForm.find('#fiscal-exterior').val('');
            modalForm.find('#fiscal-interior').val('');
        }

        modalForm.find('#fiscal-colonia').val('');
        modalForm.find('#fiscal-municipio').val('');
        modalForm.find('#fiscal-estado').val('');
        modalForm.find('#fiscal-cp').val('');
    }

    function fillModalForm(client){
        var modalForm = $('#modalForm');

        modalForm.find('#clientUID').val(client.UID);
        modalForm.find('#general-nombre').val(client.Contacto.Nombre);
        modalForm.find('#general-apellidos').val(client.Contacto.Apellidos);
        modalForm.find('#general-email').val(client.Contacto.Email);
        modalForm.find('#fiscal-telefono').val(client.Contacto.Telefono);

        modalForm.find('#fiscal-nombre').val(client.RazonSocial);
        modalForm.find('#fiscal-calle').val(client.Calle);
        modalForm.find('#fiscal-exterior').val(client.Numero);
        modalForm.find('#fiscal-interior').val(client.Interior);
        modalForm.find('#fiscal-colonia').val(client.Colonia);
        modalForm.find('#fiscal-municipio').val(client.Ciudad);
        modalForm.find('#fiscal-estado').val(client.Estado);
        modalForm.find('#fiscal-cp').val(client.CodigoPostal);
    }


});
