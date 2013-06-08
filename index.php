<?php
/* --------------------------------------------------------------------------------
 * One File Install (OFI) for WordPress - 0.1
 * 
 * License GNU/LGPL - Xaver Birsak (revaxarts.com) 2013
 * http://revaxarts.com
 * --------------------------------------------------------------------------------
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  error_reporting(0);
	set_time_limit(0);
	
	$required_php = '5.2.4';
	$required_mysql = '5.0';
	$php_version = PHP_VERSION;
	
	if(function_exists('mysql_get_server_info')){
		$mysql_version = mysql_get_server_info();
	}else if(function_exists('mysqli_get_server_info')){
		$mysql_version = mysqli_get_server_info();
	}else if(function_exists('mysql_get_client_info')){
		$mysql_version = mysql_get_client_info();
	}

	if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
		
		$check = isset($_POST['check']) ? $_POST['check'] : false;
		
		if($check){
		
			switch($check){
				
				case 'phpversion';
					die(version_compare($php_version, $required_php, '>='));
					
				case 'mysqlversion';
					die(version_compare($mysql_version, $required_mysql, '>='));
				
				case 'curl';
					die(in_array('curl', get_loaded_extensions()));
				
				case 'pclzip';
					$d = download('https://raw.github.com/revaxarts/pclzip/master/pclzip.lib.php', 'pclzip.lib.php');
					die($d);
				
				case 'wpversions';
					$d = file_get('http://wordpress.org/download/release-archive/');
					
					preg_match_all('#<td>([^<]+)<\/td>#', $d, $matches);
					
					$return = array(
						'stable' => array(),
						'betas' => array(),
						'multi' => array(),
					);
					foreach($matches[1] as $version){
						if(preg_match('#beta|RC#', $version)){
							$return['betas'][] = $version;
						}else if(preg_match('#MU#', $version)){
							$return['multi'][] = $version;
						}else{
							$return['stable'][] = $version;
						}
					}
					
					die(json_encode($return));
			}
			
		}
		
		if(isset($_POST['download'])){
			$d = download('http://wordpress.org/'.(isset($_POST['version']) ? 'wordpress-'.$_POST['version'] : 'latest').'.zip', 'wp.zip');
			die($d);
		}
		
		if(isset($_POST['unzip'])){
		
				error_reporting(E_ALL);
				require('pclzip.lib.php');

				unlink('index.php');

				$archive = new PclZip('wp.zip');
				$archive->extract(PCLZIP_CB_POST_EXTRACT, 'extract_callback');

				unlink('pclzip.lib.php');
				unlink('wp.zip');
				rrmdir('wordpress');
				
				die(file_exists('wp-load.php'));
			
		}
		
		die(0);
		
	}
	

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>One File Install for WordPress</title>
	<link rel='stylesheet' id='wp-admin-css'  href='http://s1.wp.com/wp-admin/css/wp-admin.min.css' type='text/css' media='all' />
	<link rel='stylesheet' id='wp-admin-css'  href='http://s1.wp.com/_static/??/wp-includes/css/buttons.min.css,/wp-admin/css/colors-fresh.min.css' type='text/css' media='all' />
	<link rel="shortcut icon" href="http://s.wordpress.org/favicon.ico" type="image/x-icon" />
	<meta name='robots' content='noindex,nofollow' />
	<style>
	.login h1{
		font-size: 1em;
		line-height: 1em;
		height: 70px;
	}
	.login h1 a{
		text-indent: 0;
		text-align: right;
		font-size: 12px;
		text-decoration: none;
		padding-top: 50px;
	}
	.stat, .stat-ok, .stat-no{
		float: right;
		display: block;
		width: 16px;
		height: 16px;
		border-radius: 50%;
		text-indent: -9999px;
		border: 1px solid #D5D5D5;
		-webkit-box-shadow: rgba(200, 200, 200, 0.7) 0 1px 1px -1px;
		box-shadow: rgba(200, 200, 200, 0.7) 0 1px 1px -1px;
	}
	.stat-init{
		background: #eeeeee;
		background: -moz-linear-gradient(top,  #eeeeee 0%, #cccccc 100%);
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#eeeeee), color-stop(100%,#cccccc));
		background: -webkit-linear-gradient(top,  #eeeeee 0%,#cccccc 100%);
		background: -o-linear-gradient(top,  #eeeeee 0%,#cccccc 100%);
		background: -ms-linear-gradient(top,  #eeeeee 0%,#cccccc 100%);
		background: linear-gradient(to bottom,  #eeeeee 0%,#cccccc 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#eeeeee', endColorstr='#cccccc',GradientType=0 );
	
	}
	.stat-ok{
		background: #f8ffe8;
		background: -moz-linear-gradient(top,  #f8ffe8 0%, #e3f5ab 33%, #b7df2d 100%);
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f8ffe8), color-stop(33%,#e3f5ab), color-stop(100%,#b7df2d));
		background: -webkit-linear-gradient(top,  #f8ffe8 0%,#e3f5ab 33%,#b7df2d 100%);
		background: -o-linear-gradient(top,  #f8ffe8 0%,#e3f5ab 33%,#b7df2d 100%);
		background: -ms-linear-gradient(top,  #f8ffe8 0%,#e3f5ab 33%,#b7df2d 100%);
		background: linear-gradient(to bottom,  #f8ffe8 0%,#e3f5ab 33%,#b7df2d 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f8ffe8', endColorstr='#b7df2d',GradientType=0 );
	}
	.stat-no{
		background: #ff3019;
		background: -moz-linear-gradient(top,  #ff3019 0%, #cf0404 100%);
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ff3019), color-stop(100%,#cf0404));
		background: -webkit-linear-gradient(top,  #ff3019 0%,#cf0404 100%);
		background: -o-linear-gradient(top,  #ff3019 0%,#cf0404 100%);
		background: -ms-linear-gradient(top,  #ff3019 0%,#cf0404 100%);
		background: linear-gradient(to bottom,  #ff3019 0%,#cf0404 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff3019', endColorstr='#cf0404',GradientType=0 );
	}
	
	.login ul li{
		display: block;
		border: 1px;
		margin: 10px 0;
	}
	p{
		color: #777;
		font-size: 14px;
		line-height: 1.4em;
	}
	select#version{
		width: 100%;
		margin-bottom: 20px;
	}
	.submit, .invalid{
		margin-top: 20px;
		display: none;
	}
	.login small{
		padding: 10px;
		display: block;
		text-align: center;
	
	}
	</style>
</head>
<body class="login wp-core-ui">
	<div id="login">
		<h1><a href="http://wordpress.org/" title="Powered by WordPress">One File Install</a></h1>
<form method="post">
	<?php
		$valid = true;
	?>
	<ul>
		<li class="check" data-check="phpversion"><label>PHP Version: <strong><?php echo $php_version ?></strong></label> <span class="stat"></span></li>
		<li class="check" data-check="mysqlversion"><label>MySQL Version: <strong><?php echo $mysql_version ?></strong></label> <span class="stat"></span></li>
		<li class="check" data-check="curl"><label>cURL </label> <span class="stat"></span></li>
		<li class="check" data-check="pclzip"><label>PCLZip: </label> <span class="stat"></span></li>
		<li class="check" data-check="wpversions"><label>get WP versions: </label> <span class="stat"></span></li>
	</ul>
	<ul>
		<li class="download"><label>Download <strong></strong></label> <span class="stat"></span></li>
		<li class="unzip"><label>Unzip <strong></strong></label> <span class="stat"></span></li>
	</ul>
	
	<p class="submit">
		<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Get this WordPress version" />
	</p>
	<div class="invalid">
	<h3>
		Can't install WordPress with your server configuration!
		WordPress requires at least
	</h3>
	</div>
</form>
	<small><acronym title="One File Install">OFI</acronym> for <a href="http://wordpress.org">WordPress</a> by <a href="http://revaxarts.com">revaxarts</a> <?php echo date('Y') ?></small>
	</div>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script>
$(function() {

	var tocheck = $('li.check'),
		valid = true;
	
	jQuery.fn.shift = [].shift;	
	
	$('form').on('submit', function(){
		
		$('li.download').find('span.stat').addClass('stat-init');
		
		$.post('<?php echo basename(__FILE__)?>', {
				download: true,
				version: $('#version').val()
			}, function(response) {
			
			if(response){
				$('li.download').find('span.stat').addClass('stat-ok');
				$.post('<?php echo basename(__FILE__)?>', {
						unzip: true
					}, function(response) {
					
					if(response){
						$('li.unzip').find('span.stat').addClass('stat-ok');
						setTimeout(function(){
							location.href = './';
						}, 1000);
					}else{
						$('li.unzip').find('span.stat').addClass('stat-no');
					}
				});
			}else{
				$('li.download').find('span.stat').addClass('stat-no');
			}
				
		});
		return false;
	});
	
	check();
	
	function check(){
		if(!tocheck.length){
			if(valid){
				$('.submit').show();
			}else{
				$('.invalid').show();
			}
			return;
		};
		
		var next = $(tocheck.shift()),
			nextcheck = next.data('check');
		
		next.find('span.stat').addClass('stat-init');
		
		$.post('<?php echo basename(__FILE__)?>', {
				check: nextcheck
			}, function(response) {
				
			if(response){
				next.find('span.stat').addClass('stat-ok');
				if(nextcheck == 'wpversions'){
					var data = $.parseJSON(response);
					var select = '<p><label>Select WordPress version<select id="version">';
					
					for(type in data){
						select += '<optgroup label="'+type+'">';
						for(e in data[type]){
							select += '<option'+((type == 'stable' && e == data[type].length-1) ? ' selected' : '')+' value="'+data[type][e]+'">'+data[type][e]+'</option>';
							console.log(data[type][e]);
						}
						select += '</optgroup>';
					}
					
					select += '</select></label><div class="clear"></div></p>';
					$(select).prependTo('.submit');
				}
			}else{
				next.find('span.stat').addClass('stat-no');
				valid = false;
			}
			check();
		});
		
	}
	
});
</script>

	<div class="clear"></div>
</body>
</html>
	
	
<?php

	function download($url, $to){
		if(!file_exists($to)){
			$data = file_get($url);
			
			if($data){
				$fp = fopen($to, "w+");
				if(fwrite($fp, $data)){
					fclose($fp);
					return true;
				}else{
					fclose($fp);
					@unlink($to);
					return false;
				}
			}
		}
		
		return true;
	}
	
	function file_get($address) {
		if (in_array('curl', get_loaded_extensions())) {
			$cp = curl_init($address);
			
			curl_setopt($cp, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($cp, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($cp, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($cp, CURLOPT_SSL_VERIFYPEER, false);
			
			
			$result = curl_exec($cp);
			
			if ($result === false){
				fclose($fp);
				$error = curl_error($cp);
				return false;
			}else{
				fclose($fp);
			}
			curl_close($cp);
			
			return $result;

		}else{
			return false;
		}
	}
	
	function extract_callback($p_event, &$p_header) {
		
		$current_dir = dirname(__FILE__);
		$current_perms = substr(sprintf('%o', fileperms($current_dir)), -4);
		if ($current_perms < 755)
		{
			$current_perms = '0755';
		}
		chmod($current_dir . DIRECTORY_SEPARATOR . $p_header['filename'], octdec($current_perms));
		
		$newfile = substr($p_header['filename'], 10);
		
		if(!is_dir(dirname($newfile))) mkdir(dirname($newfile), 0755, true);
		
		if(is_file($p_header['filename'])){
		
			if(copy($p_header['filename'], $newfile)){
				unlink($p_header['filename']);
			}
			
		}
		
		return 1;
	}
	
	function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
			}
		}
			reset($objects);
			rmdir($dir);
		}
	}
