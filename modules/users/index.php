<?php
require_once 'inc/modules_config.php';
if (!($modules['module_users'])) {
	header('Location: /404');
	exit;
}
include_once 'app/appPagination.php';
require_once 'modules/db.php';

$baseURL = 'actions.php';
$limit = 15;

$search = (isset($_GET['s']) && !empty($_GET['s'])) ? $_GET['s'] : "";

$STM2 = $pdo2->prepare("SELECT COUNT(*) FROM SP_users WHERE CONCAT(id,nick,steamid,steamid64,ip) LIKE ?");
$STM2->execute(array("%" . $search . "%"));

$row = $STM2->fetch();
$rowCount = $row[0];

$pagConfig = array(
	'baseURL' => $baseURL,
	'totalRows' => $rowCount,
	'perPage' => $limit,
	'contentDiv' => 'dataResult',
	'link_func' => 'searchFilter'
);

$pagination = new Pagination($pagConfig);

//$STM2 = $pdo2->prepare("SELECT * FROM SP_users WHERE CONCAT(id,nick,steamid,steamid64,ip) LIKE ? ORDER BY id ASC LIMIT $limit");
$STM2 = $pdo2->prepare("SELECT SP_users.id, SP_users.nick, SP_users.steamid, SP_users.steamid64, SP_users.ip, SP_users_activity.online FROM SP_users LEFT JOIN SP_users_activity ON SP_users.id=SP_users_activity.uid
	WHERE CONCAT(SP_users.id,SP_users.nick,SP_users.steamid,SP_users.steamid64,SP_users.ip) LIKE ? ORDER BY SP_users.id ASC LIMIT $limit");
$STM2->execute(array("%" . $search . "%"));
?>

<link rel="stylesheet" href="modules/users/template/styles/styles.css">
<script type="text/javascript" src="modules/users/ajax/ajax.js"></script>

<main>
    <nav>
        <div class="form">
            <input class='inp-ul' type="text" placeholder="<?=$language[$lang]["inputchat"]?>" id="search">
            <a href='/' class='btn-b btn-btn t-size-md t-1 t-md'><?=$language[$lang]["back"]?></a>
        </div>
    </nav>
    <section>
        <div class='section-table'>
            <div id='dataResult'>
                <table>
                    <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th><?=$language[$lang]["nick"]?></th>
                        <th>SteamID</th>
                        <th>SteamID64</th>
                        <th>IP</th>
                    </tr>
                    </thead>
                    <tbody>
					<?php
					while ($row = $STM2->fetch()) {
                        $steamids = $steamids . $row[3] . ","
						?>
                        <tr>
                            <td id='<?= $row[3] ?>'><a href='profile?id=<?= $row[3] ?>'>
								<div class="temp">
									<img src='modules/users/template/images/icon_user_profile.svg' class="img-circle-small">
									<span class="<?= $row[5] ? 'status-green' : 'status-red' ?> bottomRight">&nbsp</span>
								</div>
								</a></td>
                            <td><?= $row[0] ?></td>
                            <td><?= strip_tags($row[1]) ?></td>
                            <td><?= $row[2] ?></td>
                            <td><?= $row[3] ?></td>
                            <td><?= $row[4] ?></td>
                        </tr>
						<?php
					}
                    $steamids = substr($steamids, 0, -1);
                    if ($steamids) $pArrayUsers = $s->getImageList($steamids);

					if ($STM2->rowCount() == 0) {
						echo "<tr><td colspan='7' style='text-align:center;' class='t-4'>".$language[$lang]["notresult"]."</td></tr>";
					}
					?>
                    </tbody>
                </table>
				<?php echo $pagination->createLinks(); ?>
            </div>
        </div>

    </section>
</main>

<script>
    let spisokid;
	let img;
    window.onload = function () {
        let ar = <?php echo json_encode($pArrayUsers); ?>;
        if (ar) {
            let block = document.querySelectorAll('td');

            block.forEach((el, idx) => {
                let sid = el.id;
                for (let i = 0; i < ar.length; i++) {
                    if (ar[i].steamid == sid) {
                        spisokid = i;
                        break;
                    }
                }
                
				img = el.querySelector('img');
				if(img)
					img.setAttribute('src', ar[spisokid]['avatarmedium']);
            });
        }
    }

    let src2;
    let doc = document.getElementById('search');
    doc.addEventListener('keyup', function (event) {
        if (event.which === 13 && doc.value && doc.value !== src2) {
            event.preventDefault();
            src2 = doc.value;
            searchFilter();
        }
    });

</script>