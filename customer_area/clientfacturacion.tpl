<link rel="stylesheet" href="//cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css" media="screen" title="no title" charset="utf-8">
<link rel="stylesheet" href="templates/six/customer_area/clientfacturacion.css" media="screen" title="no title" charset="utf-8">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.2.8/jquery.form-validator.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="templates/six/customer_area/clientfacturacion.js"></script>
<h3>Pedidos sin facturar</h3>
<input type="hidden" id="systemURL" value="{$systemURL}">
<input type="hidden" id="serieInvoices" value="{$serieInvoices}">
<input type="hidden" id="clientW" value="{$clientW}">
<div class="table-container clearfix">
    <table id="tableClientToInvoicesList" class="table table-list">
        <thead>
            <tr>
                <th>No. Factura</th>
                <th>Fecha de factura</th>
                <th>Fecha de pago</th>
                <th>Monto total</th>
                <th>Estatus</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            {foreach key=num item=order from=$whmcsInvoices}
                <tr>
                    <td>
                        <a href="clientarea.php?action=productdetails&id={$order.orderId}">{$order.orderId}</a>
                    </td>
                    <td>{$order.orderDate}</td>
                    <td>{$order.invoiceDatePaid}</td>
                    <td>${$order.total}</td>
                    <td><span class="label status status-{if $order.status eq 'Pagada'}paid{else}unpaid{/if}">{$order.status}</span></td>
                    <td>
                        {if $order.open eq true and $order.sent eq false}
                        <a href="#" class="btn btn-success btn-modal-form"
                                data-uid="{$order.orderId}"
                                data-items='{$order.orderdata}'
                                data-toggle="modal" data-target="#modalForm">
                            <span class="glyphicon glyphicon-list-alt"></span>
                            Facturar
                        </a>
                        {else}
                            &nbsp;
                        {/if}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>

<h3>Hitorial de facturas</h3>
<div class="table-container clearfix">
    <table id="tableClientInvoicesList" class="table table-list">
        <thead>
            <tr>
                <th>Folio</th><!-- {$LANG.invoicestitle} -->
                <th>Fecha de creación</th>
                <th>Receptor</th>
                <th>Núm. Pedido</th>
                <th>Monto total</th>
                <th>Estatus</th>
                <th>PDF</th>
                <th>XML</th>
            </tr>
        </thead>
        <tbody>
            {foreach key=num item=invoice from=$clientInvoices}
                <tr>
                    <td>{$invoice.Folio}</td>
                    <td><span class="hidden">{$invoice.FechaTimbrado}</span>{$invoice.FechaTimbrado}</td>
                    <td>{$invoice.Receptor}</td>
                    <td><a href="clientarea.php?action=productdetails&id={$invoice.NumOrder}">{$invoice.NumOrder}</a></td>
                    <td>${$invoice.Total}</td>
                    <td><span class="label status status-{if $invoice.Status eq 'enviada'}paid{else}cancelled{/if}">{$invoice.Status}</span></td>
                    <td class="responsive-edit-button">
                        <a href="https://factura.com/api/publica/invoice/{$invoice.UID}/pdf" class="btn-send-email btn btn-info" data-uid="{$invoice.UID}">
                            <span class="glyphicon glyphicon-file"></span>
                            Descargar PDF
                        </a>
                    </td>
                    <td class="responsive-edit-button">
                        <a href="https://factura.com/api/publica/invoice/{$invoice.UID}/xml" class="btn-send-email btn btn-info" data-uid="{$invoice.UID}">
                            <span class="glyphicon glyphicon-file"></span>
                            Descargar XML
                        </a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>

