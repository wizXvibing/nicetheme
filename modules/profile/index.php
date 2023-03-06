<?php
require 'inc/modules_config.php';
if (!($modules['module_profile'])) {
	header('Location: /404');
	exit;
}
require 'modules/profile/ajax/actions.php';
?>

<link rel="stylesheet" href="modules/profile/template/styles/styles.css">
<script type="text/javascript" src="modules/profile/ajax/ajax.js"></script>

<main>
    <div class='nav-list'>
        <nav>
            <div class="form">
				<?php if ($modules['module_chat'] && !$nf) { ?>
                    <a href='/chat?s=<?= $result['steamid64'] ?>' class='btn-btn t-size-md t-1 t-md' target='_blank'><?=$language[$lang]["chatmsg"]?></a>
				<?php } ?>
				<?php if ($modules['module_users'] && !$nf) { ?>
                    <a href='/users?s=<?= $result['ip'] ?>' class='btn-btn t-size-md t-1 t-md' target='_blank'><?=$language[$lang]["iprow"]?></a>
				<?php } ?>
                <a href='/' class='btn-b btn-btn t-size-md t-1 t-md'><?=$language[$lang]["back"]?></a>
            </div>
        </nav>
		<?php if ($modules['module_profile_material'] && $user_admin && !$nf) { ?>
            <nav class='nav-admin'>
                <p class='t-3'><?=$language[$lang]["isadmin"]?></p>
                <div class='profile-block pb-2'>
                    <div class='fl-b'><span class='t-3'><?=$language[$lang]["accesslevel"]?>:</span><span
                                class='t-2'><?= $user_group ?></span></div>
                    <div class='fl-b'><span class='t-3'><?=$language[$lang]["gavemutes"]?>:</span><span
                                class='t-2'><?= $user_admin_comms ?></span></div>
                    <div class='fl-b'><span class='t-3'><?=$language[$lang]["gavebans"]?>:</span><span
                                class='t-2'><?= $user_admin_bans ?></span></div>
                </div>
            </nav>
		<?php } ?>
    </div>
    <section>
		<?php if (!$nf) { ?>
            <div class='left'>
                <div class='profile-block pb-1'>
                    <div>
                        <img class='pb-profile-img' src='<?= $_SESSION['profile_avatar_show'] ?>'>
                    </div>
                    <div class='pb-profile-user'>
                        <div>
                            <h2 class='t-2'><?= strip_tags($result['nick']) ?></h2>
							<?= (($user_activity_online > 0) ? "<p class='t-3 t-size-sm online'>online</p>" : "<p class='t-3 t-size-sm'>offline</p>") ?>
                        </div>
                        <div>
                <span class='t-3 t-size-sm'><?=$language[$lang]["lastactivity"]?>:</h2>
                    <p class='t-2 t-size-sm'><?= date("d.m.Y H:i", $result['activitydate']) ?></p>
                        </div>
                    </div>
                    <div class='pb-icons'>
                        <a target='_blank' href='https://steamcommunity.com/profiles/<?= $result['steamid64'] ?>'><img
                                    src='modules/profile/template/images/steam.svg'></a>
                    </div>
                </div>
                <div class='profile-block pb-2'>
                    <div class='fl-b'><span class='t-3'>UserID</span><span class='t-2'><?= $uid ?></span></div>
                    <div class='fl-b'><span class='t-3'>SteamID</span><span class='t-2'><?= $result['steamid'] ?></span>
                    </div>
                    <div class='fl-b'><span class='t-3'>SteamID64</span><span
                                class='t-2'><?= $result['steamid64'] ?></span></div>
                    <div class='fl-b'><span class='t-3'>IP</span><span class='t-2'><?= $result['ip'] ?></span></div>
                </div>
                <div class='profile-block pb-2'>
                    <div class='fl-b'><span class='t-3'><?=$language[$lang]["daysofproject"]?></span><span class='t-2'><?= $user_days ?></span>
                    </div>
                    <div class='fl-b'><span class='t-3'><?=$language[$lang]["minplayed"]?></span><span
                                class='t-2'><?= round($user_minutes) ?></span></div>
                    <div class='fl-b'><span class='t-3'><?=$language[$lang]["datereg"]?></span><span
                                class='t-2'><?= date("d.m.Y H:i", $result['regdate']) ?></span></div>
                    <div class='fl-b'><span class='t-3'><?=$language[$lang]["lastactivityfull"]?></span><span
                                class='t-2'><?= date("d.m.Y", $result['activitydate']) ?></span></div>
                </div>
            </div>

            <div class='right'>
				<?php if ($modules['module_chat']) {
				$STM2 = $pdo2->query("SELECT SP_users.nick AS nick, SP_users_chat.msg, SP_users_chat.date, SP_users.steamid64 AS steamid64 FROM SP_users_chat JOIN SP_users ON SP_users_chat.uid = SP_users.id WHERE uid = $uid ORDER BY date DESC LIMIT 30");
				?>
                <div class='profile-block pb-3'>
                    <span class='t-3'><?=$language[$lang]["lastchatmsg"]?>:</span>

					<?php while ($row = $STM2->fetch()) { ?>

                        <div class='message'>
                            <div class='title'>
                                <img src='<?= $_SESSION['profile_avatar_show'] ?>'>
                                <p class='t-3 t-size-sm t-md'><?= date('H:i', $row[2]); ?></p>
                            </div>
                            <div class='m-show'>
                                <a href='/profile?id=<?= $row[3] ?>'>
                                    <span class='t-2 t-size-md nick'><?= strip_tags($row[0]) ?></span>
                                </a>
                                <span class='t-3 t-size-sm t-md'><?= date('d.m.y', $row[2]); ?></span>
                                <p class='text-message t-size-md t-3'><?= strip_tags($row[1]) ?></p>
                            </div>
                        </div>

					<?php }
					} ?>

                </div>
				<?php if ($modules['module_profile_material']) { ?>
                    <div class='profile-block pb-2'>
                        <div class='fl-b'><span class='t-3'><?=$language[$lang]["getbans"]?>:</span><span
                                    class='t-2'><?= $user_bans ?></span></div>
                        <div class='fl-b'><span class='t-3'><?=$language[$lang]["getmutes"]?>:</span><span
                                    class='t-2'><?= $user_comms ?></span></div>
                    </div>
				<?php } ?>
            </div>
		<?php } else {
			echo '<p class="t-4">'.$language[$lang]["profilenotfound"].'</p>';
		} ?>
    </section>
</main>
