<?php
include 'login.php';

header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past

require_once 'settings-template.php';
if (file_exists('settings.php')) require_once 'settings.php';
require_once 'functions.php';

global $action, $id;

$groupmode = !isset($_COOKIE['c_filemode']);

if (isset($_REQUEST['filemode'])) {
	$groupmode = False;
	SetCookie("c_filemode", "1", 0);
}
if (isset($_REQUEST['groupmode'])) {
	$groupmode = True;
	SetCookie("c_filemode", "", time() - 10000); // delete cookie
}

if (isset ($_REQUEST['action'])) {
	$action = $_REQUEST['action'];
	if (isset ($_REQUEST['id']))
	{
		$edittext = '';
		if (isset ($_REQUEST['edittext'])) {
			$edittext = $_REQUEST['edittext'];
		}
		
		GetRequest("editqueue", array($action, (int)$_REQUEST['offset'], $edittext, (int)$_REQUEST['id']));
		if ($action == "groupresume")
			GetRequest("editqueue", array("grouppauseextrapars", (int)$_REQUEST['offset'], '', (int)$_REQUEST['id']));
	}
	else
		GetRequest($action, "");
}

if (isset ($_REQUEST['rate'])) {
	//set max download option
	GetRequest("rate", (int)$_REQUEST['rate']); 
}

if (isset ($_FILES['nzbfile'])) {
	$upload_status = upload_file($_FILES['nzbfile']);
	SetCookie("upload_status", $upload_status, time()+30); // expire in 30 seconds
	Redirect('index.php');
}

if (isset ($_REQUEST['newzbinid'])) {
	//get nzb from newzbin
	$newzbin_status = FetchFromNewzbin($_REQUEST['newzbinid']); 
	SetCookie("newzbin_status", $newzbin_status, time()+30); // expire in 30 seconds
	Redirect('index.php');
}

$page = 0;
if (isset($_REQUEST['page'])) {
	$page = $_REQUEST['page'];
	SetCookie("c_page", $page, 0);
} else if (isset($_COOKIE['c_page'])) {
	$page = (int)$_COOKIE['c_page'];
}

$logpage = 0;
if (isset($_REQUEST['logpage'])) {
	$logpage = $_REQUEST['logpage'];
	SetCookie("c_logpage", $logpage, 0);
} else if (isset($_COOKIE['c_logpage'])) {
	$logpage = (int)$_COOKIE['c_logpage'];
}

$postlogpage = 0;
if (isset($_REQUEST['postlogpage'])) {
	$postlogpage = $_REQUEST['postlogpage'];
	SetCookie("c_postlogpage", $postlogpage, 0);
} else if (isset($_COOKIE['c_postlogpage'])) {
	$postlogpage = (int)$_COOKIE['c_postlogpage'];
}

$rpc_api = GetAvailableApi();
//echo "<!-- API: $rpc_api -->\n";
if (!isset($rpc_api)) {
	echo "NZBGetWeb: Could not find required extension or library. Consult README-file for installation instructions.";
	Exit(-1);
}

$wantstart = false;
$connected = false;
$phpvars = null;

if (isset($_REQUEST['start']) && $ServerStartCommand != '') {
	$wantstart = true;
}

if (!$wantstart) {
	$phpvars = GetInfo($groupmode);
	$connected = !IsConnectError($phpvars);
}

function add_category_combo($category, $id, $paused) {
	global $Categories;
	
	if ($category == '' && count($Categories) == 0) {
		return;
	}
	
	$catfound = false;

	echo '<select class="'.($paused ? 'pausedcategorycombo' : 'categorycombo').'" onchange="javascript:updatestatus(\'status.php?action=groupsetcategory&edittext=\' + this.options[this.selectedIndex].text + \'&offset=-1&id='.$id.'\')">';
	foreach ($Categories as $cat) {
		if ($cat==$category) {
			echo "<option selected='selected'>$cat</option>";
			$catfound = true;
		} else {
			echo "<option>$cat</option>";
		}
	}
	
	if (!$catfound) {
		echo "<option value ='$category' selected='selected'>$category</option>";
	}
	
	echo '</select> ';
}

