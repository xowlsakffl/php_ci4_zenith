<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.login') ?> <?= $this->endSection() ?>

<?= $this->section('guestContent') ?>

<div class="row container-fluid register-container">
    <div class="card col-lg-6 col-12">
    <form action="<?= url_to('login') ?>" method="post">
        <?= csrf_field() ?>

        <h1 class="card-title">로그인</h1>   

        <?php if (session('error') !== null) : ?>
            <div class="alert alert-danger lh-base" role="alert"><?= session('error') ?></div>
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

        <?php if (session('message') !== null) : ?>
        <div class="alert alert-success" role="alert"><?= session('message') ?></div>
        <?php endif ?>

        <!-- Email -->
        <div class="mb-2 position-relative">
            <i class="bi bi-person-check"></i>
            <input type="text" class="form-control" name="username" placeholder="<?= lang('Auth.username') ?>" value="<?= old('username') ?>" />
        </div>

        <!-- Password -->
        <div class="mb-2 position-relative">
            <i class="fa fa-envelope-o"></i>
            <input type="password" class="form-control" name="password" inputmode="text" autocomplete="current-password" placeholder="<?= lang('Auth.password') ?>" />
        </div>

        <!-- Remember me -->
        <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" name="remember" class="form-check-input" <?php if (old('remember')): ?> checked<?php endif ?>>
                    <?= lang('Auth.rememberMe') ?>
                </label>
            </div>
        <?php endif; ?>

        <div class="my-5">
            <button type="submit" class="btn btn-outline-primary btn-block"><?= lang('Auth.login') ?></button>
        </div>

        <div class="d-flex justify-content-between">
            <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
                <div class="d-flex align-items-center icon"><a href="<?= url_to('magic-link') ?>"><span class="material-symbols-outlined">lock_person</span><?= lang('Auth.forgotPassword') ?> </a></div>
            <?php endif ?>

            <?php if (setting('Auth.allowRegistration')) : ?>
                <div class="d-flex align-items-center icon"><a href="<?= url_to('register') ?>"><span class="material-symbols-outlined">how_to_reg</span><?= lang('Auth.register') ?></a></div>
            <?php endif ?>
        </div>
    </form>
    </div>
</div>
<script>
    //slide up 효과
    let account = document.querySelector('.register-container form');
    let effect = account.querySelectorAll('form > div');
    let i=0;
    let timer = setInterval(function(){
        effect[i].classList.add('effect');     
        i++;
    
        if(i >= effect.length){
            clearInterval(timer); 
        }              
    },150); 
</script>
<?= $this->endSection() ?>
