<?php
require_once '../../inc/modules_config.php';
require_once '../../app/appMessages.php';
include_once '../../app/appPagination.php';
require_once '../../modules/db.php';
require_once '../../inc/db.php';

require '../../app/appSteam.php';
require '../../inc/steam_config.php';
$s = new steam($steam_api);

function GetAvatar($arr, $steam64) {
	foreach($arr as $item) {
		if(strcmp($steam64, $item["steamid"]) == 0)
			return $item["avatarmedium"];
	}
	
	return 'modules/users/template/images/icon_user_profile.svg';
}

$search = $_POST['search'];
$start = strtotime($_POST['start']);

$end = strtotime($_POST['end']);
$type_id = $_POST['type_id'];
$type_srv = $_POST['type_srv'];

$baseURL = 'actions.php';
$limit = 10;

$end = ($start == $end) ? strtotime('now') : $end;

$time = strtotime($type_day);
if (empty($time)) $time = 0;

$type_list = array(
	'connect',
	'disconnect',
);

$srv = array();
$STM = $pdo->query("SELECT server_id, server_shortname FROM syspanel_servers ORDER BY server_id");
while ($row = $STM->fetch()) {
	$srv[$row[0]] = $row[1];
}

$search_server = (empty($type_srv) || !is_numeric($type_srv)) ? '' : 'AND server = ' . $type_srv;
$search_type = (empty($type_id) || !in_array($type_id, $type_list)) ? 'connect' : $type_id;
$stype = ($search_type == 'connect') ? $language[$lang]["connected"] : $language[$lang]["disconnected"];
$search_time = (empty($start) || !is_numeric($start) || empty($end) || !is_numeric($end)) ? "AND $search_type > 0" : "AND $search_type > $start AND $search_type < $end";
$search_time_fp = (empty($start) || !is_numeric($start) || empty($end) || !is_numeric($end)) ? "$search_type > 0" : "$search_type > $start AND $search_type < $end";

$STM2 = $pdo2->prepare("SELECT COUNT(*) FROM SP_users_visits INNER JOIN SP_users ON SP_users.id = SP_users_visits.uid WHERE CONCAT(nick, geo, SP_users_visits.ip, SP_users.ip, steamid, steamid64) like ? $search_time $search_server");
$STM2->execute(array("%" . $search . "%"));
$rowCount = $STM2->fetch()[0];

$STM5 = $pdo2->prepare("SELECT SP_users_visits.server, SP_users_visits.connect, SP_users_visits.ip, SP_users_visits.geo, SP_users.nick, SP_users.steamid64, SP_users_visits.disconnect, SP_users.steamid FROM SP_users_visits INNER JOIN SP_users ON SP_users.id = SP_users_visits.uid WHERE CONCAT(nick, geo, SP_users_visits.ip, SP_users.ip, steamid, steamid64) like ? $search_time $search_server ORDER BY $search_type DESC LIMIT 0,$limit");
$STM5->execute(array("%" . $search . "%"));

$offset = (!empty($_POST['page']) && is_numeric($_POST['page'])) ? $_POST['page'] : 0;

$pagConfig = array(
	'baseURL' => $baseURL,
	'totalRows' => $rowCount,
	'perPage' => $limit,
	'currentPage' => $offset,
	'contentDiv' => 'dataResult',
	'link_func' => 'searchFilter'
);
$pagination = new Pagination($pagConfig);

