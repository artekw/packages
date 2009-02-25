<?php
include 'login.php';

header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past

require_once 'settings-template.php';
if (file_exists('settings.php')) require_once 'settings.php';
require_once 'functions.php';

$config = null;
$reqsection = null;
$skipsections = array('DISPLAY (TERMINAL)');
$WebConfigTemplate = 'settings-template.php';
$WebConfigFile = 'settings.php';

define('CATEGORY_SERVER', '1');
define('CATEGORY_WEB', '2');
define('CATEGORY_POSTPROCESS', '3');

if (isset($_REQUEST['section'])) {
	$reqsection = $_REQUEST['section'];
}

class Option {
	var $name;
	var $caption;
	var $value;
	var $defvalue;
	var $description;
	var $enabled;
	var $template;
	var $select;
	var $modified;
	var $multiid;
	var $type;
}

class Section {
	var $name;
	var $key;
	var $multi;
	var $category;
	var $modified;
	var $options = array();
}

function ReadConfigTemplate($filename) {
	global $skipsections;
	
	$config = array();
	$section = null;
	$description = '';
	$firstdescrline = '';
	
	if (!file_exists($filename))
	{
		trigger_error("File not exists $filename");
		return false;
	}
	
	$file_handle = fopen($filename, "rb");
	if ($file_handle == 0)
	{
		trigger_error("Could not open file $filename");
		return false;
	}
	while (!feof($file_handle) ) {
		$line = trim(fgets($file_handle));

		if (!strncmp($line, '### ', 4)) {
			$section = new Section();
			$section->name = trim(substr($line, 4, strlen($line) - 8));
			$description = '';
			if (!in_array($section->name, $skipsections)) {
				$config[$section->name] = $section;
			}
		} else if (!strncmp($line, '# ', 2) || $line == '#') {
			if ($description != '') {
				$description .= ' ';
			}
			$description .= trim(substr($line, 1, 1000));
			$lastchar = substr($description, strlen($description) - 1, 1);
			if ($lastchar == '.' && $firstdescrline == '')
				$firstdescrline = $description;			
			if (strpos(".;:", $lastchar) > -1 || $line == '#') {
				$description .= "\n";
			}
		} else if (strpos($line, '=')) {
			if (!$section)
			{
				// bad template file; create default section.
				$section = new Section();
				$section->name = 'OPTIONS';
				$description = '';
				$config[$section->name] = $section;
			}
		
			$option = new Option();
			$option->enabled = substr($line, 0, 1) != '#';
			$option->name = trim(substr($line, $option->enabled ? 0 : 1, strpos($line, '=') - ($option->enabled ? 0 : 1)));
			$option->caption = $option->name;
			$option->defvalue = trim(substr(strstr($line, '='), 1, 1000));
			$option->description = $description;

			$pstart = strrpos($firstdescrline, '(');
			$pend = strrpos($firstdescrline, ')');
			if ($pstart && $pend && $pend == strlen($firstdescrline) - 2) {
				$option->select = array();
				$paramstr = substr($firstdescrline, $pstart + 1, $pend - $pstart - 1);
				$params = explode(',', $paramstr);
				foreach ($params as $p) {
					$option->select[] = trim($p);
				}
			}

			if (strpos($option->name, '1.') > -1) {
				$section->multi = true;
			}

			if (!$section->multi || strpos($option->name, '1.') > -1) {
				$section->options[] = $option;
			}
			
			if ($section->multi) {
				$option->enabled = false;
				$option->template = true;
			}

			$description = '';
			$firstdescrline = '';
		} else {
			$description = '';
			$firstdescrline = '';
		}
	}
	fclose($file_handle);
	
	return $config;
}

function ReadConfigValues($filename) {

	$values = array();
	
	if (!file_exists($filename))
	{
		trigger_error("File not exists $filename");
		return false;
	}
	
	$file_handle = fopen($filename, "rb");
	if ($file_handle == 0)
	{
		trigger_error("Could not open file $filename");
		return false;
	}
	while (!feof($file_handle) ) {
		$line = trim(fgets($file_handle));

		if (strpos($line, '=')) {
			$option = new Option();
			$enabled = substr($line, 0, 1) != '#';
			if ($enabled) {
				$name = strtolower(trim(substr($line, 0, strpos($line, '='))));
				$value = trim(substr(strstr($line, '='), 1, 1000));
				$values[$name] = $value;
			}
		}
	}
	fclose($file_handle);
	
	return $values;
}

