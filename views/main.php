<?php
require 'inc/modules_config.php';
?>

<main>
    <div class="cell-list">
		<?php if ($modules['module_users']) { ?>
            <a href="users" class="cell">
                <div class="title">
                    <img src="public/images/icon_db.png" alt="icon">
                    <h2><?=$language[$lang]["main_base"]?></h2>
                </div>
                <p class="t-2 t-sm"><?=$language[$lang]["main_base_desc"]?></p>
            </a>
		<?php }
		if ($modules['module_chat']) { ?>
            <a href="chat" class="cell">
                <div class="title">
                    <img src="public/images/icon_chat.png" alt="icon">
                    <h2><?=$language[$lang]["main_chat"]?></h2>
                </div>
                <p class="t-2 t-sm"><?=$language[$lang]["main_chat_desc"]?></p>
            </a>
		<?php }
		if ($modules['module_visits']) { ?>
            <a href="visits" class="cell">
                <div class="title">
                    <img src="public/images/icon_stats.png" alt="icon">
                    <h2><?=$language[$lang]["main_stats"]?></h2>
                </div>
                <p class="t-2 t-sm"><?=$language[$lang]["main_stats_desc"]?></p>
            </a>
		<?php }
		if ($_SESSION['role'] == 3) { ?>
            <a href="admin" class="cell bg-cell-r">
                <div class="title">
                    <img src="public/images/icon_ap.png" alt="icon">
                    <h2><?=$language[$lang]["main_admin"]?></h2>
                </div>
                <p class="t-2 t-sm"><?=$language[$lang]["main_admin_desc"]?></p>
            </a>
		<?php } ?>
    </div>
</main>
