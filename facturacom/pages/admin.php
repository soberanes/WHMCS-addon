<?php
    $invoices = WrapperHelper::getInvoices();
    $index = 0;
    $configEntity = WrapperConfig::configEntity();
    $systemURL = $configEntity['systemURL'];
?>
<style media="screen">
    .label-danger {
        background-color: #d9534f;
    }
    .label-success {
        background-color: #5cb85c;
    }
</style>
<script src="<?php echo $systemURL ?>/modules/addons/facturacom/pages/js/functions.js" type="text/javascript"></script>
<input type="hidden" id="systemURL" value="<?php echo $systemURL ?>" />
<p class="text-msg">
    <?php echo count($invoices->data) ?> facturas en sistema.
</p>
<div class="tablebg">
    <input type="hidden" id="kval" value="<?php echo $config["key"] ?>">
    <input type="hidden" id="sval" value="<?php echo $config["secret"] ?>">
    <input type="hidden" id="aval" value="<?php echo $config["user"] ?>">
    <table id="adminInvoices" class="datatable" width="100%" cellspacing="1" cellpadding="3">
        <thead>
            <tr>
                <th>#</th>
                <th>Folio</th>
                <th>Fecha de creación</th>
                <th>Receptor</th>
                <th>Núm. de cliente</th>
                <th>Núm. de pedido</th>
                <th>Estado</th>
                <th>PDF</th>
                <th>XML</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($invoices->data as $invoice):
                $index ++;
                $label = ($invoice->Status == 'enviada') ? 'label-success' : 'label-danger';
            ?>
            <tr>
                <th scope="row"><?php echo $index ?></th>
                <td><?php echo $invoice->Folio ?></td>
                <td><?php echo $invoice->FechaTimbrado ?></td>
                <td><?php echo $invoice->Receptor ?></td>
                <td><a href="<?php echo $systemURL ?>admin/clientssummary.php?userid=<?php echo $invoice->ReferenceClient ?>" target="_blank"><?php echo $invoice->ReferenceClient ?></a></td>
                <td><a href="<?php echo $systemURL ?>admin/orders.php?action=view&id=<?php echo $invoice->NumOrder ?>" target="_blank"><?php echo $invoice->NumOrder ?></a></td>
                <td><span class="label <?php echo $label ?>"><?php echo $invoice->Status ?></span></td>
                <td><a href="http://devfactura.in/api/publica/invoice/<?php echo $invoice->UID ?>/pdf">PDF</a></td>
                <td><a href="http://devfactura.in/api/publica/invoice/<?php echo $invoice->UID ?>/xml">XML</a></td>
                <?php if($invoice->Status == 'enviada'): ?>
                <td>
                    <a href="#" class="btn-send-email btn btn-info" data-uid="<?php echo $invoice->UID ?>">
                        <span class="glyphicon glyphicon-envelope"></span>
                        Enviar por correo
                    </a>
                    <a href="#" class="btn-cancel-invoice btn btn-danger" data-uid="<?php echo $invoice->UID ?>">
                        <span class="glyphicon glyphicon-ban-circle"></span>
                        Cancelar
                    </a>
                </td>
                <?php else: ?>
                    <td>&nbsp;</td>
                <?php endif ?>
            </tr>
            <?php
            endforeach
            ?>


        </tbody>
    </table>
</div>
<div id="facturaModal" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="facturaModalLabel">Mensaje de Factura.com</h4>
            </div>
            <div class="modal-body" id="facturaModalText"></div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
             </div>
        </div>
    </div>
</div>
<div id="facturaModalConfirm" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="facturaModalLabel">Cancelar Factura.com</h4>
            </div>
            <div class="modal-body" id="facturaModalText">
                ¿Seguro que desea cancelar esta factura?
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary" id="cancelInvoiceBtn">Cencelar factura</button>
                <button type="button" data-dismiss="modal" class="btn">Conservar factura</button>
            </div>
        </div>
    </div>
</div>