function MergeValues($config, $values) {
	
	// copy values
	foreach ($config as $section) {
		if ($section->multi) {

			// multi sections (news-servers, scheduler)

			$subexists = true;
			for ($i = 1; $subexists; $i++) {
				$subexists = false;
				foreach ($section->options as $option) {
					if (strpos($option->name, '1.') > -1) {
						$name = str_replace('1', $i, $option->name);
						if (array_key_exists(strtolower($name), $values)) {
							$subexists = true;
							break;
						}
					}
				}
				if ($subexists) {
					foreach ($section->options as $option) {
						if ($option->template) {
							$name = str_replace('1', $i, $option->name);
							// copy option
							$newoption = clone $option;
							$newoption->name = $name;
							$newoption->caption = $name;
							$newoption->enabled = true;
							$newoption->template = false;
							$newoption->multiid = $i;
							$section->options[] = $newoption;
							if (array_key_exists(strtolower($name), $values)) {
								$newoption->value = $values[strtolower($name)];
							}
						}
					}
				}
			}
		} else {

			// simple sections

			foreach ($section->options as $option) {
				if (array_key_exists(strtolower($option->name), $values)) {
					$option->value = $values[strtolower($option->name)];
				}
			}
		}
	}
}

function LoadServerConfig() {
	global $config, $ServerConfigTemplate, $ServerConfigFile;

	if (!file_exists($ServerConfigTemplate)) {
		echo '<div class="block"><font color="red">ERROR:</font> Could not load server configuration template. File "'.$ServerConfigTemplate.'" not found. Check option "ServerConfigTemplate".</div>';
		return false;
	}

	$serverconfig = ReadConfigTemplate($ServerConfigTemplate);
	if (!$serverconfig) {
		echo '<div class="block"><font color="red">ERROR:</font> Could not load server configuration template ('.$ServerConfigTemplate.'). Check option "ServerConfigTemplate".</div>';
		return false;
	}

	if (!file_exists($ServerConfigFile)) {
		echo '<div class="block"><font color="red">ERROR:</font> Could not load server configuration file. File "'.$ServerConfigFile.'" not found. Check option "ServerConfigFile".</div>';
		return false;
	}

	$values = ReadConfigValues($ServerConfigFile);
	
	if (!$values) {
		echo '<div class="block"><font color="red">ERROR:</font> Could not load server configuration file ('.$ServerConfigFile.'). Check option "ServerConfigFile".</div>';
		return false;
	}
	
	// copy values
	MergeValues($serverconfig, $values);
	
	// merge sections to main config-array
	foreach ($serverconfig as $key => $section) {
		$section->category = CATEGORY_SERVER;
		$section->key = "S-$key";
		$config["S-$key"] = $section;
	}
	
	return true;
}

function LoadPostProcessConfig() {
	global $config, $PostProcessConfigTemplate, $PostProcessConfigFile;

	if (!file_exists($PostProcessConfigTemplate)) {
		echo '<div class="block"><font color="red">ERROR:</font> Could not load postprocess configuration template. File "'.$PostProcessConfigTemplate.'" not found. Check option "PostProcessConfigTemplate".</div>';
		return false;
	}

	$postprocessconfig = ReadConfigTemplate($PostProcessConfigTemplate);
	if (!$postprocessconfig) {
		echo '<div class="block"><font color="red">ERROR:</font> Could not load postprocess configuration template ('.$PostProcessConfigTemplate.'). Check option "PostProcessConfigTemplate".</div>';
		return false;
	}

	if (!file_exists($PostProcessConfigFile)) {
		echo '<div class="block"><font color="red">ERROR:</font> Could not load postprocess configuration file. File "'.$PostProcessConfigFile.'" not found. Check option "PostProcessConfigFile".</div>';
		return false;
	}

	$values = ReadConfigValues($PostProcessConfigFile);
	
	if (!$values) {
		echo '<div class="block"><font color="red">ERROR:</font> Could not load postprocess configuration file ('.$PostProcessConfigFile.'). Check option "PostProcessConfigFile".</div>';
		return false;
	}
	
	// copy values
	MergeValues($postprocessconfig, $values);
	
	// merge sections to main config-array
	foreach ($postprocessconfig as $key => $section) {
		$section->category = CATEGORY_POSTPROCESS;
		$section->key = "P-$key";
		$config["P-$key"] = $section;
	}
	
	return true;
}