if ($_POST['page'] == 'undefined') {
	$visits_time = 0;
	$STM2 = $pdo2->query("SELECT COUNT(*) FROM SP_users_visits WHERE $search_type > $start AND  $search_type < $end $search_server AND now = 1");
	$s_newplayers = $STM2->fetch();
	$STM2 = $pdo2->query("SELECT COUNT(*) FROM SP_users_visits WHERE $search_type > $start AND  $search_type < $end $search_server");
	$s_ollvisits = $STM2->fetch();
	$STM2 = $pdo2->query("SELECT connect, disconnect FROM SP_users_visits WHERE $search_type > $start AND  $search_type < $end $search_server");
	while ($row = $STM2->fetch()) {
		if ($row[1] > 0) {
			$visits_time = $visits_time + ($row[1] - $row[0]) / 60 / 60;
		}
	}

	$STM2GT = $pdo2->query("SELECT connect, players, now, disconnect FROM SP_users_visits WHERE $search_time_fp $search_server ORDER BY connect ASC");
	$GT = array();
	while ($row = $STM2GT->fetch()) {
		$GT[date("m/d/Y", $row[0])][1]++;
		if ($row[2] == 1) {
			$GT[date("m/d/Y", $row[0])][2]++;
		}
		$GT[date("m/d/Y", $row[0])][3] += $row[1];
		if ($row[3] > 0) {
			$GT[date("m/d/Y", $row[0])][4] += ($row[3] - $row[0]) / 60 / 60;
		}
	}

	$atp = json_encode($GT);

	echo "
        <script>

        gt = []; arr1 = []; arr2 = []; arr3 = []; arr4 = []; arr5 = [];
        gt = $atp;
        for (let key in gt) {
            arr1.push(key.slice(0, -5));
            arr2.push(gt[key][1]);
            arr3.push(gt[key][2]);
            arr4.push(Math.round(gt[key][3]/gt[key][1]));
            arr5.push(Math.round(gt[key][4]));
        }

        var newplayers = '$s_newplayers[0]';
        var ollvisits = '$s_ollvisits[0]';
        var visits_time = Math.ceil('$visits_time');

        $('#newplayers').html(newplayers);
        $('#ollvisits').html(ollvisits);
        $('#visits_time').html(visits_time+' ".$language[$lang]["hours"]."');


        </script>
      ";
} else {
	$STM5 = $pdo2->prepare("SELECT SP_users_visits.server, SP_users_visits.connect, SP_users_visits.ip, SP_users_visits.geo, SP_users.nick, SP_users.steamid64, SP_users_visits.disconnect, SP_users.steamid FROM SP_users_visits INNER JOIN SP_users ON SP_users.id = SP_users_visits.uid WHERE CONCAT(nick, geo, SP_users_visits.ip, SP_users.ip, steamid, steamid64) like ? $search_time $search_server ORDER BY $search_type DESC LIMIT $offset,$limit");
	$STM5->execute(array("%" . $search . "%"));
}

?>

<div id='dataResult'>
    <table>
        <thead>
        <tr>
            <th></th>
            <th><?=$language[$lang]["date"]?></th>
            <th><?=$language[$lang]["country"]?></th>
            <th><?=$language[$lang]["srv"]?></th>
            <th><?=$language[$lang]["nick"]?></th>
            <th><?=$language[$lang]["action"]?></th>
            <th>IP</th>
            <th><?=$language[$lang]["session"]?></th>
        </tr>
        </thead>
        <tbody>
		<?php
		
		$data = $STM5->fetchAll();
		foreach($data as $item) {
			$steamids = $steamids . $item["steamid64"] . ",";
		}
		
		//var_dump($steamids);
		
		$steamids = substr($steamids, 0, -1);
		if ($steamids) $pArrayUsers = $s->getImageList($steamids);
		
		foreach($data as $row) {
			?>

            <tr>
                <td><a href='profile?id=<?= $row[5] ?>'><img src='<?= GetAvatar($pArrayUsers, $row[5]) ?>'></a>
                </td>
                <td><?= date("d.m", $search_type == 'connect' ? $row[1] : $row[6]); ?><span
                            class='t-xl t-size-xl'> <?= date("H:i", $search_type == 'connect' ? $row[1] : $row[6]); ?></span>
                </td>
                <td><?= (empty($row[3]) ? '-' : $row[3]) ?></td>
                <td><?= $srv[$row[0]] ?></td>
                <td><?= strip_tags($row[4]) ?></td>
                <td><?= $stype ?></td>
                <td><?= $row[2] ?></td>
                <td><?= (($row[6] > 0) ? round(($row[6] - $row[1]) / 60) . ' '.$language[$lang]["minutes"] : '-') ?></td>
            </tr>
			<?php
		}

		if ($STM5->rowCount() == 0) {
			echo "<tr><td colspan='8' style='text-align:center;' class='t-4'>".$language[$lang]["notresult"]."</td></tr>";
		}
		?>
        </tbody>
    </table>
	<?php echo $pagination->createLinks(); ?>
</div>