<!-- Factura.com modal form -->
<div class="modal fade" id="modalForm">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Facturar el pedido #<span id="orderNum"></span></h4>
      </div>
      <form id="fiscalDataForm">
          <input type="hidden" id="clientUID" name="clientUID" value="">
          <input type="hidden" id="orderNum" name="orderNum" value="">
          <div class="modal-body step-block">
              <div class="step-content">
                  <div class="messages">
                      <h4 id="error_message" style="margin-bottom:30px;color: red;display:none;"></h4>
                  </div>
                  <input type="hidden" name="csrf" value="">
                  <input type="hidden" id="apimethod" name="apimethod" value="create">
                  <div class="input-group">
                      <label for="fiscal-rfc">RFC</label>
                      <input type="text" class="input-upper f-input f-top" id="fiscal-rfc" name="fiscal-rfc" value="" placeholder="12 o 13 dígitos" data-validation="length alphanumeric required" data-validation-length="12-13" data-validation-error-msg="Por favor ingrese un RFC válido" >
                      <span id="rfc-loading"><em>buscando cliente...</em></span>
                  </div>
                  <h3>Datos de contacto</h3>
                  <div class="input-group">
                      <label for="general-nombre">Nombre</label>
                      <input type="text" class="input-cap f-input f-top" id="general-nombre" name="general-nombre" value="" placeholder="" data-validation="required" data-validation-error-msg="Por favor ingrese un nombre válido" >
                  </div>
                  <div class="input-group">
                        <label for="general-apellidos">Apellidos</label>
                        <input type="text" class="input-cap f-input f-top" id="general-apellidos" name="general-apellidos" value="" placeholder="" data-validation="required" data-validation-error-msg="Por favor ingrese un valor válido" >
                    </div>
                    <div class="input-group">
                        <label for="general-email">Correo electrónico</label>
                        <input type="email" class="f-input f-top" id="general-email" name="general-email" value="" placeholder="Email para envío de CFDI" data-validation="email required" data-validation-error-msg="Por favor ingrese un email válido" >
                    </div>
                    <div class="input-group">
                        <label for="fiscal-telefono">Teléfono</label>
                        <input type="text" class="input-cap f-input f-no-top f-right f-bottom" id="fiscal-telefono" name="fiscal-telefono" value="" placeholder="10 digitos" data-validation="length number" data-validation-length="10" data-validation-error-msg="Por favor ingrese un teléfono válido" >
                    </div>
                    <h3>Datos fiscales</h3>
                    <div class="input-group">
                        <label for="fiscal-nombre">Razón Social</label>
                        <input type="text" class="input-cap f-input f-top" id="fiscal-nombre" name="fiscal-nombre" value="" placeholder="" data-validation="required" data-validation-error-msg="Por favor ingrese un valor válido" >
                    </div>
                    <div class="input-group">
                        <label for="fiscal-calle">Calle</label>
                        <input type="text" class="input-cap f-input f-no-top" id="fiscal-calle" name="fiscal-calle" value="" placeholder="" data-validation="required" data-validation-error-msg="Por favor ingrese un valor válido" >
                    </div>
                    <div class="input-group float-left">
                        <label for="fiscal-exterior">Número exterior</label>
                        <input type="text" class="input-cap f-input f-right f-no-top" id="fiscal-exterior" name="fiscal-exterior" value="" placeholder="" data-validation="required" data-validation-error-msg="Por favor ingrese un valor válido" >
                    </div>
                    <div class="input-group float-left" style="float: right;">
                        <label for="fiscal-interior">Número interior</label>
                        <input type="text" class="input-cap f-input" id="fiscal-interior" name="fiscal-interior" value="" placeholder="" >
                    </div>
                    <div class="input-group">
                        <label for="fiscal-cp">Código Postal</label>
                        <input type="text" class="input-cap f-input f-no-top f-bottom" id="fiscal-cp" name="fiscal-cp" value="" placeholder="" data-validation="required" data-validation-error-msg="Por favor ingrese un valor válido" >
                        <span id="cp-loading"><em>buscando datos...</em></span>
                    </div>
                    <div class="input-group">
                        <label for="fiscal-colonia">Colonia</label>
                        <input type="text" class="input-cap f-input f-right" id="fiscal-colonia" name="fiscal-colonia" value="" placeholder="" data-validation="required" data-validation-error-msg="Por favor ingrese un valor válido" >
                    </div>
                    <div class="input-group">
                        <label for="fiscal-municipio">Delegación o Municipio</label>
                        <input type="text" class="input-cap f-input f-no-top f-right" id="fiscal-municipio" name="fiscal-municipio" value="" placeholder="" data-validation="required" data-validation-error-msg="Por favor ingrese un valor válido">
                    </div>
                    <div class="input-group">
                        <label for="fiscal-estado">Estado</label>
                        <input type="text" class="input-cap f-input" id="fiscal-estado" name="fiscal-estado" value="" placeholder="" data-validation="required" data-validation-error-msg="Por favor ingrese un valor válido">
                    </div>
                    <div class="input-group">
                        <label for="fiscal-pais">País</label>
                        <input type="text" class="input-cap f-input f-right" id="fiscal-pais" name="fiscal-pais" value="México" placeholder="" data-validation="required" data-validation-error-msg="Por favor ingrese un valor válido">
                    </div>
                </div>
          </div>
          <div class="modal-footer">
              <div class="buttons-right">
                  <input type="button" class="btn btn-danger f-submit f-back" id="step-two-button-back" name="f-back" data-dismiss="modal" value="Cancelar" data-f="2">
                  <input type="submit" class="btn btn-success f-submit" id="invoice-button-create" name="f-submit" value="Facturar">
                  <input type="hidden" id="orderItems" name="orderItems" value="">
              </div>
              <div class="clearfix"></div>
              <div class="f-loading" style="display: none;">Cargando...</div>
              <div class="error_msj"></div>
              <div class="clearfix"></div>
          </div>
    </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script> $.validate(); </script>
