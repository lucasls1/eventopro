<?php if(!class_exists('Rain\Tpl')){exit;}?><div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Minha Conta</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="single-product-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <?php require $this->checkTemplate("profile-menu");?>
            </div>
            <div class="col-md-9">


                <div class="alert alert-danger">
                 <p> Atenção</p>
                   <p>Está ação irá apagar todos os dados da sua conta.</p>
                   <p>Não será possivel recupera-la novamente.</p>
                </div>

                <form method="post" action="/profile/delete-conta">

                    <button type="submit" class="btn btn-primary">Deletar Conta</button>

                </form>
            </div>
        </div>
    </div>
</div>