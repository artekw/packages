// global
var pkg = 'simpleshaper';
var prioNames = ['Najwyższy', 'Wysoki', 'Normalny', 'Niski', 'Najniższy'];
var uci;
var wsp = 1;

function createEditButton()
{
	editButton = createInput("button");
	editButton.value = "Edytuj";
	editButton.className="default_button";
	editButton.onclick = test;
	
//	editButton.className = enabled ? "default_button" : "default_button_disabled" ;
//	editButton.disabled  = enabled ? false : true;

	return editButton;
}

function resetData() {

	uci = uciOriginal.clone();
	// sekcja shape
	// priorytet 'Normalny' domyslnie
	setSelectedValue("add_prio", "2", document);

	var shapeSections = uciOriginal.getAllSectionsOfType(pkg, "shape");

	var columnsName = ['Adres IP', 'Gwarant. DL', 'Maks. DL', 'Gwarant. UL', 'Maks. UL', 'Priorytet'];	
	var shapeTableData = new Array();

	for (secIndex=0; secIndex < shapeSections.length; secIndex++)
	{
		var ip_addr = uciOriginal.get(pkg, shapeSections[secIndex], 'ip_addr');
		var guar_dl = uciOriginal.get(pkg, shapeSections[secIndex], 'guaranted_dl');
		var max_dl = uciOriginal.get(pkg, shapeSections[secIndex], 'max_dl');
		var guar_ul = uciOriginal.get(pkg, shapeSections[secIndex], 'guaranted_ul');
		var max_ul = uciOriginal.get(pkg, shapeSections[secIndex], 'max_ul');
		var prio = uciOriginal.get(pkg, shapeSections[secIndex], 'prio');
		if ( ! prio ) 
		{
			prio=prioNames[2];
		}
		else
		{
			prio=prioNames[prio];
		}

		shapeTableData.push([ip_addr, guar_dl, max_dl, guar_ul, max_ul, prio, createEditButton()]);

	}

	var shapeTable = createTable(columnsName, shapeTableData, 'shape_table', true, false);
	var tableContainer = document.getElementById('shape_table_container');

	if(tableContainer.firstChild != null)
        {
                tableContainer.removeChild(tableContainer.firstChild);
        }
        tableContainer.appendChild(shapeTable);

	// sekcja settings
	var settingsSections = uciOriginal.getAllSectionsOfType(pkg, "settings");

	var linedown = uciOriginal.get(pkg, settingsSections[0], 'line_download');
	document.getElementById("setttings_linedown").value = linedown;

	var lineupload = uciOriginal.get(pkg, settingsSections[0], 'line_upload');
        document.getElementById("setttings_lineupload").value = lineupload;

        var guardowndef = uciOriginal.get(pkg, settingsSections[0], 'def_guaranted');
	if ( ! guardowndef) 
	{
		guardowndef = 128;
	}
        document.getElementById("setttings_quardefdown").value = guardowndef;

        var maxupldef = uciOriginal.get(pkg, settingsSections[0], 'def_max');
	if ( ! maxupldef)
	{
		maxupldef = 128;
        }
        document.getElementById("setttings_maxdefupload").value = maxupldef;

	var custom_wan = uciOriginal.get(pkg, settingsSections[0], 'wan');
	if ( ! custom_wan)
	{
		custom_wan = 'auto';
	}
	setSelectedValue("settings_wan", custom_wan);
}

function saveChanges() {
}

function addNewShape() {

	var settingsSections = uciOriginal.getAllSectionsOfType(pkg, "settings");

	var ip_addr = document.getElementById("add_ipaddr").value;
	var guar_dl = document.getElementById("add_guardl").value;
	var max_dl = document.getElementById("add_maxdl").value;
	var guar_ul = document.getElementById("add_guarul").value;
	var max_ul = document.getElementById("add_maxul").value;
	var prio = document.getElementById("add_prio").value;	

	if (ip_addr == "")
	{
		alert("Pole 'Adres IP' nie moze byc puste")
		return;
	}
	if (guar_dl == "")
	{
		alert("Pole 'Gwarant. pobier.' nie moze byc puste");
		return;
	}
	if (guar_ul == "")
	{
		alert("Pole 'Gwarant. wysy�.' nie moze byc puste");
		return;
	}
	if (max_dl == "")
	{
		var linedown = uciOriginal.get(pkg, settingsSections[0], 'line_download');
		max_dl = Math.round(linedown * wsp)+'';
	}
	if (max_ul == "")
	{
		var lineupload = uciOriginal.get(pkg, settingsSections[0], 'line_upload');
		max_ul = Math.round(lineupload * wsp)+'';
	}

	var prio_num = prio;
	prio=prioNames[prio];

	var sections = uci.getAllSectionsOfType(pkg, "shape");

	var tableContainer = document.getElementById("shape_table_container");
	var table = tableContainer.firstChild;
	addTableRow(table, [ ip_addr, guar_dl, max_dl, guar_ul, max_ul, prio, createEditButton()], true, false);

	uci.set(pkg, sectionIndex, "", "shape");
	uci.set(pkg, sectionIndex, "ip_addr", ip_addr);
	uci.set(pkg, sectionIndex, "guaranted_dl", guar_dl);
	uci.set(pkg, sectionIndex, "max_dl", max_dl);
	uci.set(pkg, sectionIndex, "guaranted_ul", guar_ul);
	uci.set(pkg, sectionIndex, "max_ul", max_ul);
	uci.set(pkg, sectionIndex, "prio", prio_num);

}

function test() {

var settingsSections = uciOriginal.getAllSectionsOfType(pkg, "settings");
var lineupload = uciOriginal.get(pkg, settingsSections[0], 'line_upload');

alert(lineupload * wsp);
}
