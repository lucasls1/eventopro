<?php if(!class_exists('Rain\Tpl')){exit;}?><div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Carrinho de Compras</h2>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Page title area -->

<div class="single-product-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">
            
            <div class="col-md-12">
                <div class="product-content-right">
                    <div class="woocommerce">

                        <form action="/checkout">

                            <table cellspacing="0" class="shop_table cart">
                                <thead>
                                    <tr>
                                        <th class="product-remove">&nbsp;</th>
                                        <th class="product-thumbnail">&nbsp;</th>
                                        <th class="product-name">Evento</th>
                                        <th class="product-price">Preço</th>
                                        <th class="product-quantity">Quantidade</th>
                                        <th class="product-subtotal">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $counter1=-1;  if( isset($eventos) && ( is_array($eventos) || $eventos instanceof Traversable ) && sizeof($eventos) ) foreach( $eventos as $key1 => $value1 ){ $counter1++; ?>
                                    <tr class="cart_item">
                                        <td class="product-remove">
                                            <a title="Remove this item" class="remove" href="/carrinho/<?php echo htmlspecialchars( $value1["pk_evento"], ENT_COMPAT, 'UTF-8', FALSE ); ?>/remove">×</a>
                                        </td>

                                        <td class="product-thumbnail">
                                            <a href="/evento/<?php echo htmlspecialchars( $value1["url_url"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"><img width="145" height="145" alt="poster_1_up" class="shop_thumbnail" src="<?php echo htmlspecialchars( $value1["desphoto"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"></a>
                                        </td>

                                        <td class="product-name">
                                            <a href="/evento/<?php echo htmlspecialchars( $value1["url_url"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"><?php echo htmlspecialchars( $value1["nme_evento"], ENT_COMPAT, 'UTF-8', FALSE ); ?></a>
                                        </td>

                                        <td class="product-price">
                                            <span class="amount">R$ <?php echo formatPrice($value1["vlr_inteiro"]); ?> </span>
                                        </td>

                                        <td class="product-quantity">
                                            <div class="quantity buttons_added">
                                                <input type="button" class="minus" value="-" onclick="window.location.href = '/carrinho/<?php echo htmlspecialchars( $value1["pk_evento"], ENT_COMPAT, 'UTF-8', FALSE ); ?>/minus'">
                                                <input type="number" size="4" class="input-text qty text" title="Qty" value="<?php echo htmlspecialchars( $value1["nrqtd"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" min="0" step="1">
                                                <input type="button" class="plus" value="+" onclick="window.location.href = '/carrinho/<?php echo htmlspecialchars( $value1["pk_evento"], ENT_COMPAT, 'UTF-8', FALSE ); ?>/add'">
                                            </div>
                                        </td>

                                        <td class="product-subtotal">
                                            <span class="amount">R$ <?php echo formatPrice($value1["vlr_total"]); ?></span>
                                        </td>
                                    </tr>
                                <?php } ?>
                                    
                                </tbody>
                            </table>

                            <div class="cart-collaterals">

                            <div class="cart_totals ">

                                <h2>Resumo da Compra</h2>

                                <table cellspacing="0">
                                    <tbody>

                                            <tr class="order-total">
                                                <th>Total</th>
                                                <td><strong><span class="amount">R$ <?php echo formatPrice($cart["vltotal"]); ?></span></strong> </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                            <div class="pull-right">
                                <input type="submit" value="Finalizar Compra" name="proceed" class="checkout-button button alt wc-forward">
                            </div>

                        </form>

                    </div>                        
                </div>                    
            </div>
        </div>
    </div>
</div>