function currently_downloading ($phpvars) {
	echo '<div class = "block">';
	if (isset($phpvars['activegroup'])) {
		//Download in progress, display info
		$cur_queued=$phpvars['activegroup'];
		if (!$phpvars['status']['ServerPaused'])
			echo '<center>Currently downloading</center><br>';
		else
			echo '<center>Currently downloading (pausing)</center><br>';

		echo '<table width="100%">';
		echo '<tr><td colspan="7"></td><td>name</td><td width="20">category</td><td width="100" align="right">download rate</td><td width="60" align="right">left</td><td width="100" align="right">remaining time</td></tr>';

		echo '<tr class="unpausedgroup">';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=groupdelete&offset=0&id='.$cur_queued['LastID'].'\')"><IMG src=images/cancel.gif width=15 height=15 alt="remove nzb" title="remove nzb"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=groupmovetop&offset=0&id='.$cur_queued['LastID'].'\')"><IMG src=images/first.gif width=15 height=15 alt="move nzb to top in queue" title="move nzb to top in queue"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=groupmoveoffset&offset=-1&id='.$cur_queued['LastID'].'\')"><IMG src=images/up.gif width=15 height=15 alt="move nzb up" title="move nzb up"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=groupmoveoffset&offset=1&id='.$cur_queued['LastID'].'\')"><IMG src=images/down.gif width=15 height=15 alt="move nzb down" title="move nzb down"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=groupmovebottom&offset=0&id='.$cur_queued['LastID'].'\')"><IMG src=images/last.gif width=15 height=15 alt="move nzb to bottom in queue" title="move nzb to bottom in queue"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=grouppause&offset=0&id='.$cur_queued['LastID'].'\')"><IMG src=images/pause.gif width=15 height=15 alt="pause nzb" title="pause nzb"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=groupresume&offset=0&id='.$cur_queued['LastID'].'\')"><IMG src=images/resume.gif width=15 height=15 alt="resume nzb" title="resume nzb"></a></td>';
		echo "<td>".namereplace($cur_queued['NZBNicename'])."</td>";
		echo '<td width="20">';
		add_category_combo($cur_queued['Category'], $cur_queued['LastID'], false);
		echo '</td>';
		echo "<td align='right'>".round0($phpvars['status']['DownloadRate']/1024)." KB/s</td>";
		echo "<td align='right'>".formatSizeMB($cur_queued['RemainingSizeMB']-$cur_queued['PausedSizeMB'])." </td>";
		if ($phpvars['status']['DownloadRate'] > 0)
			echo "<td align='right'>".sec2hms(($cur_queued['RemainingSizeMB']-$cur_queued['PausedSizeMB'])/($phpvars['status']['DownloadRate']/1024/1024))."</td>";
		else
			echo "<td align='right'></td>";
		echo '</tr>';

		echo '<tr><td colspan="7"><td colspan="3" class="progress">';
		$a=$cur_queued['FileSizeMB']-$cur_queued['PausedSizeMB'];
		if ($a > 0)
			$percent_complete=round0(($a-($cur_queued['RemainingSizeMB']-$cur_queued['PausedSizeMB']))*100/$a);
		else
			$percent_complete=100;
		echo "<IMG src=images/pbar.gif height=12 width=".$percent_complete."%>";
		echo "<td>".$percent_complete."%</td>";
		echo '</tr>';

		echo '</table>';
	}
	else {
		echo '<table width="100%">';
		echo '<tr><td>';
		if ($phpvars['status']['ServerPaused']) {
			echo '<center>Server is paused</center><br>';
			echo '<center><a href="javascript:updatestatus(\'status.php?action=resume\')">resume</a></center>';
		} else
			echo '<center>Server is sleeping</center><br>';
		echo '</td></tr>';
		echo '</table>';
	}
	echo '</div>';
}

