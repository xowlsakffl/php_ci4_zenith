<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.login') ?> <?= $this->endSection() ?>

<?= $this->section('guestContent') ?>

<div class="container-fluid account02-container">
    <div class="card col-10 col-md-5">
        <div class="card-body">
            <h5 class="card-title">SIGN IN</h5>
        
            <?php if (session('error') !== null) : ?>  <!-- 패스워드, alert -->
                <div class="alert alert-danger" role="alert"><?= session('error') ?></div>
            <?php elseif (session('errors') !== null) : ?>
                <div class="alert alert-danger" role="alert">
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

            <form action="<?= url_to('login') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Email -->
                <div class="mb-2 position-relative">
                    <p>EMAIL ADDRESS</p>
                    <input type="email" class="form-control" name="email" inputmode="email" autocomplete="email" placeholder="<?= lang('Email') ?>" value="<?= old('email') ?>"/>
                </div>

                <!-- Password -->
                <div class="mb-2 position-relative">
                    <p>PASSWORD</p>
                    <input type="password" class="form-control" name="password" inputmode="text" autocomplete="current-password" placeholder="<?= lang('Auth.password') ?>"  />

                    
                </div>

                <!-- Remember me -->
                <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" name="remember" class="form-check-input" <?php if (old('remember')): ?> checked<?php endif ?>>
                            <span><?= lang('Auth.rememberMe') ?></span>
                        </label>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary btn-block"><?= lang('SIGN IN') ?></button>                

                <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
                    <p class="text-center forgot"><a href="<?= url_to('magic-link') ?>"><?= lang('Forgot password') ?></a></p>
                <?php endif ?>

                <?php if (setting('Auth.allowRegistration')) : ?>
                    <div class="text-start"><?= lang('Auth.needAccount') ?> <a href="<?= url_to('register') ?>"><?= lang('Auth.register') ?></a></div>
                <?php endif ?>

            </form>
        </div>
    </div>
</div>
<script>
    //slide up 효과
    let account = document.querySelector('.account-container form');
    let effect = account.querySelectorAll(' form > div');
    let i=0;
    let timer = setInterval(function(){
        effect[i].classList.add('effect');     
        i++;
    
        if(i >= effect.length){
            clearInterval(timer); 
        }              
    },700); 

    // 타이핑 효과
    let typingTxt = 'Login';    
    let typingIdx = 0;   
    let tyInt = setInterval(typing,250); 
    typingTxt=typingTxt.split("");

    function typing(){
        if(typingIdx < typingTxt.length){
            $('h5.card-title').append(typingTxt[typingIdx]);
            typingIdx++; 
        }else{
            clearInterval(tyInt);    
        }
    }  
</script>
<?= $this->endSection() ?>
