<?php
require 'inc/modules_config.php';
if ($_SESSION['role'] != 3) {
	header('Location: /404');
	exit;
}
?>

<link rel="stylesheet" href="modules/admin/template/styles/styles.css">
<script type="text/javascript" src="modules/admin/ajax/ajax.js"></script>

<main>
    <div class='nav-list'>
        <nav>
            <div class="form">
                <a href='/admin' class='btn-b btn-btn t-size-md t-1 t-md'><?=$language[$lang]['users']?></a>
                <a href='/servers' class='btn-b btn-btn t-size-md t-1 t-md'><?=$language[$lang]['servers']?></a>
                <a href='/clean' class='btn-b btn-btn t-size-md t-1 t-md'><?=$language[$lang]['clean']?></a>
                <a href='/' class='btn-b btn-btn t-size-md t-1 t-md'><?=$language[$lang]['back']?></a>
            </div>
        </nav>

        <nav class='nav-admin'>
            <div class='profile-block pb-2'>
                <div class='fl-b'><span class='t-3'><?=$language[$lang]['version']?></span><span class='t-2'>1.x</span></div>
                <div class='fl-b'><span class='t-3'><?=$language[$lang]['license']?></span><span class='t-2'><?=$language[$lang]['permament']?></span></div>
                <div class='fl-b'><span class='t-3'><?=$language[$lang]['domain']?></span><span class='t-2'><?= $url ?></span></div>
            </div>
        </nav>

    </div>
    <section>
        <div class='server-block-left'>
            <div class="form">
                <select id='valueType'>
                    <option value="1"><?=$language[$lang]['log_chat']?></option>
                    <option value="2"><?=$language[$lang]['log_visits']?></option>
                </select>
                <select id='valueDays'>
                    <option value="360">> 360 <?=$language[$lang]['days']?></option>
                    <option value="120">> 120 <?=$language[$lang]['days']?></option>
                    <option value="90">> 90 <?=$language[$lang]['days']?></option>
                    <option value="30">> 30 <?=$language[$lang]['days']?></option>
                    <option value="7">> 7 <?=$language[$lang]['days']?></option>
                    <option value="1">> 1 <?=$language[$lang]['day']?></option>
                </select>
                <input type="submit" class='btn-btn t-size-md t-1 t-md' value="<?=$language[$lang]['clear']?>" id='btn-clean'>
            </div>
        </div>
        <div class='server-block-right'>
            <div class='c-warning'>
                <p>
	                <?=$language[$lang]['cwarn_1']?>
                    <br><br>
	                <?=$language[$lang]['cwarn_2']?>
                </p>
            </div>
        </div>
    </section>
</main>