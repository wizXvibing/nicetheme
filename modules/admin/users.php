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
    <section class='users-sec'>
		<?php

		$role_list = array(
			$language[$lang]['role_0'],
			$language[$lang]['role_1'],
			$language[$lang]['role_2'],
			$language[$lang]['role_3'],
		);

		$STM = $pdo->query("SELECT nick, role, steamid64 FROM syspanel_users ORDER BY id");
		while ($row = $STM->fetch()) {

			$steamids = $steamids . $row[2] . ","
			?>
            <a class='pop-open' id='<?= $row[2] ?>'>
                <div class='block'>
                    <img class='users-c' src='modules/admin/template/images/icon_chat_user.svg'>
                    <div class='title'>
                        <span class='t-2'><?= $row[0] ?></span>
                        <p class='t-3 t-size-sm'><?= $role_list[$row[1]] ?></p>
                    </div>
                </div>
            </a>
		<?php }
		$steamids = substr($steamids, 0, -1);
		if ($steamids) $pArrayUsers = $s->getImageList($steamids);
		?>


    </section>
</main>

<div class='popups-inner'>
    <div class='popup users-popup'>

        <div class="form">

            <select id="role_id">
                <option value=""><?=$language[$lang]['select_role']?></option>
                <option value="0"><?=$language[$lang]['role_0']?></option>
                <option value="1"><?=$language[$lang]['role_1']?></option>
                <option value="2"><?=$language[$lang]['role_2']?></option>
                <option value="3"><?=$language[$lang]['role_3']?></option>
            </select>

            <input type="submit" class='btn-btn t-size-md t-1 t-md' value="<?=$language[$lang]['save']?>" id="user-save">
            <a id='user-cancel' class='btn-b btn-btn t-size-md t-1 t-md'><?=$language[$lang]['cancel']?></a>

        </div>
    </div>
</div>

<script>
    let gravit = 0;
    let spisokid;
    window.onload = function () {
        let ar = <?php echo json_encode($pArrayUsers); ?>;
        if (ar) {
            let block = document.querySelectorAll('.pop-open');

            block.forEach((el, idx) => {
                let sid = el.id;
                for (let i = 0; i < ar.length; i++) {
                    if (ar[i].steamid == sid) {
                        spisokid = i;
                        break;
                    }
                }
                el.querySelector('img').setAttribute('src', ar[spisokid]['avatarmedium']);
                el.querySelector("span").innerHTML = ar[spisokid]['personaname'];
            });
        }
    }
</script>