function queued_downloading($phpvars, $page) {
	if (count($phpvars['queuedgroups']) == 0)
		return;

	global $GroupsPerPage;
	$cnt = count($phpvars['queuedgroups']);
	$pagecount = pagecount($cnt, $GroupsPerPage);
	if ($page > $pagecount)
		$page = $pagecount;
	if ($page < 1)
		$page = 1;
	
	echo '<div class = "block"><center>Queued</center><br>';
	echo '<table width="100%">';
	echo '<tr><td colspan="7"></td><td>name</td><td width="20">category</td><td width="60" align="right">total</td><td width="60" align="right">left</td><td width="100" align="right">estimated time</td></tr>';

	foreach (array_slice($phpvars['queuedgroups'], ($page - 1) * $GroupsPerPage, $GroupsPerPage) as $cur_queued) {
		$grouppaused=($cur_queued['PausedSizeLo'] != 0) && ($cur_queued['RemainingSizeLo']==$cur_queued['PausedSizeLo']);
		if ($grouppaused)
			echo '<tr class="pausedgroup">';
		else
			echo '<tr class="unpausedgroup">';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=groupdelete&offset=0&id='.$cur_queued['LastID'].'\')"><IMG src=images/cancel.gif width=15 height=15 alt="remove nzb" title="remove nzb"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=groupmovetop&offset=0&id='.$cur_queued['LastID'].'\')"><IMG src=images/first.gif width=15 height=15 alt="move nzb to top in queue" title="move nzb to top in queue"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=groupmoveoffset&offset=-1&id='.$cur_queued['LastID'].'\')"><IMG src=images/up.gif width=15 height=15 alt="move nzb up" title="move nzb up"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=groupmoveoffset&offset=1&id='.$cur_queued['LastID'].'\')"><IMG src=images/down.gif width=15 height=15 alt="move nzb down" title="move nzb down"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=groupmovebottom&offset=0&id='.$cur_queued['LastID'].'\')"><IMG src=images/last.gif width=15 height=15 alt="move nzb to bottom in queue" title="move nzb to bottom in queue"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=grouppause&offset=0&id='.$cur_queued['LastID'].'\')"><IMG src=images/pause.gif width=15 height=15 alt="pause nzb" title="pause nzb"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=groupresume&offset=0&id='.$cur_queued['LastID'].'\')"><IMG src=images/resume.gif width=15 height=15 alt="resume nzb" title="resume nzb"></a></td>';
		echo '<td>'.namereplace($cur_queued['NZBNicename']).'</td>';
		echo '<td width="20">';
		add_category_combo($cur_queued['Category'], $cur_queued['LastID'], $grouppaused);
		echo '</td>';
		echo '<td align="right">'.formatSizeMB($cur_queued['FileSizeMB']).'</td>';
		echo '<td align="right">'.formatSizeMB($cur_queued['RemainingSizeMB']-$cur_queued['PausedSizeMB']).'</td>';

		if ($phpvars['status']['DownloadRate'] > 0)
			echo '<td align="right">'.sec2hms(($cur_queued['RemainingSizeMB']-$cur_queued['PausedSizeMB'])/($phpvars['status']['DownloadRate']/1024/1024)).'</td>';
		else
			echo '<td align="right"></td>';
		
		echo '</tr>';
	}

	echo '</table>';
	
	if ($cnt > $GroupsPerPage) {
		pagelist($cnt, $page, $GroupsPerPage, 'page');
	}
	
	echo '</div>';
}

function has_other_postfiles_from_same_nzb($phpvars, $proc){
	foreach ($phpvars['postqueue'] as $cur_proc) {
		if (($cur_proc['InfoName'] != $proc['InfoName']) &&
			($cur_proc['NZBFilename'] == $proc['NZBFilename']))
			return true;
	}
	return false;
}

