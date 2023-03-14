<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.login') ?> <?= $this->endSection() ?>

<?= $this->section('guestContent') ?>

    <div class="container-fluid account-container">
        <div class="card col-12 col-md-5">
            <div class="card-body">
                <h5 class="card-title mb-5"><?= lang('Auth.login') ?></h5>
            
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
                        <i class="fa fa-envelope-o"></i>
                        <input type="email" class="form-control" name="email" inputmode="email" autocomplete="email" placeholder="<?= lang('Email') ?>" value="<?= old('email') ?>"/>
                    </div>

                    <!-- Password -->
                    <div class="mb-2 position-relative">
                        <i class="fa fa-lock"></i>
                        <input type="password" class="form-control" name="password" inputmode="text" autocomplete="current-password" placeholder="<?= lang('Auth.password') ?>"  />

                        <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
                            <p class="text-center forgot"><a href="<?= url_to('magic-link') ?>"><?= lang('Forgot?') ?></a></p>
                        <?php endif ?>
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

                    <div class="m-3">
                        <button type="submit" class="btn btn-primary btn-block"><?= lang('Auth.login') ?></button>
                    </div>
                    

                    <?php if (setting('Auth.allowRegistration')) : ?>
                        <p class="text-start"><?= lang('Auth.needAccount') ?> <a href="<?= url_to('register') ?>"><?= lang('Auth.register') ?></a></p>
                    <?php endif ?>

                </form>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>