function LoadWebConfig() {
	global $config, $WebConfigTemplate, $WebConfigFile;

	if (!file_exists($WebConfigTemplate)) {
		echo '<div class="block"><font color="red">ERROR:</font> Could not load web configuration template. File "'.$WebConfigTemplate.'" not found.</div>';
		return false;
	}

	$webconfig = ReadConfigTemplate($WebConfigTemplate);
	if (!$webconfig) {
		echo '<div class="block"><font color="red">ERROR:</font> Could not load web configuration template ('.$WebConfigTemplate.').</div>';
		return false;
	}

	if (!file_exists($WebConfigFile)) {
		// copy template file to config file
		if (!copy($WebConfigTemplate, $WebConfigFile)) {
			echo '<div class="block"><font color="red">ERROR:</font> Could not load web configuration file and could not create a new one. File "'.$WebConfigFile.'" not found.</div>';
			return false;
		}		
	}

	if (!file_exists($WebConfigFile)) {
		echo '<div class="block"><font color="red">ERROR:</font> Could not load web configuration file. File "'.$WebConfigFile.'" not found.</div>';
		return false;
	}

	$values = ReadConfigValues($WebConfigFile);
	
	if (!$values) {
		echo '<div class="block"><font color="red">ERROR:</font> Could not load web configuration file ('.$WebConfigFile.').</div>';
		return false;
	}

	// further processing of web-options
	foreach ($webconfig as $section) {
		foreach ($section->options as $option) {
			$option->name = $option->name;
			$option->caption = substr($option->caption, 1, strlen($option->caption) - 1);
			$option->description = str_replace('$', '', $option->description);
			$option->description = str_replace('NOTE: Backslashes (on Windows) must be doubled.', '', $option->description);
			$option->description = str_replace('\\\\', '\\', $option->description);
			$option->description = str_replace('(true, false)', '(yes, no)', $option->description);
			$option->description = trim($option->description);

			WebOptionConfigToValue($option, $option->defvalue);
		}
	}

	// copy values
	foreach ($webconfig as $section) {
		foreach ($section->options as $option) {
			if (array_key_exists(strtolower($option->name), $values)) {
				WebOptionConfigToValue($option, $values[strtolower($option->name)]);
			}
		}
	}

	// merge sections to main config-array
	foreach ($webconfig as $key => $section) {
		$section->category = CATEGORY_WEB;
		$section->key = "W-$key";
		$config["W-$key"] = $section;
	}

	return true;
}

function SaveServerConfig($filename, $category) {
	global $config;
	
	$configcontent = array();

	// read config file
	$file_handle = fopen($filename, "rb");
	if ($file_handle == 0)
	{
		trigger_error("Could not open file $filename");
		return false;
	}
	while (!feof($file_handle) ) {
		$configcontent[] = fgets($file_handle);
	}
	fclose($file_handle);

	// apply settings
	foreach ($config as $section) {
		foreach ($section->options as $option) {
			if ($option->modified && ($section->category == $category)) {
				// find option in configcontent array
				$found = false;
				foreach ($configcontent as $key => $line) {
					if (strpos($line, '=') && strncmp($line, '# ', 2)) {
						$enabled = substr($line, 0, 1) != '#';
						$name = trim(substr($line, $enabled ? 0 : 1, strpos($line, '=') - ($enabled ? 0 : 1)));
						if (strcasecmp($name, $option->name) == 0) {
							$configcontent[$key] = $option->name.'='.$option->value."\n";
							$found = true;
							break;
						}
					}
				}
				
				if (!$found) {
					$configcontent[] = $option->name.'='.$option->value."\n";
				}			
			}
		}
	}
	
	// delete multi options not listed in current config
	foreach ($configcontent as $key => $line) {
		if (strpos($line, '=') && strncmp($line, '# ', 2)) {
			$enabled = substr($line, 0, 1) != '#';
			$name = trim(substr($line, $enabled ? 0 : 1, strpos($line, '=') - ($enabled ? 0 : 1)));

			if (strpos($name, '.') > -1) {
				$found = false;
				foreach ($config as $section) {
					if ($section->multi) {
						foreach ($section->options as $option) {
							if (!$option->template && strcasecmp($name, $option->name) == 0) {
								$found = true;
								break;
							}
						}
					}
					if ($found) {
						break;
					}
				}
				
				if (!$found) {
					unset($configcontent[$key]);
				}
			}
		}
	}
	
	// write config file
	$file_handle = fopen($filename, "w");
	if ($file_handle == 0)
	{
		trigger_error("Could not open file $filename for writing");
		return false;
	}
	foreach ($configcontent as $line) {
		fwrite($file_handle, $line);
	}
	fclose($file_handle);
	
	return true;
}