function currently_processing($phpvars, $page){
	if (count($phpvars['postqueue']) == 0) 
		return;
		
	$cur_proc=$phpvars['postqueue'][0];
	$disptime="";
	$completed = "";
	$remtime=true;

	if ($cur_proc['Stage'] == 'LOADING_PARS') {	
		$stage="loading par-files";
		$stagewidth=110;
	}
	else if ($cur_proc['Stage'] == 'VERIFYING_SOURCES') {	
		$stage="verifying files";
		$stagewidth=90;
	}
	else if ($cur_proc['Stage'] == 'REPAIRING') {
		$stage="repairing files";
		$stagewidth=90;
	}
	else if ($cur_proc['Stage'] == 'VERIFYING_REPAIRED') {
		$stage="verifying repaired files"; 
		$stagewidth=145;
	}
	else if ($cur_proc['Stage'] == 'EXECUTING_SCRIPT') {
		$stage="executing script"; 
		$stagewidth=100;
		$remtime=false;
	}
	else {
		$stage="";
		$stagewidth=50;
	}

	if ($remtime)
	{
		if ($cur_proc['StageTimeSec'] > 0 && $cur_proc['StageProgress'] > 0) {
			$requiredtime = $cur_proc['StageTimeSec'] * 1000 / $cur_proc['StageProgress'] - $cur_proc['StageTimeSec'];
			$disptime = sec2hms($requiredtime);
		}
	}
	else
	{
		$disptime=sec2hms($cur_proc['StageTimeSec']);
	}

	if (($cur_proc['Stage'] == 'REPAIRING') || 
		($cur_proc['Stage'] == 'VERIFYING_SOURCES') || 
		($cur_proc['Stage'] == 'VERIFYING_REPAIRED'))
		$completed = round1($cur_proc['StageProgress'] / 10)."%";
	
	echo '<div class = "block"><center>Currently processing</center><br>';
	echo '<table width="100%">';
	echo '<tr>';
	echo '<td>name</td><td align="left" width="'.$stagewidth.'">stage</td>';
	echo '<td align="right" width="40">%</td><td width="100" align="right">'.($remtime ? "remaining time" : "elapsed time").'</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td valign="top">'.namereplace($cur_proc[has_other_postfiles_from_same_nzb($phpvars, $cur_proc) ? 'InfoName' : 'NZBNicename']).'</td>';
	echo '<td valign="top" align="left" width="'.$stagewidth.'">'.$stage.'</td>';
	echo '<td valign="top" align="right">'.$completed.'</td>';
	echo '<td valign="top" align="right">'.$disptime.'</td>';
	echo '</tr>';
	echo '</table>';
	
	if (($cur_proc['Stage'] == 'LOADING_PARS') || 
		($cur_proc['Stage'] == 'VERIFYING_SOURCES') || 
		($cur_proc['Stage'] == 'VERIFYING_REPAIRED')) {
		echo '<table width="100%">';
		echo '<tr height="2"><td></td></tr>';
		echo '<tr>';
		echo '<td><small>'.($cur_proc['ProgressLabel']).' ('.(round1($cur_proc['FileProgress'] / 10)).'%)</small></td>';
		echo '<td align="right" width="40"></td><td align="right" width="100"></td>';
		echo '</tr>';
		echo '</table>';
	}
	
// Messages	
	global $NewMessagesFirst, $PostMessagesPerPage;

	$a=$cur_proc['Log'];
	if ($PostMessagesPerPage > 0 && isset($a) && count($a) > 0) {
		
		if ($NewMessagesFirst)
			$a=array_reverse($a);
		
		$cnt = count($a);
		$pagecount = pagecount($cnt, $PostMessagesPerPage);
		if ($page > $pagecount)
			$page = $pagecount;
		if ($page < 1)
			$page = 1;
		
		$per_page = $PostMessagesPerPage;
		if ($NewMessagesFirst) {
			$start = ($page - 1) * $PostMessagesPerPage;
		} else {
			$start = $cnt - $page * $PostMessagesPerPage;
			if ($start < 0) {
				$per_page = $PostMessagesPerPage + $start;
				$start = 0;
			}
		}
	
		echo '<div class = "postlog"><center>Script-output</center><br>';
		echo '<table class="postlogtable">';
		
		foreach (array_slice($a, $start, $per_page) as $info) {
			echo "<tr><td valign='top' class='".$info['Kind']."'>".$info['Kind']."</td><td valign='top'>".FormatLogText($info['Text'])."</td></tr>";
		}
		echo '</table>';
		
		if ($cnt > $PostMessagesPerPage) {
			pagelist($cnt, $page, $PostMessagesPerPage, 'postlogpage');
		}
		echo '</div>';
	}

	echo '</div>';
}

function queued_processing($phpvars){
	$queue=array_slice($phpvars['postqueue'], 1);
	if (count($queue) == 0) 
		return;

	echo '<div class = "block"><center>Queued</center><br>';
	echo '<table width="100%">';
	//echo '<tr><td>name</td></tr>';
	foreach ($queue as $cur_proc) {
		echo '<tr>';
		echo '<td>'.namereplace($cur_proc[has_other_postfiles_from_same_nzb($phpvars, $cur_proc) ? 'InfoName' : 'NZBNicename']).'</td>';
		echo '</tr>';
	}
	echo '</table></div>';
}

