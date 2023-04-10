<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.register') ?> <?= $this->endSection() ?>

<?= $this->section('guestContent') ?>
<div class="container-fluid exam-container" id="register">
    <div class="card col-10 col-md-5">
        <div class="card-body">
            <h5 class="card-title"></h5>

            <?php if (session('error') !== null) : ?>
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

            <form action="<?= url_to('register') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Email -->
                <div class="mb-2 position-relative">
                    <i class="fa fa-envelope-o"></i>
                    <input type="email" class="form-control" name="email" inputmode="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>" required />
                </div>

                <!-- Username -->
                <div class="mb-4">              
                    <i class="bi bi-person-check"></i>
                    <input type="text" class="form-control" name="username" inputmode="text" autocomplete="username" placeholder="<?= lang('Auth.username') ?>" value="<?= old('username') ?>" required />
                </div>

                <!-- Password -->
                <div class="mb-2 position-relative">
                    <i class="bi bi-lock"></i>
                    <input type="password" class="form-control" name="password" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.password') ?>" required />
                </div>

                <!-- Password (Again) -->
                <div class="mb-5">
                    <i class="bi bi-check-circle"></i>
                    <input type="password" class="form-control" name="password_confirm" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.passwordConfirm') ?>" required />
                </div>

                <div class="m-3">
                    <button type="submit" class="btn btn-primary btn-block"><?= lang('Auth.register') ?></button>
                </div>

                <div class="text-start"
                >
                    <?= lang('Auth.haveAccount') ?> <a href="<?= url_to('login') ?>"><?= lang('Auth.login') ?></a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    //slide up 효과
    let account = document.querySelector('.exam-container form');
    let effect = account.querySelectorAll('form > div');
    let i=0;
    let timer = setInterval(function(){
        effect[i].classList.add('effect');     
        i++;
    
        if(i >= effect.length){
            clearInterval(timer); 
        }              
    },150); 

    // 타이핑 효과
    let typingTxt = 'Register';    
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