function SaveWebConfig($filename) {
	global $config;
	
	$configcontent = array();

	// read config file
	$file_handle = fopen($filename, "rb");
	if ($file_handle == 0)
	{
		trigger_error("Could not open file $filename");
		return false;
	}
	while (!feof($file_handle) ) {
		$configcontent[] = fgets($file_handle);
	}
	fclose($file_handle);

	// remove closing php-tag
	foreach ($configcontent as $key => $line) {
		if ($line == '?>') {
			unset($configcontent[$key]);
		}
	}

	// apply settings
	foreach ($config as $section) {
		foreach ($section->options as $option) {
			if ($option->modified && ($section->category == CATEGORY_WEB)) {
				// find option in configcontent array
				$found = false;
				foreach ($configcontent as $key => $line) {
					if (strpos($line, '=') && strncmp($line, '# ', 2)) {
						$name = trim(substr($line, 0, strpos($line, '=')));
						if (strcasecmp($name, $option->name) == 0) {
							$value = WebOptionValueToConfig($option).';';
							$configcontent[$key] = $option->name.'='.$value."\n";
							$found = true;
							break;
						}
					}
				}
				
				if (!$found) {
					$value = WebOptionValueToConfig($option).';';
					$configcontent[] = $option->name.'='.$value."\n";
				}			
			}
		}
	}
	
	// add closing php-tag
	$configcontent[] ='?>';
	
	// write config file
	$file_handle = fopen($filename, "w");
	if ($file_handle == 0)
	{
		trigger_error("Could not open file $filename for writing");
		return false;
	}
	foreach ($configcontent as $line) {
		fwrite($file_handle, $line);
	}
	fclose($file_handle);
	
	return true;
}

function SaveConfig() {
	global $config, $ServerConfigFile, $WebConfigFile, $PostProcessConfigFile;

	$server_modified = false;
	$web_modified = false;
	$postprocess_modified = false;
	
	foreach ($config as $section) {
		foreach ($section->options as $option) {
			if ($option->modified || $section->modified) {
				if ($section->category == CATEGORY_SERVER) {
					$server_modified = true;
				} else if ($section->category == CATEGORY_WEB) {
					$web_modified = true;
				} else if ($section->category == CATEGORY_POSTPROCESS) {
					$postprocess_modified = true;
				}
			}
		}
	}

	$OK = true;
	
	if ($server_modified) {
		$OK &= SaveServerConfig($ServerConfigFile, CATEGORY_SERVER);
	}
	if ($web_modified) {
		$OK &= SaveWebConfig($WebConfigFile);
	}
	if ($postprocess_modified) {
		$OK &= SaveServerConfig($PostProcessConfigFile, CATEGORY_POSTPROCESS);
	}
	
	return $OK;	
}

function WebOptionValueToConfig($option) {

	$value = $option->value;
	
	if ($option->type == 'string') {
		$value = str_replace('\\', '\\\\', $value);
		$value = '\''.$value.'\'';
	} else if ($option->type == 'bool') {
		if ($value == 'yes') {
			$value = 'true';
		} else if ($value == 'no') {
			$value = 'false';
		}
	}
	
	// special handling for option "Categories"
	if ($option->name == '$Categories') {
		if (strncasecmp($value, 'array(', 6)) {
			if ($value != '') {
				$value = '\''.str_replace(',', '\',\'', $value).'\'';

				// normalizing spaces between commas
				$oldvalue = '';
				while ($oldvalue != $value) {
					$oldvalue = $value;
					$value = str_replace(',\' ', ', \'', $value);
					$value = str_replace(', \' ', ', \'', $value);
					$value = str_replace(' \',', '\',', $value);
					$value = str_replace(',\'', ', \'', $value);
					$value = str_replace('\' ,', '\',', $value);
				}
			}
			$value = 'array('.$value.')';
		}
	}
	
	return $value;
}