function logging ($phpvars, $page) {
	global $NewMessagesFirst, $MessagesPerPage, $LogTimeFormat;

	$a=$phpvars['log'];
	if ($NewMessagesFirst)
		$a=array_reverse($a);
	
	$cnt = count($a);
	$pagecount = pagecount($cnt, $MessagesPerPage);
	if ($page > $pagecount)
		$page = $pagecount;
	if ($page < 1)
		$page = 1;
	
	$per_page = $MessagesPerPage;
	if ($NewMessagesFirst) {
		$start = ($page - 1) * $MessagesPerPage;
	} else {
		$start = $cnt - $page * $MessagesPerPage;
		if ($start < 0) {
			$per_page = $MessagesPerPage + $start;
			$start = 0;
		}
	}

	echo '<div class = "block"><center>Messages</center><br>';
	echo '<table width="100%">';
	
	foreach (array_slice($a, $start, $per_page) as $info) {
		echo "<tr><td valign='top' class='".$info['Kind']."'>".$info['Kind'].
		"</td><td valign='top'><span class='date'>".date($LogTimeFormat, $info['Time'])."</span> ".$info['Text']."</td></tr>";
	}
	echo '</table>';
	
	if ($cnt > $MessagesPerPage) {
		pagelist($cnt, $page, $MessagesPerPage, 'logpage');
	}
	
	echo '</div>';
}

function filelist($phpvars, $page) {
	global $FilesPerPage;

	$cnt = count($phpvars['files']);
	$pagecount = pagecount($cnt, $FilesPerPage);
	if ($page > $pagecount)
		$page = $pagecount;
	if ($page < 1)
		$page = 1;

	echo '<div class = "block"><center>Files for downloading</center><br>';
	echo '<table width="100%">';
	echo '<tr><td colspan="7"></td><td>name</td><td width="60" align="right">MB total</td></tr>';
	
	foreach (array_slice($phpvars['files'], ($page - 1) * $FilesPerPage, $FilesPerPage)  as $cur_queued) {
		$paused=$cur_queued['Paused'];
		if ($paused)
			echo '<tr class="pausedgroup">';
		else
			echo '<tr class="unpausedgroup">';

		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=filedelete&offset=0&id='.$cur_queued['ID'].'\')"><IMG src=images/cancel.gif width=15 height=15 alt="remove file" title="remove file"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=filemovetop&offset=0&id='.$cur_queued['ID'].'\')"><IMG src=images/first.gif width=15 height=15 alt="move file to top in queue" title="move file to top in queue"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=filemoveoffset&offset=-1&id='.$cur_queued['ID'].'\')"><IMG src=images/up.gif width=15 height=15 alt="move file up" title="move file up"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=filemoveoffset&offset=1&id='.$cur_queued['ID'].'\')"><IMG src=images/down.gif width=15 height=15 alt="move file down" title="move file down"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=filemovebottom&offset=0&id='.$cur_queued['ID'].'\')"><IMG src=images/last.gif width=15 height=15 alt="move file to bottom in queue" title="move file to bottom in queue"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=filepause&offset=0&id='.$cur_queued['ID'].'\')"><IMG src=images/pause.gif width=15 height=15 alt="pause file" title="pause file"></a></td>';
		echo '<td width="10"><a href="javascript:updatestatus(\'status.php?action=fileresume&offset=0&id='.$cur_queued['ID'].'\')"><IMG src=images/resume.gif width=15 height=15 alt="resume file" title="resume file"></a></td>';

		echo "<td>".namereplace($cur_queued['NZBNicename'])."/".namereplace($cur_queued['Filename'])."</td>";
		echo "<td align=right>".(round1($cur_queued['FileSizeLo'] / 1024 / 1024))." MB</td>";
		echo '</tr>';
	}
	echo '</table>';
	
	if ($cnt > $FilesPerPage) {
		pagelist($cnt, $page, $FilesPerPage, 'page');
	}
	
	echo '</div>';
}

function pagecount($cnt, $per_page) {
	$pagecount = (int)($cnt / $per_page);
	if ($cnt % $per_page > 0)
		$pagecount++;
	return $pagecount;
}

