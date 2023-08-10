<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.register') ?> <?= $this->endSection() ?>

<?= $this->section('guestContent') ?>
<div class="row container-fluid account-container">
    <div class="card col-lg-6 col-12">
        

        <form action="<?= url_to('register') ?>" method="post">
            <?= csrf_field() ?>
            <h5 class="card-title"><?= lang('Auth.register') ?></h5>
            <?php if (session('error') !== null) : ?>
            <div class="alert alert-danger" role="alert"><?= session('error') ?></div>
            <?php elseif (session('errors') !== null) : ?>
                <div class="alert alert-danger lh-base" role="alert">
                    <?php if (is_array(session('errors'))) : ?>
                        <?php foreach (session('errors') as $error) : ?>
                            <?= $error ?>
                            <br>
                        <?php endforeach ?>
                    <?php else : ?>
                        <?= session('errors') ?>
                    <?php endif ?>
                </div>
            <?php endif ?>
            <!-- Email -->
            <div class="mb-2 position-relative">
                <i class="fa fa-envelope-o"></i>
                <input type="email" class="form-control" name="email" inputmode="email" autocomplete="off" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>"/>
            </div>            

            <!-- Username -->
            <div class="mb-2 position-relative">
            <i class="bi bi-person-check"></i>
                <input type="text" class="form-control" name="username" inputmode="text" autocomplete="off" placeholder="<?= lang('Auth.username') ?>" value="<?= old('username') ?>"/>
            </div>

            <!-- Password -->
            <div class="mb-2 position-relative">
            <i class="bi bi-lock"></i>
                <input type="password" class="form-control" name="password" inputmode="text" autocomplete="off" placeholder="<?= lang('Auth.password') ?>" />
            </div>         

            <!-- Password (Again) -->
            <div class="mb-2 position-relative">
                <i class="bi bi-check-circle"></i>
                <input type="password" class="form-control" name="password_confirm" inputmode="text" autocomplete="off" placeholder="<?= lang('Auth.passwordConfirm') ?>" />
            </div>


            <div class="my-5">
                <button type="submit" class="btn btn-outline-primary btn-block"><?= lang('Auth.register') ?></button>
            </div>

            <div class="icon"><a href="<?= url_to('login') ?>"><span class="material-symbols-outlined">input</span><?= lang('Auth.login') ?></a></div>

        </form>
    </div>
</div>
<script>
    //slide up 효과
    let account = document.querySelector('.account-container form');
    let effect = account.querySelectorAll('form > div');
    let i=0;
    let timer = setInterval(function(){
        effect[i].classList.add('effect');     
        i++;

        if(i >= effect.length){
            clearInterval(timer); 
        }              
    },150); 

    //회원정보 입력시 아이콘 색상 변화
    let icon_blue = '#4477CE';

    $('.form-control').bind('keyup',function(){
        $(this).prev().css('color',icon_blue);
        $(this).css('color',icon_blue);
    });
</script>
<?= $this->endSection() ?>
