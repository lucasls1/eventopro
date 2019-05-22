<?php if(!class_exists('Rain\Tpl')){exit;}?><div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2><?php echo htmlspecialchars( $category["nme_categoria"], ENT_COMPAT, 'UTF-8', FALSE ); ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="single-product-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">
            <?php $counter1=-1;  if( isset($evento) && ( is_array($evento) || $evento instanceof Traversable ) && sizeof($evento) ) foreach( $evento as $key1 => $value1 ){ $counter1++; ?>

            <div class="col-md-3 col-sm-6">
                <div class="single-shop-product">
                    <div class="product-upper">
                        <img src="<?php echo htmlspecialchars( $value1["desphoto"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" alt="Photo">
                    </div>
                    <h2><a href="/eventos/<?php echo htmlspecialchars( $value1["url_url"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"><?php echo htmlspecialchars( $value1["nme_evento"], ENT_COMPAT, 'UTF-8', FALSE ); ?></a></h2>

                    </div>
                </div>
                <?php } ?>


        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="product-pagination text-center">
                    <nav>
                        <ul class="pagination">
                        <?php $counter1=-1;  if( isset($pages) && ( is_array($pages) || $pages instanceof Traversable ) && sizeof($pages) ) foreach( $pages as $key1 => $value1 ){ $counter1++; ?>

                        <li><a href="<?php echo htmlspecialchars( $value1["link"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"><?php echo htmlspecialchars( $value1["page"], ENT_COMPAT, 'UTF-8', FALSE ); ?></a></li>
                       <?php } ?>

                        </ul>
                    </nav>                        
                </div>
            </div>
        </div>
    </div>
</div>