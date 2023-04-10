<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.register') ?> <?= $this->endSection() ?>

<?= $this->section('guestContent') ?>
<div class="row container-fluid register-container">
    <div class="card col-lg-6 col-12">
    <form action="<?= url_to('register') ?>" method="post">  
        <?= csrf_field() ?>
        <h1 class="card-title">가입하기</h1>   
        <!-- Email -->
        <div class="mb-2 position-relative">
            <i class="fa fa-envelope-o"></i>
            <input type="email" class="form-control" name="email" inputmode="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>" required />
        </div>

        <!-- Username -->
        <div class="mb-2 position-relative">              
            <i class="bi bi-person-check"></i>
            <input type="text" class="form-control" name="username" inputmode="text" autocomplete="username" placeholder="<?= lang('Auth.username') ?>" value="<?= old('username') ?>" required />
        </div>

        <!-- Password -->
        <div class="mb-2 position-relative">
            <i class="bi bi-lock"></i>
            <input type="password" class="form-control" name="password" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.password') ?>" required />
        </div>

        <!-- Password (Again) -->
        <div class="mb-2 position-relative">
            <i class="bi bi-check-circle"></i>
            <input type="password" class="form-control" name="password_confirm" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.passwordConfirm') ?>" required />
        </div>

        

        <div class="text-start"
        >
            <?= lang('Auth.haveAccount') ?> <a href="<?= url_to('login') ?>"><?= lang('Auth.login') ?></a>
        </div>        

        <div class="mt-5">
            <button type="submit" class="btn btn-outline-primary btn-block"><?= lang('Auth.register') ?></button>
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