function WebOptionConfigToValue($option, $confvalue) {

	$value = $confvalue;
	$value = substr($value, 0, strlen($value) - 1);
	
	if (!strcasecmp($value, 'true') || !strcasecmp($value, 'false')) {
		if (!strcasecmp($value, 'true')) {
			$value = 'yes';
		} else if (!strcasecmp($value, 'false')) {
			$value = 'no';
		}
		$option->type = 'bool';
		// replace select (true, false) with (yes, no)
		$option->select = array('yes', 'no');
	} else if (substr($value, 0, 1) == '\'') {
		$value = substr($value, 1, strlen($value) - 2);
		$option->type = 'string';
		$value = str_replace('\\\\', '\\', $value);
	}

	// special handling for option "Categories"
	if ($option->name == '$Categories') {
		if (!strncasecmp($value, 'array(', 6)) {
			$value = substr($value, 6, strlen($value) - 6 - 1);
			$value = str_replace('\'', '', $value);
		}
	}

	$option->value = $value;
}

function MergeSettings() {
	global $_REQUEST, $config;
	
	foreach ($config as $section) {
		foreach ($section->options as $option) {
			if (!$option->template) {
				$name = str_replace('.', '_', $option->name);
				if (isset($_REQUEST[$name])) {
					$value = $_REQUEST[$name];
					if ($option->value != $value) {
						$option->value = $value;
						$option->modified = true;
					}
				}
			}
		}
	}
}

function DeleteMultiSet($secionname, $optionname, $deletemultiid) {
	global $config, $multiset_deleted;
	$section = $config[$secionname];

	// delete set of options
	foreach ($section->options as $key => $option) {
		if ($option->multiid == $deletemultiid) {
			unset($section->options[$key]);
			$section->modified = true;
		}
	}
	
	// renumerate sets
	foreach ($section->options as $option) {
		if ($option->multiid > $deletemultiid) {
			$option->name = str_replace($option->multiid, $option->multiid-1, $option->name);
			$option->multiid--;
			$option->modified = true;
		}	
	}	
}

function AddMultiSet($secionname, $optionname) {
	global $config;
	$section = $config[$secionname];

	// find the biggest multiid
	$maxmultiid = 0;
	foreach ($section->options as $option) {
		if ($maxmultiid < $option->multiid) {
			$maxmultiid = $option->multiid;
		}
	}
	$maxmultiid++;

	// add multi set
	foreach ($section->options as $option) {
		if ($option->template) {
			$name = str_replace('1', $maxmultiid, $option->name);
			// copy option
			$newoption = clone $option;
			$newoption->name = $name;
			$newoption->enabled = true;
			$newoption->template = false;
			$newoption->multiid = $maxmultiid;
			$newoption->value = $newoption->defvalue;
			$newoption->modified = true;
			$section->options[] = $newoption;
			$section->modified = true;
		}
	}
}

function BuildOptionRaw($option) {
	echo ($option->enabled ? '<tr class="enabledoption">' : '<tr class="disabledoption">');
	
	echo '<td width="150">'.$option->caption.'&nbsp;&nbsp;</td>';
	echo '<td>';

	if (count($option->select) > 1) {
		echo '<select class="configselect" name="'.$option->name.'">';
		$valfound = false;
		foreach($option->select as $pvalue) {
			if (strcasecmp($pvalue, $option->value) == 0) {
				echo "<option selected='selected'>$pvalue</option>";
				$valfound = true;
			} else {
				echo "<option>$pvalue</option>";
			}
		}
		if (!$valfound) {
			echo "<option selected='selected'>$option->value</option>";
		}
		echo '</select>';
	} else if (count($option->select) == 1) {
		echo '<input type="text" name="'.$option->name.'" value="'.$option->value.'" class="configeditnumeric">';
		echo ' '.$option->select[0];
	} else if (!strncasecmp($option->description, 'User name', 9) ||
			   !strncasecmp($option->description, 'IP ', 3)) {
		echo '<input type="text" name="'.$option->name.'" value="'.$option->value.'" class="configeditsmall">';
	} else if (!strncasecmp($option->description, 'Password', 8)) {
		echo '<input type="password" name="'.$option->name.'" value="'.$option->value.'" class="configeditsmall">';
	} else {
		echo '<input type="text" name="'.$option->name.'" value="'.$option->value.'" class="configeditlarge">';
	}

	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	if ($option->description != '') {
		$htmldescr = $option->description;
		$htmldescr = str_replace("NOTE: do not forget to uncomment the next line.\n", '', $htmldescr);
		$htmldescr = htmlspecialchars($htmldescr);
		$htmldescr = str_replace("\n", '<br>', $htmldescr);
		$htmldescr = str_replace('NOTE: ', '<font color="red"><b>NOTE: </b></font>', $htmldescr);
		
		echo '<td></td>';
		echo '<td><table><tr><td><div class="description">'.$htmldescr.'</div></td></tr></table></td>';
		echo '</tr>';
	}
}

