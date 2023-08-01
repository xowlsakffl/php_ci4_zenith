<?=$this->extend('templates/front.php');?>

<!--타이틀-->
<?=$this->section('title');?>
    CHAIN 열혈광고 - 마이페이지
<?=$this->endSection();?>

<!--헤더-->
<?=$this->section('header');?>

<?=$this->endSection();?>

<!--바디-->
<?=$this->section('body');?>
<?=$this->endSection();?>

<!--컨텐츠영역-->
<?=$this->section('content');?>
<div class="sub-contents-wrap myPage-container">
    <div class="title-area">
        <h2 class="page-title">마이페이지</h2>
        <p class="title-disc">혼자서는 작은 한 방울이지만 함께 모이면 바다를 이룬다.</p>
    </div>
    
    <div class="sub-contents-wrap">
        <main class="container my-5">
            <h1 class="text-center"><?php echo $user->nickname?></h1>
            <div class="row justify-content-center mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h3 class="card-title mb-0">Profile</h3>
                        </div>
                        <div class="card-body">
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
                        <?php if (session('message') !== null) : ?>
                            <div class="alert alert-success" role="alert"><?= session('message') ?></div>
                        <?php endif ?>
                            <form action="/mypage/update" method="post">
                                <dl>
                                    <dt>아이디:</dt>
                                    <dd><?php echo $user->username?></dd> 
                                </dl>
                                <dl>
                                    <dt>이메일:</dt>
                                    <dd><?php echo $user->getEmail()?></dd> 
                                </dl>
                                <dl>
                                    <dt>기존 비밀번호:</dt>
                                    <dd><input type="password" class="form-control" name="old_password" placeholder="<?= lang('Auth.old_password') ?>"
                            value="" /></dd>
                                </dl>
                                <dl>
                                    <dt>신규 비밀번호:</dt>
                                    <dd><input type="password" class="form-control" name="password" placeholder="<?= lang('Auth.password') ?>"
                            value="" /></dd>
                                </dl>
                                <dl>
                                    <dt>비밀번호 확인:</dt>
                                    <dd><input type="password" class="form-control" name="password_confirm" placeholder="<?= lang('Auth.passwordConfirm') ?>"
                            value="" /></dd>
                                </dl>
                                <button type="submit" class="btn btn-primary">등록</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?=$this->endSection();?>

<?=$this->section('script');?>
<script>
</script>
<?=$this->endSection();?>

<!--푸터-->
<?=$this->section('footer');?>
<?=$this->endSection();?>
