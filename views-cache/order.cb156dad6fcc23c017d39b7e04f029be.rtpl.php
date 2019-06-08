<?php if(!class_exists('Rain\Tpl')){exit;}?><!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1>
        Pedido N°<?php echo htmlspecialchars( $order["pk_order"], ENT_COMPAT, 'UTF-8', FALSE ); ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="/admin"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="/admin/orders">Pedidos</a></li>
        <li class="active"><a href="/admin/orders/<?php echo htmlspecialchars( $order["pk_order"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">Pedido N°<?php echo htmlspecialchars( $order["pk_order"], ENT_COMPAT, 'UTF-8', FALSE ); ?></a></li>
    </ol>
    </section>

    <section class="invoice">
        <!-- title row -->
        <div class="row">
            <div class="col-xs-12">
            <h2 class="page-header">
                <img src="/res/site/img/logoEventoPro.png" alt="Logo">
                <small class="pull-right">Date: <?php echo date('d/m/Y'); ?></small>
            </h2>
            </div>
            <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
            De
            <address>
                <strong>EventoPro</strong><br>
                Avenida das Araucárias, Rua 214 Lote 1/17, QS 1<br>
                Taguatinga, Brasília - DF<br>
                Telefone: (61) 3171-3080<br>
                E-mail: suporte@eventopro.com.br
            </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
            Para
            <address>
                <strong><?php echo htmlspecialchars( $order["nme_pessoa"], ENT_COMPAT, 'UTF-8', FALSE ); ?></strong><br>
                <?php echo htmlspecialchars( $order["end_endereco"], ENT_COMPAT, 'UTF-8', FALSE ); ?>, <?php echo htmlspecialchars( $order["cpt_complemento"], ENT_COMPAT, 'UTF-8', FALSE ); ?><br>
                <?php echo htmlspecialchars( $order["cid_cidade"], ENT_COMPAT, 'UTF-8', FALSE ); ?> - <?php echo htmlspecialchars( $order["est_estado"], ENT_COMPAT, 'UTF-8', FALSE ); ?><br>
                <?php if( $order["nrphone"] && $order["nrphone"]!='0' ){ ?>Telefone: <?php echo htmlspecialchars( $order["nrphone"], ENT_COMPAT, 'UTF-8', FALSE ); ?><br><?php } ?>
                E-mail: <?php echo htmlspecialchars( $order["eml_email"], ENT_COMPAT, 'UTF-8', FALSE ); ?>
            </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
            <b>Pedido #<?php echo htmlspecialchars( $order["pk_order"], ENT_COMPAT, 'UTF-8', FALSE ); ?></b><br>
            <br>
            <b>Emitido em:</b> <?php echo formatDate($order["dti_registro"]); ?><br>
            <b>Pago em:</b> <?php echo formatDate($order["dti_registro"]); ?>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    
        <!-- Table row -->
        <div class="row">
            <div class="col-xs-12 table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Qtd</th>
                    <th>Produto</th>
                    <th>Código #</th>
                    <th>Subtotal</th>
                </tr>
                </thead>
                <tbody>
                <?php $counter1=-1;  if( isset($evento) && ( is_array($evento) || $evento instanceof Traversable ) && sizeof($evento) ) foreach( $evento as $key1 => $value1 ){ $counter1++; ?>
                <tr>
                    <td><?php echo htmlspecialchars( $value1["nrqtd"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
                    <td><?php echo htmlspecialchars( $value1["nme_evento"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
                    <td><?php echo htmlspecialchars( $value1["pk_evento"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
                    <td>R$<?php echo formatPrice($order["vlr_total"]); ?></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    
        <div class="row">
            <!-- accepted payments column -->
            <div class="col-xs-6">

                <p class="lead">Forma de Pagamento</p>
                
                <table class="table">
                    <tbody>
                    <tr>
                        <th style="width:180px;">Método de Pagamento:</th>
                        <td>Boleto</td>
                    </tr>
                    <tr>
                        <th>Parcelas:</th>
                        <td>1x</td>
                    </tr>
                    <!--
                    <tr>
                        <th>Valor da Parcela:</th>
                        <td>R$100,00</td>
                    </tr>
                    -->
                    </tbody>
                </table>

            </div>
            <!-- /.col -->
            <div class="col-xs-6">
            <p class="lead">Resumo do Pedido</p>
    
            <div class="table-responsive">
                <table class="table">
                <tbody>



                <tr>
                    <th>Total:</th>
                    <td>R$<?php echo formatPrice($cart["vltotal"]); ?></td>
                </tr>
                </tbody></table>
            </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    
        <!-- this row will not appear when printing -->
        <div class="row no-print">
            <div class="col-xs-12">
                <button type="button" onclick="window.location.href = '/admin/orders/<?php echo htmlspecialchars( $order["pk_order"], ENT_COMPAT, 'UTF-8', FALSE ); ?>/status'" class="btn btn-default pull-left" style="margin-left: 5px;">
                    <i class="fa fa-pencil"></i> Editar Status
                </button>
                <button type="button" onclick="window.open('/boleto/<?php echo htmlspecialchars( $order["pk_status"], ENT_COMPAT, 'UTF-8', FALSE ); ?>')" class="btn btn-default pull-left" style="margin-left: 5px;">
                    <i class="fa fa-barcode"></i> Boleto
                </button>

                
                <button type="button" onclick="window.print()" class="btn btn-primary pull-right" style="margin-right: 5px;">
                    <i class="fa fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </section>

    <div class="clearfix"></div>

</div>
<!-- /.content-wrapper -->