function BuildMultiRowStart($section, $multiid, $option) {
	$name = $option->name;
	$setname = substr($name, 0, strpos($name, '.'));

	echo '<tr><td colspan="2"><a name="'.$setname.'"></td></tr>';
	echo '<tr><td colspan="2" class="configsettitle">'.$setname.'</td></tr>';
	echo '<tr><td colspan="2"></td></tr>';
	echo '<tr><td colspan="2"></td></tr>';
}

function BuildMultiRowEnd($section, $multiid, $hasmore, $hasoptions) {
	$name = $section->options[0]->name;
	$setname = substr($name, 0, strpos($name, '1'));
	
	if ($hasoptions) {
		echo '<tr><td colspan="2"><table class="tableline" width="100%"><tr><td></td></tr></table></td></tr>';
		echo '<tr><td colspan="2">';
		echo "<input type='button' value='Delete $setname$multiid' onclick='location=\"?delete=$setname&id=$multiid&section=$section->key\"'>";
		echo '</td></tr>';
		echo '<tr><td colspan="2">&nbsp;</td></tr>';
	}

	if (!$hasmore) {
		echo '<tr><td colspan="2"><table class="tableline" width="100%"><tr><td></td></tr></table></td></tr>';
		echo '<tr><td colspan="2">';
		$nextid = $hasoptions ? $multiid+1 : 1;
		echo "<input type='button' value='Add $setname' onclick='location=\"?add=$setname&id=$nextid&section=$section->key\"'>";
		echo '</td></tr>';
	}
}

function BuildOptionsPage() {
	global $config, $reqsection;

	foreach ($config as $section) {
		if ($section->key == $reqsection || !$reqsection) {
			echo '<div class = "block"><center><b>'.$section->name.'</b></center><br>';

			echo '<table width="100%">';
		
			$lastmultiid = 1;
			$firstmultioption = true;
			$hasoptions = false;
			foreach ($section->options as $option) {
				if (!$option->template) {
					if ($section->multi && $option->multiid != $lastmultiid) {
						// new set in multi section
						BuildMultiRowEnd($section, $lastmultiid, true, true);
						$lastmultiid = $option->multiid;
						$firstmultioption = true;
					}
					echo '<tr><td colspan="2"><table class="tableline" width="100%"><tr><td></td></tr></table></td></tr>';
					if ($section->multi && $firstmultioption) {
						BuildMultiRowStart($section, $option->multiid, $option);
						$firstmultioption = false;
					}
					BuildOptionRaw($option);
					$hasoptions = true;
				}
			}
			if ($section->multi) {
				BuildMultiRowEnd($section, $lastmultiid, false, $hasoptions);
			}
			
			echo '</table>';
			echo '</div><br>';
		}
	}
}

