#!/usr/bin/haserl
<?
	# Copyright (c) 2011 Artur Wronowski <arteqw@gmail.com>
	eval $( gargoyle_session_validator -c "$COOKIE_hash" -e "$COOKIE_exp" -a "$HTTP_USER_AGENT" -i "$REMOTE_ADDR" -r "login.sh" -t $(uci get gargoyle.global.session_timeout) -b "$COOKIE_browser_time"  )
	gargoyle_header_footer -h -s "firewall" -p "simpleshaper" -c "internal.css" -j "ss.js table.js" -i -n simpleshaper network
?>

<script>
<!--
<?

?>
//-->
</script>

<fieldset id='simpleshaper'>
	<legend class="sectionheader">Simpleshaper</legend>
		<label id='enabled_label' class='leftcolumn'>Włacz Simpleshaper</label>
		<span class='rightcolumn'>
			<input type='checkbox' id='enabled_checkbox' onclick='setDnsEnabled(this)'/>
		<span>
	<div id='settings_container'>
	<div>
		<label id='linedown_label' class='leftcolumn'>Maks. pobieranie</label>
		<input id='setttings_linedown' class='rightcolumn' type='text' size='4'/><span>Kbps</span>
	</div>
	<div>
		<label id='lineupload_label' class='leftcolumn'>Maks. wysyłanie</label>
        	<input id='setttings_lineupload' class='rightcolumn' type='text' size='4'/><span>Kbps</span>
	</div>
	<div>
		<label id='defdown_label' class='leftcolumn'>Domyślne gwarnt. pobieranie</label>
        	<input id='setttings_quardefdown' class='rightcolumn' type='text' size='4'/><span>Kbps</span><br />	
	</div>
	<div>	
		<label id='defupload_label' class='leftcolumn'>Domyślne maks. wysyłanie</label>
        	<input id='setttings_maxdefupload' class='rightcolumn' type='text' size='4'/><span>Kbps</span><br />
	</div>
		<label id='wan_label' class='leftcolumn'>Interfejs WAN</label>
		<select id='settings_wan'>
			<option value='auto'>Automatycznie</option>
			<option value='custom'>Inny</option>
		</select>
	</div>
		<div class='internal_divider'></div>
		<label class='nocolumn' id='add_label'>Nowy limit:</label>
	<div class='bottom_gap'>
	<div id='shape_add_container'>
		<table>
			<tr class='table_row_add_header'>
				<th><label id='add_ipaddr_label' for='add_ipaddr'>Adres IP</label></th>
				<th><label id='add_guardl_label' for='add_guardl'>Gwarant. pobier.</label></th>
				<th><label id='add_maxdl_label' for='add_maxdl'>Maks. pobier.</label></th>
				<th><label id='add_guarul_label' for='add_guarul'>Gwarant. wysył</label></th>
				<th><label id='add_maxul_label' for='add_maxul'>Maks. wysył.</label></th>
				<th><label id='add_prio_label' for='add_prio'>Priorytet</label></th>
			</tr>
			<tr class='table_row_add'>
				<td><input type='text' id='add_ipaddr' size='14' onkeyup='proofreadIp(this)' maxLength='15'/></td>
				<td><input type='text' id='add_guardl' size='3' maxLength='5'/></td>
				<td><input type='text' id='add_maxdl' size='3' maxLength='5'/></td>
				<td><input type='text' id='add_guarul' size='3' maxLength='5'/></td>
				<td><input type='text' id='add_maxul' size='3' maxLength='5'/></td>
				<td>
					<select id='add_prio'>
						<option value='0'>Najwyższy</option>
						<option value='1'>Wysoki</option>
						<option value='2'>Normalny</option>
						<option value='3'>Niski</option>
						<option value='4'>Najniższy</option>
					</select>
				</td>
				<td><input type='button' id='add_button' value='Dodaj' class='default_button' onclick='addNewShape()'/></td>
			</tr>
		</table>
	</div>
	</div>
	<div id='shape_table_container' class="bottom_gap"></div>
	<div class='nocolumn'>
		<em>INFORMACJA: Pozostawienie pól 'Maks. pobieranie' i 'Maks. wysyłanie' pustych przy dodawaniu nowego limitu 
		automatycznie wypełniane są wartościami z Maks. pobierania i wysyłania całej linii!</em>
	</div>
</fieldset>

<div id="bottom_button_container">
        <input type='button' value='Zapisz zmiany' id="save_button" class="bottom_button" onclick='saveChanges()' />
        <input type='button' value='Anuluj' id="reset_button" class="bottom_button" onclick='resetData()'/>
</div>

<script>
<!--
	resetData();
//-->
</script>

<?
	gargoyle_header_footer -f -s "firewall" -p "simpleshaper"
?>
