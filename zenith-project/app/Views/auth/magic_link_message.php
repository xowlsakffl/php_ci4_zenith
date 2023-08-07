<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.useMagicLink') ?> <?= $this->endSection() ?>

<?= $this->section('guestContent') ?>

<div class="row container-fluid account-container">
    <div class="card col-lg-6 col-12">
        <h1 class="card-title"><?= lang('Auth.useMagicLink') ?></h1>

        <div class="mb- position-relative">
            <i class="fa fa-envelope-o me-2"></i>
            <b><?= lang('Auth.checkYourEmail') ?></b>

            <p class="mt-3"><?= lang('Auth.magicLinkDetails', [setting('Auth.magicLinkLifetime') / 60]) ?></p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
