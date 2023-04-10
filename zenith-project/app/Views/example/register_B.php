<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.register') ?> <?= $this->endSection() ?>

<?= $this->section('guestContent') ?>
<div class="wrap">
    <div class="container-fluid exam02-container toggle">
        <div class="btn btn-outline-primary">
            <button type="button" class="drag box">Have an account? Sign in
                <div class="item" id="item" draggable="true"></div>
            </button>
            <!-- <p class="text-center site"><?= lang('Auth.haveAccount') ?><a href="<?= url_to('login') ?>"><?= lang('Auth.login') ?></a></p> -->
        </div>

        <div class="card col-10 col-md-5">
            <div class="card-body">
                <h5 class="card-title"><?= lang('SIGN UP') ?></h5>

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
                        <p>EMAIL ADDRESS</p>
                        <input type="email" class="form-control" name="email" inputmode="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>" required />
                    </div>

                    <!-- Username -->
                    <div class="mb-4 position-relative">
                        <p>username</p>
                        <input type="text" class="form-control" name="username" inputmode="text" autocomplete="username" placeholder="<?= lang('Auth.username') ?>" value="<?= old('username') ?>" required />
                    </div>

                    <!-- Password -->
                    <div class="mb-2 position-relative">
                        <p>PASSWORD</p>
                        <input type="password" class="form-control" name="password" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.password') ?>" required />
                    </div>

                    <!-- Password (Again) -->
                    <div class="mb-5">
                        <p>PASSWORD CONFIRM</p>
                        <input type="password" class="form-control" name="password_confirm" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.passwordConfirm') ?>" required />
                    </div>

                    <button type="submit" class="btn btn-primary btn-block"><?= lang('SIGN IN') ?></button>
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
    location.href = "https://zenith.chainsaw.co.kr/example/login_02";    
}
</script>
<?= $this->endSection() ?>


