<?php if(!class_exists('Rain\Tpl')){exit;}?>

<div class="slider-area">
    <!-- Slider -->
    <div class="block-slider block-slider4">
        <ul class="" id="bxslider-home4">
            <li>
                <img src="/res/site/img/happyholi.png" alt="Slide">

            </li>
            <li><img src="/res/site/img/playground.png" alt="Slide">

            </li>
            <li><img src="/res/site/img/truelove.png" alt="Slide">

            </li>
            <li><img src="/res/site/img/bailedoimperador.png" alt="Slide">

            </li>
        </ul>
    </div>
    <!-- ./Slider -->
</div> <!-- End slider area -->


<div class="maincontent-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="latest-product">
                    <h2 class="section-title">Eventos</h2>
                    <div class="product-carousel">
                        <?php $counter1=-1;  if( isset($eventos) && ( is_array($eventos) || $eventos instanceof Traversable ) && sizeof($eventos) ) foreach( $eventos as $key1 => $value1 ){ $counter1++; ?>

                        <div class="single-product">
                            <div class="product-f-image">
                                <img src="<?php echo htmlspecialchars( $value1["desphoto"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"alt="Photo">
                                <div class="product-hover">

                                    <a href="/eventos/<?php echo htmlspecialchars( $value1["url_url"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" class="view-details-link"><i class="fa fa-link"></i> Ver Detalhes</a>
                                </div>
                            </div>

                            <h2><a href="/eventos/<?php echo htmlspecialchars( $value1["url_url"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"><?php echo htmlspecialchars( $value1["nme_evento"], ENT_COMPAT, 'UTF-8', FALSE ); ?></a></h2>

                        </div>
                        <?php } ?>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End main content area -->

<div class="brands-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="brand-wrapper">
                    <div class="brand-list">
                        <img src="/res/site/img/brand1.png" alt="">
                        <img src="/res/site/img/brand2.png" alt="">
                        <img src="/res/site/img/brand3.png" alt="">
                        <img src="/res/site/img/brand4.png" alt="">
                        <img src="/res/site/img/brand5.png" alt="">
                        <img src="/res/site/img/brand6.png" alt="">
                        <img src="/res/site/img/brand1.png" alt="">
                        <img src="/res/site/img/brand2.png" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End brands area -->