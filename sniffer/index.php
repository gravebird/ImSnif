<?php
	include_once("conf.php");
	if (empty($_GET)) { header("Location: ?page=upload"); }
	if (isset($_GET['act']) && $_GET['act'] == 'upload') {
		$whitelist = array('.gif', '.jpg', '.jpeg', '.png');
		$imageinfo = getimagesize($_FILES['image']['tmp_name']);
		if( $imageinfo['mime'] != 'image/gif' &&
			$imageinfo['mime'] != 'image/jpeg' &&
			$imageinfo['mime'] != 'image/png'
		) {
			echo 'Пардон, но Вы можете загружать только изображения.';
			exit;
		} else {
			$filemime = $imageinfo['mime'];
			foreach ($whitelist as $item) {
				if(preg_match("/$item\$/i", $_FILES['image']['name'])) {
					$filetype = $item;
					break;
				}
			}
			if (empty($filetype)) {
				echo 'Пардон, но Вы можете загружать только изображения.';
				exit;
			}
		}
		
		$ImageLink=$_FILES['image']['tmp_name'];
		$OpenImage=fopen($ImageLink, 'rb');
		$ImageRead=fread($OpenImage, filesize($ImageLink));
		$ImageBase64=base64_encode($ImageRead);
		fclose($OpenImage);
		
		$filename = $_POST['filename'];
		$complitesnif =
"<?php
	@ob_clean();
	include(\"../snifconf.php\");
	header(\"Content-type: ".$filemime."\");
	echo base64_decode(\"".$ImageBase64."\");
	if (empty(\$_GET) || !isset(\$_GET['showinlist'])) {
		\$file = '".$filename.$filetype."';
		include(\"../SxGeo.php\");
		\$ip = GetRealIp();
		\$SxGeo = new SxGeo('..\SxGeoCity.dat', SXGEO_BATCH | SXGEO_MEMORY);
		\$city = \$SxGeo->get(\$ip);
		\$ui = userinfo();
		\$compliteinfo = \"[\".date('j.d.Y G:i').\"]\\n\tIP: \".\$ip.\"\\n\tCity: \".\$city['country'].\"\\n\tUser agent: \".\$ui[0].\"\n\tOS: \".\$ui[1].\"\\n[----------]\\n\\n\";
		file_put_contents(\$statfolder.\$file.'.txt', file_get_contents(\$statfolder.\$file.'.txt').\$compliteinfo);
	}
?>";
		if (!is_file($snifferscat.$filename.$filetype.'.php')) {
			file_put_contents($snifferscat.$filename.$filetype.'.php', $complitesnif);
			header("Location: index.php?page=list");
			file_put_contents($snifstatcat.$filename.$filetype.'.txt', "");
		} else {
			echo 'Пардон, но файл с таким именем и расширением уже существует.';
			exit();
		}
	}
?>
<html>
	<head>
		<title>Сниффер же</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<style>
		table {
			border-collapse:collapse;
		}
		table,th, td {
			border: 1px solid black;
		}
		textarea, iframe {
			width: 350; height: 300;
			border: 0;
			overflow: auto;
		}
		</style>
	</head>
	<body>
		<p>
			<a href="?page=upload">Загрузить сниффер</a> |
			<a href="?page=list">Список снифферов</a>
		</p>
<? if (isset($_GET['page']) && $_GET['page'] == 'upload') { ?>
		<p>
			<form method="post" action="?act=upload" enctype="multipart/form-data">
				Выберите файл (jpeg/png/gif):<br>
				<input type="file" name="image"><br>
				Имя файла (без расширения):<br>
				<input name="filename"><br>
				<input type="submit" name="up" value="Загрузить!">
			</form>
		</p>
<? } elseif (isset($_GET['page']) && $_GET['page'] == 'list') { ?>
		<table>
	<?php
		if ($handle = opendir($snifferscat)) {
			while (false !== ($file = readdir($handle))) { 
				if ($file != "." && $file != ".." && $file != "index.php") { 
				preg_match("/(.*).php/", $file, $imgname);
					echo "
			<tr>
				<td>
				Изобржаение: <a href=\"http://$siteaddr/$snifferscat$imgname[1]\">$imgname[1] <br>
					<iframe src=\"$snifferscat$file?showinlist\"></iframe>
				</td>
				<td>
				Статистика: $imgname[1].txt <br>
					<textarea readonly>".file_get_contents("$snifstatcat$imgname[1].txt")."</textarea>
				</td>
			</tr>

";
				} 
			}
			closedir($handle); 
		}
	?>
		</table>
<? } ?>
	</body>
</html>