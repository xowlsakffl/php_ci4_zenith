<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?>비밀번호 재설정<?= $this->endSection() ?>

<?= $this->section('guestContent') ?>

<div class="container d-flex justify-content-center p-5">
    <div class="card col-12 col-md-5 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-5">비밀번호 재설정</h5>

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

            <form action="/set-password" method="post">
                <?= csrf_field() ?>

                <!-- Email -->
                <div class="mb-2">
                    <input type="password" class="form-control" name="password" placeholder="<?= lang('Auth.password') ?>"
                           value="" />
                </div>

                <div class="mb-2">
                    <input type="password" class="form-control" name="password_confirm" placeholder="<?= lang('Auth.passwordConfirm') ?>"
                           value="" />
                </div>

                <div class="d-grid col-12 col-md-8 mx-auto m-3">
                    <button type="submit" class="btn btn-primary btn-block">비밀번호 변경</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
