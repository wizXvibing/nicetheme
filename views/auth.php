<?php
if (isset($_COOKIE["tokeq"]) && !empty($_COOKIE["tokeq"])) {
	$STM = $pdo->prepare("SELECT steamid64, role FROM syspanel_users WHERE hash = ? LIMIT 1");
	$STM->execute([$_COOKIE["tokeq"]]);
	$result = $STM->fetch();

	if ($result) {
		$_SESSION['steamdata']['steamid'] = $result['steamid64'];
		$_SESSION['role'] = $result['role'];

		if ($_SESSION['role'] > 0) {
			$s->forceReload();
			$_SESSION['profile_avatar'] = $s->avatarmedium;

			header("Location: /");
			exit;
		}
	}
}
?>


<div class="c-block">
    <header>
        <img class="logo" src="public/images/logo.svg" alt="Logotype">
    </header>
	<?php if ($s->loggedIn()) { ?>
        <p class="t-4"><?=$language[$lang]["role_0"]?></p>
        <main>
            <a href="?logout">
                <div class="btn">
                    <span class="t-1"><?=$language[$lang]["logout"]?></span>
                </div>
            </a>
        </main>
	<?php } else { ?>
        <main>
            <a href="<?= $s->loginUrl(); ?>">
                <div class="btn">
                    <span class="t-1"><?=$language[$lang]['login']?></span>
                    <img class="logo" src="public/images/icon_steam.svg" alt="Logotype">
                </div>
            </a>
        </main>
	<?php } ?>
    <footer>
        <p class="t-sm t-3">SystemPanel</p>
        <p class="t-sm t-3">Web By AUTHTERN</p>
        <p class="t-sm t-3"><span class="nicetext">Nice Theme</span> By wizard</p>
    </footer>
</div>
