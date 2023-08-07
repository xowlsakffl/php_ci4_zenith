<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?>비밀번호 재설정<?= $this->endSection() ?>

<?= $this->section('guestContent') ?>

<div class="row container-fluid account-container">
    <div class="card col-lg-6 col-12">
        <form action="/set-password" method="post">
            <?= csrf_field() ?>
            <h1 class="card-title">비밀번호 재설정</h1>   

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

            <!-- Email -->
            <div class="mb-2 position-relative">
                <i class="bi bi-lock"></i>
                <input type="password" class="form-control" name="password" placeholder="<?= lang('Auth.password') ?>" value=""/>
            </div>

            <div class="mb-2 position-relative">
                <i class="bi bi-check-circle"></i>
                <input type="password" class="form-control" name="password_confirm" placeholder="<?= lang('Auth.passwordConfirm') ?>" value=""/>
            </div>

            <div class="my-5">
                <button type="submit" class="btn btn-outline-primary btn-block">비밀번호 변경</button>
            </div>

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
</script>
<?= $this->endSection() ?>