function MakeMenu() {
	global $config, $reqsection;

	$server = false;
	$web = false;
	$postprocess = false;
	
	foreach ($config as $section) {
		if ($section->category == CATEGORY_SERVER) {
			$server = true;
		} else if ($section->category == CATEGORY_WEB) {
			$web = true;
		} else if ($section->category == CATEGORY_POSTPROCESS) {
			$postprocess = true;
		}
	}
	
	if ($web) {
		echo '<div class = "block"><center>WEB-INTERFACE</center><br>';
		echo '<table width="100%">';
		foreach ($config as $section) {
			if ($section->category == CATEGORY_WEB) {
				echo '<tr><td class="configmenuitem"><a href="?section='.$section->key.'">'
					.($section->key == $reqsection ? '<span class="menuselectedsection">' : '')
					.$section->name
					.($section->key == $reqsection ? '</span>' : '')
					.'</a></td></tr>';
			}
		}
		echo '</table>';
		echo '</div>';
	}
	
	if ($server) {
		echo '<div class = "block"><center>NZBGET-SERVER</center><br>';
		echo '<table width="'.(msie() ? '90%' : '100%').'%">';
		foreach ($config as $section) {
			if ($section->category == CATEGORY_SERVER) {
				echo '<tr><td class="configmenuitem"><a href="?section='.$section->key.'">'
					.($section->key == $reqsection ? '<span class="menuselectedsection">' : '')
					.$section->name
					.($section->key == $reqsection ? '</span>' : '')
					.'</a></td></tr>';
			}
		}
		echo '</table>';
		echo '</div>';
	}
	
	if ($postprocess) {
		echo '<div class = "block"><center>POSTPROCESSING-SCRIPT</center><br>';
		echo '<table width="'.(msie() ? '90%' : '100%').'%">';
		foreach ($config as $section) {
			if ($section->category == CATEGORY_POSTPROCESS) {
				echo '<tr><td class="configmenuitem"><a href="?section='.$section->key.'">'
					.($section->key == $reqsection ? '<span class="menuselectedsection">' : '')
					.$section->name
					.($section->key == $reqsection ? '</span>' : '')
					.'</a></td></tr>';
			}
		}
		echo '</table>';
		echo '</div>';
	}
}

?>
<HTML>
<HEAD>
<TITLE>NZBGet Web Interface - Settings</TITLE>

<style TYPE="text/css">
<!--
<?php include "style.css" ?>
-->
</style>

</HEAD>
<BODY >

<div class = "top">
	NZBGet Web Interface - Settings
</div>

<?php
	$OK = LoadWebConfig();
	if ($ServerConfigFile != '') {
		LoadServerConfig();
	}
	if ($PostProcessConfigFile != '') {
		LoadPostProcessConfig();
	}

	if ($OK) {
		if (!$reqsection)
		{
			$reqsection = reset($config)->key;
		}

		if (isset($_REQUEST['save'])) {
			MergeSettings();
			$OK = SaveConfig();
			if ($OK) {
				Redirect('config.php?section='.$_REQUEST['section']);
			}
		} else if (isset($_REQUEST['delete'])) {
			DeleteMultiSet($_REQUEST['section'], $_REQUEST['delete'], $_REQUEST['id']);
			$OK = SaveConfig();
			if ($OK) {
				Redirect('config.php?section='.$_REQUEST['section']);
			}
		} else if (isset($_REQUEST['add'])) {
			AddMultiSet($_REQUEST['section'], $_REQUEST['add']);
			$OK = SaveConfig();
			if ($OK) {
				Redirect('config.php?section='.$_REQUEST['section'].'#'.$_REQUEST['add'].$_REQUEST['id']);
			}
		}
	}
	
	if ($OK) {
?>

<table width="100%">
<tr>
<td valign="top" width="270">
<?php
	MakeMenu();
?>

<div class="block">
<center>
<a class="commandlink" href="index.php">back to main page</a>
</center>
</div>

</td>

<td valign="top">
<?php
	echo '<form action="config.php" method='.$FormMethod.'">';
	echo '<input type="hidden" name="save" value="1">';
	echo '<input type="hidden" name="section" value="'.$reqsection.'">';

	BuildOptionsPage();

	echo '<div class="block"><table width="100%"><tr><td><input type="submit" value="Save changes"></td></tr></table></div>';

	if (array_key_exists($reqsection, $config)) {
		$section = $config[$reqsection];
		if ($section->category == CATEGORY_SERVER) {
			echo '<br><div class="block"><table width="100%"><tr><td><font color="red"><b>NOTE:</b></font> NZBGet-Server must be restarted for any changes to have effect.</td></tr></table></div>';
		}
	}	

	echo '</form><br>';
}
?>
</td>

</tr>
</table>

</BODY>
</HTML>
