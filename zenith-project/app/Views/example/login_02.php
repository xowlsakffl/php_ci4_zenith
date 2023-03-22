<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.login') ?> <?= $this->endSection() ?>

<?= $this->section('guestContent') ?>

<div class="wrap">
<div class="container-fluid account02-container">
    <div class="btn btn-outline-primary">
        <button type="button" class="drag box" id="drag" draggable="true">Not a member? Sign up
            <div class="item" id="item" draggable="true"></div>
        </button>
        <!-- <?php if (setting('Auth.allowRegistration')) : ?>
            <div class="text-start"><?= lang('Auth.needAccount') ?> <a href="<?= url_to('register') ?>"><?= lang('Auth.register') ?></a></div>
        <?php endif ?> -->
    </div>

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
            </form>
        </div>
    </div>
</div>
</div>
<script>
    const item = document.querySelector('.item');
    const boxes = document.querySelectorAll('.box');
    const target = document.querySelector('.target');

    item.addEventListener('dragstart', dragStart);

    boxes.forEach(box => {
        box.addEventListener('dragenter', dragEnter)
        box.addEventListener('dragover', dragOver);
        box.addEventListener('dragleave', dragLeave);
        box.addEventListener('drop', drop);
    });

function dragStart(e) {
    e.dataTransfer.setData('text/plain', e.target.id);

    setTimeout(() => {
        e.target.classList.add('hide');
    }, 0);
}

function dragEnter(e) {
    e.preventDefault();
    e.target.classList.add('drag-over'); //
    console.log('dragEnter')
}

function dragOver(e) {
    e.preventDefault();
    e.target.classList.add('drag-over');
    console.log('dragOver')
}

function dragLeave(e) {
    e.target.classList.remove('drag-over');
    item.classList.remove('hide');
    console.log('dragLeave')
}

function drop(e) {
    e.target.classList.remove('drag-over');
    console.log('drop')

    const id = e.dataTransfer.getData('text/plain');
    const draggable = document.getElementById(id);

    e.target.appendChild(draggable);

    draggable.classList.remove('hide');
    location.href = "https://zenith.chainsaw.co.kr/example/signUP";    
}
</script>
<?= $this->endSection() ?>
