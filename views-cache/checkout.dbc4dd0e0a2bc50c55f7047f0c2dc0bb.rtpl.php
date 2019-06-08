<?php if(!class_exists('Rain\Tpl')){exit;}?>
<div class="product-big-title-area">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="product-bit-title text-center">
					<h2>Pagamento</h2>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="single-product-area">
	<div class="zigzag-bottom"></div>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="product-content-right">
					<form action="/checkout" class="checkout" method="post" name="checkout">
						<div id="customer_details" class="col2-set">
							<div class="row">
								<div class="col-md-12">

										<?php if( $error != '' ){ ?>
									<div class="alert alert-danger">
										<?php echo htmlspecialchars( $error, ENT_COMPAT, 'UTF-8', FALSE ); ?>
									</div>
									<?php } ?>

									<div class="woocommerce-billing-fields">
										<h3>Endereço de Cobrança</h3>
										<p id="billing_address_1_field" class="form-row form-row-wide address-field validate-required">
											<label class="" for="billing_cep_1">Cep <abbr title="required" class="required">*</abbr>
											</label>
											<input type="text" value="<?php echo htmlspecialchars( $endereco["cep_cep"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" placeholder="00000-000" id="billing_cep_1" name="cep_cep" class="input-text ">
											<input type="submit" value="Atualizar CEP" id="place_orde" class="button alt" formaction="/checkout" formmethod="get">
										</p>
										<div class="row">
											<div class="col-sm-9">
												<p id="billing_address_2_field" class="form-row form-row-wide address-field validate-required">
													<label class="" for="billing_address_2">Endereço <abbr title="required" class="required">*</abbr>
													</label>
													<input type="text" value="<?php echo htmlspecialchars( $endereco["end_endereco"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" placeholder="Logradouro, número e bairro" id="billing_address_1" name="end_endereco" class="input-text ">
												</p>
											</div>
											<div class="col-sm-3">
												<p id="billing_number_1_field" class="form-row form-row-wide number-field validate-required">
													<label class="" >Número <abbr title="required" class="required">*</abbr>
													</label>
													<input type="text" value="<?php echo htmlspecialchars( $endereco["nr_numero"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" placeholder="Número" id="billing_address_" name="nr_numero" class="input-text ">
												</p>
											</div>
										</div>
										<p id="billing_address_3_field" class="form-row form-row-wide address-field">
											<input type="text" value="" placeholder="Complemento (opcional)" id="billing_address_2" name="cpt_complemento" class="input-text ">
                                        </p>
                                        <p id="billing_district_field" class="form-row form-row-wide address-field validate-required" data-o_class="form-row form-row-wide address-field validate-required">
											<label class="" for="billing_district">Bairro <abbr title="required" class="required">*</abbr>
											</label>
											<input type="text" value="<?php echo htmlspecialchars( $endereco["des_distrito"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" placeholder="Cidade" id="billing_district" name="des_distrito" class="input-text ">
										</p>
										<p id="billing_city_field" class="form-row form-row-wide address-field validate-required" data-o_class="form-row form-row-wide address-field validate-required">
											<label class="" for="billing_city">Cidade <abbr title="required" class="required">*</abbr>
											</label>
											<input type="text" value="<?php echo htmlspecialchars( $endereco["cid_cidade"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" placeholder="Cidade" id="billing_city" name="cid_cidade" class="input-text ">
										</p>
										<p id="billing_stat_field" class="form-row form-row-first address-field validate-state" data-o_class="form-row form-row-first address-field validate-state">
											<label class="" for="billing_state">Estado</label>
											<input type="text" id="billing_stat" name="est_estado" placeholder="Estado" value="<?php echo htmlspecialchars( $endereco["est_estado"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" class="input-text ">
										</p>
										<p id="billing_state_field" class="form-row form-row-first address-field validate-state" data-o_class="form-row form-row-first address-field validate-state">
											<label class="" for="billing_state">País</label>
											<input type="text" id="billing_state" name="pais_pais" placeholder="País" value="<?php echo htmlspecialchars( $endereco["pais_pais"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" class="input-text ">
										</p>
										<div class="clear"></div>
										<h3 id="order_review_heading" style="margin-top:30px;">Detalhes do Pedido</h3>
										<div id="order_review" style="position: relative;">
											<table class="shop_table">
												<thead>
													<tr>
														<th class="product-name">Produto</th>
														<th class="product-total">Total</th>
													</tr>
												</thead>
												<tbody>
                                                    <?php $counter1=-1;  if( isset($evento) && ( is_array($evento) || $evento instanceof Traversable ) && sizeof($evento) ) foreach( $evento as $key1 => $value1 ){ $counter1++; ?>
													<tr class="cart_item">
														<td class="product-name">
															<?php echo htmlspecialchars( $value1["nme_evento"], ENT_COMPAT, 'UTF-8', FALSE ); ?> <strong class="product-quantity">× <?php echo htmlspecialchars( $value1["nrqtd"], ENT_COMPAT, 'UTF-8', FALSE ); ?></strong>
														</td>
														<td class="product-total">
															<span class="amount">R$<?php echo formatPrice($value1["vlr_total"]); ?></span>
														</td>
                                                    </tr>
                                                    <?php } ?>
												</tbody>
												<tfoot>

													<tr class="order-total">
														<th>Total do Pedido</th>
														<td><strong><span class="amount">R$<?php echo formatPrice($cart["vltotal"]); ?></span></strong> </td>
													</tr>
												</tfoot>
											</table>
											<div id="payment">
												<div class="form-row place-order">
													<input type="submit" data-value="Place order" value="Continuar" id="place_order" name="woocommerce_checkout_place_order" class="button alt">
												</div>
												<div class="clear"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>