function pagelist($cnt, $page, $per_page, $varname) {
	$pagecount = pagecount($cnt, $per_page);

	echo '<p><small>&nbsp;&nbsp;';
	for ($i = 1; $i <= $pagecount; $i++) {
		if ($i == $page)
			echo "<span class=\"curpage\">$i</span> &nbsp;";
		else
			echo '<span class="page"><a href="javascript:updatestatus(\'status.php?'.$varname.'='.$i.'\')">'.$i.'</a></span> &nbsp;';
	}
	echo '</small></p>';
}

function serverinfobox($phpvars) {
	echo '<div style="display: none" id="serverinfohidden">';
	echo '<center>NZBGet version '.$phpvars['version'].'</center><br/>';
	echo '<table width="100%">';
	echo '<tr><td>uptime:</td><td align="right"><nobr>'.sec2hms($phpvars['status']['UpTimeSec']).'</nobr></td></tr>';
	echo '<tr><td>download time:</td><td align="right"><nobr>'.sec2hms($phpvars['status']['DownloadTimeSec']).'</nobr></td></tr>';
	echo '<tr><td><nobr>average download rate:</nobr></td><td align="right"><nobr>'.round0($phpvars['status']['AverageDownloadRate']/1024).' KB/s</nobr></td></tr>';
	echo '<tr><td>total downloaded:</td><td align="right"><nobr>'.formatSizeMB($phpvars['status']['DownloadedSizeMB']).'</nobr></td></tr>';
	echo '<tr><td>free disk space:</td><td align="right"><nobr>'.formatSizeMB(freediskspace()).'</nobr></td></tr>';
	echo '</table>';
	echo '</div>';
}	

function servercommandbox($phpvars) {
	global $connected, $WebUsername, $ServerStartCommand, $ServerConfigTemplate, 
		$ServerConfigFile, $groupmode;
	
	echo '<div style="display: none" id="servercommandhidden">';
	echo '<center>Server control<br><br>';
	echo '<span style="line-height: 150%;">';

	echo '<a class="commandlink" href="javascript:updatestatus(\'status.php\')">refresh</a>';

	if ($connected) {
		if ($phpvars['status']['ServerPaused']) {
			echo ' &nbsp;&nbsp;<a class="commandlink" href="javascript:updatestatus(\'status.php?action=resume\')">resume</a>';
		} else {
			echo ' &nbsp;&nbsp;<a class="commandlink" href="javascript:updatestatus(\'status.php?action=pause\')">pause</a>';
		}
	
		if ($ServerStartCommand != '') {
			echo ' &nbsp;&nbsp;<a class="commandlink" href="javascript:ShutdownServer()">shutdown</a>';
		}
		
		if ($groupmode) {
			echo ' &nbsp;&nbsp;<a class="commandlink" href="javascript:updatestatus(\'status.php?filemode=1&page=1&logpage=1\')">files</a>';
		} else {
			echo ' &nbsp;&nbsp;<a class="commandlink" href="javascript:updatestatus(\'status.php?groupmode=1&page=1&logpage=1\')">groups</a>';
		}
	} else {
		if ($ServerStartCommand != '') {
			echo ' &nbsp;&nbsp;<a class="commandlink" href="javascript:updatestatus(\'status.php?start=1\')">start</a>';
		}
	}

	echo ' &nbsp;&nbsp;<a class="commandlink" href=config.php>config</a>';

	if (isset($WebUsername) && $WebUsername != '') {
		echo ' &nbsp;&nbsp;<a class="commandlink" href="logout.php">logout</a>';
	}

	echo '</span>';
	echo '</center>';
	echo '</div>';
}

function connect_error($errormsg) {
	global $ServerStartCommand, $ServerConfigTemplate, $ServerConfigFile;
	
	$connectfailed = !strncmp($errormsg, "ERROR: Connect error:", 21);
	$connectclosed = $errormsg == "ERROR: Server closed connection";
	$connectdecode = !strncmp($errormsg, "ERROR: Could not decode", 23);
	
	echo '<div class = "block">';
	if ($connectfailed) {
		echo '<font color="red">ERROR: NZBGetWeb could not connect to NZBGet-Server.</font><br><br>';
		echo 'Possible reasons include:<br>';
		echo '<list>';
		echo '<li>NZBGet-Server is not running';
		if ($ServerStartCommand != '') {
			echo ' (<a class="commandlink" href="javascript:updatestatus(\'status.php?start=1\')">start</a>)';
		}
		echo ';<il>';
		echo '<li>IP/Port-settings are incorrect. Check <a class="commandlink" href=config.php?section=W-COMMUNICATION%20WITH%20NZBGET-SERVER>config</a>;<il>';
		echo '<li>Firewall is not properly configured (if nzbget-server and web-interface run on different computer).<il>';
		echo '</list>';
		echo "<br><br>Error-message reported by OS: ".substr($errormsg, 22)."<br>";
	} else if ($connectclosed) {
		echo '<font color="red">ERROR: NZBGetWeb could not receive response from NZBGet-Server (although successfully connected).</font><br><br>';
		echo 'Possible reasons include:<br>';
		echo '<list>';
		echo '<li>Password incorrect. Check option "ServerPassword" in <a class="commandlink" href=config.php?section=W-COMMUNICATION%20WITH%20NZBGET-SERVER>config</a>;<il>';
		echo '<li>Server too busy, connect timeout too short. Check option "ConnectTimeout" in <a class="commandlink" href=config.php?section=W-COMMUNICATION%20WITH%20NZBGET-SERVER>config</a>;<il>';
		echo '<li>Not compatible server version.</il>';
		echo '</list>';
	} else if ($connectdecode) {
		echo '<font color="red">ERROR: NZBGetWeb could not process response received from NZBGet-Server (although successfully connected).</font><br><br>';
		echo 'Possible reasons include:<br>';
		echo '<list>';
		echo '<li>Wrong port-settings, NZBGetWeb tries to communicate with a different kind of server (a web-server for example, but not nzbget-server). ';
		echo 'Check option "ServerPort" in <a class="commandlink" href=config.php?section=W-COMMUNICATION%20WITH%20NZBGET-SERVER>config</a>;<il>';
		echo '<li>Not compatible server version.</il>';
		echo '</list>';
	} else {
		echo '<font color="red">'.$errormsg.'</font><br>';
	}
	echo '</div>';
}

function start_server() {
	echo '<div class="block">';
	$output = array();
	$retval = StartServer($output);
	if ($retval != 0) {
		echo "<font color='red'>ERROR: Could not start server. Errorcode: $retval.</font><br><br>";
		if (count($output) > 0) {
			echo 'Output:<br>';
			foreach ($output as $line) {
				echo $line.'<br>';
			}
		}
	} else {
		echo '<font color="#00BB00">INFO: Server started successfully.</font><br><br>';
		echo 'Please give the server few seconds for initialization, then refresh the page.<br>';
	}
	echo '</div>';
}

?>
<?php
	if ($connected) {
		if ($groupmode) {
			currently_downloading($phpvars);
			queued_downloading($phpvars, $page);
			currently_processing($phpvars, $postlogpage);
			queued_processing($phpvars);
			logging ($phpvars, $logpage);
		} else {
			filelist($phpvars, $page);
			if ($FileModeLog) {
				echo '<br>';
				logging ($phpvars, $logpage);
			}
		}
		serverinfobox($phpvars);
		servercommandbox($phpvars);
	
		echo '<div style="display: none" id="updateinterval">'.($groupmode ? $GroupModeRefreshInterval : $FileModeRefreshInterval).'</div>';
		echo '<div style="display: none" id="downloadlimit">'.($phpvars['status']['DownloadLimit'] / 1024).'</div>';
		if (isset($_COOKIE['upload_status'])) {
			echo '<div style="display: none" id="uploadstatushidden">'.($_COOKIE['upload_status']).'</div>';
		}
		if (isset($_COOKIE['newzbin_status'])) {
			echo '<div style="display: none" id="newzbinstatushidden">'.($_COOKIE['newzbin_status']).'</div>';
		}


	} else {
		if ($wantstart) {
			start_server();
		} else {
			connect_error($phpvars);
		}
		servercommandbox($phpvars);
		echo '<div style="display: none" id="serverinfohidden"><center>Server information</center><br></div>';
		echo '<div style="display: none" id="downloadlimit">0</div>';
		echo '<div style="display: none" id="updateinterval">0</div>';
	}
?>