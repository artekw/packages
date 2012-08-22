#!/usr/bin/haserl
Content-Type: text/html

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>OpenWrt One-Page Setup</title>
<link rel="stylesheet" href="../styl.css" type="text/css">
<script>document.documentElement.className = "js";</script>
</head>
<div id="cont">
<%#<form action="<% echo -n $SCRIPT_NAME %>" method="POST"> %>
<div id="center">
<h1>OpenWrt One-Page Setup</h1>
<div id="expand">
<h3>Hasło SSH</h3>
<div id="box"><div class="box-edit">
<div class="title">SSH</div>
<%
if [ `cat /etc/passwd | grep ^root | cut -d : -f 2` != "!" ]
then
echo "<h4>SSH jest włączone. Wpisz nowe hasło dwa razy.</h4>"
	echo "<form action="$SCRIPT_NAME" method="post"><label>NEW.PASSWORD:</label><input type="hidden" name="action" value="passwd_write"><input size="20" type="text" name="password" value="$password"><input size="20" type="text" name="password2" value="$password2"><br /><input type="submit" value="Zapisz"></form>";
else
echo "SSH wyłączone! Ustaw hasło!"
	echo "<form action="$SCRIPT_NAME" method="post"><label>PASSWORD:</label><input type="hidden" name="action" value="passwd_write"><input size="20" type="text" name="password" value="$password"><input size="20" type="text" name="password2" value="$password2"><br /><input type="submit" value="Zapisz"></form>";
fi
%>
</div>
</div>
<h3>Ustawienia sieci bezprzewodowej</h3>
<div id="box"><div class="box-edit">
<div class="title">Radio</div>
<%
if [ `uci get wireless.radio0.disabled` = "1" ]
then
		echo "<h4><span style=color:yellow;>Radio jest wyłączone!</h4><br />"
		echo "<form action="$SCRIPT_NAME" method="post"><input type="hidden" name="action" value="wireless_enable"><input type="hidden" name="form_value" value="0"><input type="submit" value="Włącz-WiFi!"></form>"
else
if [ `/sbin/uci get wireless.@wifi-iface[0].encryption` != "none" ]
then
	values="ssid mode encryption key";
else 
	values="ssid mode encryption";
fi
for value in $values
do	
	label_value=$value
	value=`/sbin/uci get wireless.@wifi-iface[0].$value`
		
	echo "<form action="$SCRIPT_NAME" method="post"><label>`echo $label_value | tr '[a-z]' '[A-Z]'`:</label><input type="hidden" name="action" value="wireless_write"><input type="hidden" name="zmienna" value="$label_value"><input size="15" type="text" name="form_value" value="$value"><input type="submit" value="Zapisz"></form>";

done

echo "<form action="$SCRIPT_NAME" method="post"><input type="hidden" name="action" value="wireless_reload"><input type="submit" value="Przeładuj"></form>";
echo "<form action="$SCRIPT_NAME" method="post"><input type="hidden" name="action" value="wireless_enable"><input type="hidden" name="form_value" value="1"><input type="submit" value="Wyłącz"></form>";
fi
%>
</div>
</div>
<h3>Ustawienia sieci przewodowej</h3>
<div id="box"><div class="box-edit">
<div class="title">Sieć</div>
<%
case `/sbin/uci get network.wan.proto` in
"dhcp") values="lan.type lan.ipaddr lan.dns lan.gateway wan.proto wan.macaddr"; labels="typ-sieci-lan adres-ip-lan adres-dns-lan adres-bramy-lan protokół-wan mac-wan";;
"static") values="lan.type lan.ipaddr lan.dns lan.gateway wan.proto wan.ipaddr wan.netmask wan.macaddr";;
"pppoe") values="lan.type lan.ipaddr lan.dns lan.gateway wan.proto wan.username wan.password wan.keepalive";;
"3g") values="lan.type lan.ipaddr lan.dns lan.gateway wan.proto wan.device wan.service wan.apn wan.pincode wan.username wan.password";;
"") values="lan.proto lan.type lan.ipaddr lan.dns lan.gateway wan.proto";;
*) values="lan.proto lan.type lan.ipaddr lan.dns lan.gateway wan.proto"; echo "Zły protokół!";;
esac

for value in $values
do
		label_value=$value
		valuez=`/sbin/uci get network.$value`
		echo "<form action="$SCRIPT_NAME" method="post"><label>`echo $label_value | tr '[a-z]' '[A-Z]'`:</label><input type="hidden" name="action" value="network_write"><input type="hidden" name="zmienna" value="$label_value"><input size="15" type="text" name="form_value" value="$valuez"><input type="submit" value="Zapisz"></form>";
done
		echo "<form action="$SCRIPT_NAME" method="post"><input type="hidden" name="action" value="network_initd"><input type="submit" value="Przeładuj"></form>";
%>
</div>
</div>
<h3>Ustawienia systemu</h3>
<div id="box"><div class="box-edit">
<div class="title">System</div>
<%
values="hostname timezone"
for value in $values
do	
		label_value=$value
		value=`/sbin/uci get system.@system[0].$value`
		
		echo "<form action="$SCRIPT_NAME" method="post"><label>`echo $label_value | tr '[a-z]' '[A-Z]'`:</label><input type="hidden" name="action" value="system_write"><input type="hidden" name="zmienna" value="$label_value"><input size="20" type="text" name="form_value" value="$value"><input type="submit" value="Zapisz"></form>";
done
%>
</div>
</div>
<h3>Ustawienia serwera DHCP</h3>
<div id="box"><div class="box-edit">
<div class="title">DHCP</div>
<%
case `/sbin/uci get dhcp.wan.ignore` in
"1") values_wan="wan.ignore";;
"0") values_wan="wan.ignore wan.interface wan.start wan.limit wan.leasetime";;
*) values_wan="1";;
esac

case `/sbin/uci get dhcp.lan.ignore` in
"1") values_lan="lan.ignore";;
"0") values_lan="lan.ignore lan.interface lan.start lan.limit lan.leasetime";;
*) values_lan="0";;
esac

if [ `/sbin/uci get dhcp.lan.ignore` = "0" ]
then
lists=`/sbin/uci get dhcp.lan.dhcp_option`
blank='""'
new_lists="$lists $blank"
for list in $new_lists
do	
	echo "<form action="$SCRIPT_NAME" method="post"><label>LAN.DHCP_OPTION:</label><input type="hidden" name="action_add" value="dhcp_addlist"><input type="hidden" name="zmienna" value="lan.dhcp_option"><input size="15" type="text" name="dhcp_value" value="$list"><input name="add" type="submit" value="+"><input name="delete" type="submit" value="-"></form>";	
done
fi

values="$values_lan $values_wan"
for value in $values
do	
		label_value=$value
		value=`/sbin/uci get dhcp.$value`
		
		echo "<form action="$SCRIPT_NAME" method="post"><label>`echo $label_value | tr '[a-z]' '[A-Z]'`:</label><input type="hidden" name="action" value="dhcp_write"><input type="hidden" name="zmienna" value="$label_value"><input size="21" type="text" name="form_value" value="$value"><input type="submit" value="Zapisz"></form>";
done


		echo "<form action="$SCRIPT_NAME" method="post"><input type="hidden" name="action" value="network_initd"><input type="submit" value="Przeładuj"></form>";
		

%>
</div>
</div>
<h3>Ustawienia montowania dysków</h3>
<div id="box"><div class="box-edit">
<div class="title">fstab/mount</div>
<%
flash_rootfs=`cat /proc/mtd | grep rootfs_data | awk '{ print $1 }' | cut -b4-4 | xargs echo mtdblock | sed 's/ //`
overlay_part=`df | grep overlay | grep -v mini_fo | awk '{ print $1 }' | cut -d / -f 3`

case `ls /lib/preinit/00_extroot.conf | wc -l` in
"0") values="enabled options enabled_fsck fstype device target";;
"1") values="enabled options enabled_fsck fstype device target is_rootfs";;
*) values="enabled options enabled_fsck fstype device target"; echo "Błąd!";;
esac

if [ $flash_rootfs != $overlay_part ]; then
    echo "<h4>extroot aktywny!</h4>"
    values="enabled options enabled_fsck fstype device target"
else
    echo "<h4>extroot wyłączony</h4>"
fi
for value in $values
do	
		label_value=$value
		value=`/sbin/uci get fstab.@mount[0].$value`
		
		echo "<form action="$SCRIPT_NAME" method="post"><label>`echo $label_value | tr '[a-z]' '[A-Z]'`:</label><input type="hidden" name="action" value="mount_write"><input type="hidden" name="zmienna" value="$label_value"><input size="20" type="text" name="form_value" value="$value"><input type="submit" value="Zapisz"></form>";
done
%>
<div class="title">fstab/swap</div>
<%
values="enabled device"
for value in $values
do	
		label_value=$value
		value=`/sbin/uci get fstab.@swap[0].$value`		
		echo "<form action="$SCRIPT_NAME" method="post"><label>`echo $label_value | tr '[a-z]' '[A-Z]'`:</label><input type="hidden" name="action" value="swap_write"><input type="hidden" name="zmienna" value="$label_value"><input size="20" type="text" name="form_value" value="$value"><input type="submit" value="Zapisz"></form>";
done

%>
</div>
</div>
<h3 class="active">Informacje o systemie</h3>
<div id="box"><div class="box-info">
<div class="title">Informacje</div>
<%
gateway=`route -n | awk '/^0.0.0.0/ { print $2 }'`;
wanifname=`uci get network.wan.ifname`;
wanip=`ip -o -f inet addr | grep $wanifname | awk '{ print $4 '} | cut -d "/" -f 1`;
release=`cat /etc/openwrt_release | grep DISTRIB_DESCRIPTION | cut -d '"' -f 2`;
loadavg=`uptime | sed "s/.*load average: \(.*\)/\\1/"`
echo "<form><label>OPENWRT.VER:</label><input disabled="disabled" size="25" type="text" value='"$release"'></form>";
echo "<form><label>LOAD.AVG:</label><input disabled="disabled" size="25" type="text" value='"$loadavg"'></form>";
echo "<form><label>WAN.IP:</label><input disabled="disabled" size="25" type="text" value="$wanip"></form>";
echo "<form><label>LAN.GATEWAY:</label><input disabled="disabled" size="25" type="text" value="$gateway"></form>";
%>
</div>
<div id="thanks">OpenWrt One-Page Setup v0.07 (c) <a href=mailto:arteqw@gmail.com>Artur Wronowski</a></div>
</div>
<%
if [ "$FORM_action" = "wireless_write" ]
then
    /sbin/uci set wireless.@wifi-iface[0].$FORM_zmienna=$FORM_form_value
	/sbin/uci commit wireless
	echo "<div id="info">Zapisywanie....</div>"; sleep 2; 
    echo "<META HTTP-EQUIV="refresh" CONTENT="0">"
fi
%>
<%
if [ "$FORM_action" = "wireless_enable" ]
then
    /sbin/uci set wireless.radio0.disabled=$FORM_form_value
	/sbin/uci commit wireless
	wifi > /dev/null
	echo "<div id="info">Zapisywanie....</div>"; sleep 2;
    echo "<META HTTP-EQUIV="refresh" CONTENT="0">"
fi
%>
<%
if [ "$FORM_action" = "wireless_reload" ]
then
	wifi > /dev/null
	echo "<div id="info">Zapisywanie....</div>"; sleep 2;
    echo "<META HTTP-EQUIV="refresh" CONTENT="0">"
fi
%>
<%
if [ "$FORM_action" = "network_write" ]
then
	/sbin/uci set network.$FORM_zmienna=$FORM_form_value
	/sbin/uci commit network
	echo "<div id="info">Zapisywanie....</div>"; sleep 2; 
    echo "<META HTTP-EQUIV="refresh" CONTENT="0">"
fi
%>
<%
if [ "$FORM_action" = "dhcp_write" ]
then
	/sbin/uci set dhcp.$FORM_zmienna=$FORM_form_value
	/sbin/uci commit dhcp
	echo "<div id="info">Zapisywanie....</div>"; sleep 2;  
    echo "<META HTTP-EQUIV="refresh" CONTENT="0">"
fi
%>
<%
if [ "$FORM_action_add" = "dhcp_addlist" ] & [ "$FORM_add" = "+" ]
then

	/sbin/uci add_list dhcp.$FORM_zmienna=$FORM_dhcp_value
	/sbin/uci commit dhcp
	echo "<div id="info">Zapisywanie....</div>"; sleep 2;  
    echo "<META HTTP-EQUIV="refresh" CONTENT="0">"
fi
%>
<%
if [ "$FORM_delete" = "-" ]
then
	list_now=`/sbin/uci get dhcp.lan.dhcp_option`
	/sbin/uci del dhcp.lan.dhcp_option
	mod_list=`echo $list_now | sed 's/'$FORM_dhcp_value'//'`
	for piece in $mod_list
	do
	/sbin/uci add_list dhcp.lan.dhcp_option=$piece
	done
	/sbin/uci commit dhcp
	echo "<div id="info">Zapisywanie....</div>"; sleep 2;  
    echo "<META HTTP-EQUIV="refresh" CONTENT="0">"
fi
%>
<%
if [ "$FORM_action" = "proto_wan" ]
then
	/sbin/uci set network.wan.proto=$FORM_form_proto
	/sbin/uci commit network
	echo "<div id="info">Zapisywanie....</div>"; sleep 2;
    echo "<META HTTP-EQUIV="refresh" CONTENT="0">"
fi
%>
<%
if [ "$FORM_action" = "mount_write" ]
then
    /sbin/uci set fstab.@mount[0].$FORM_zmienna=$FORM_form_value
	/sbin/uci commit fstab
	echo "<div id="info">Zapisywanie....</div>"; sleep 2; 
    echo "<META HTTP-EQUIV="refresh" CONTENT="0">"
fi
%>
<%
if [ "$FORM_action" = "swap_write" ]
then
    /sbin/uci set fstab.@swap[0].$FORM_zmienna=$FORM_form_value
	/sbin/uci commit fstab
	echo "<div id="info">Zapisywanie....</div>"; sleep 2; 
    echo "<META HTTP-EQUIV="refresh" CONTENT="0">"
fi
%>
<%
if [ "$FORM_action" = "system_write" ]
then
    /sbin/uci set system.@system[0].$FORM_zmienna=$FORM_form_value
	/sbin/uci commit system
	echo "<div id="info">Zapisywanie....</div>"; sleep 2; 
    echo "<META HTTP-EQUIV="refresh" CONTENT="0">"
fi
%>
<%
if [ "$FORM_action" = "network_initd" ]
then
    /etc/init.d/network restart
	echo "<div id="info">Zapisywanie....</div>"; sleep 2; 
    echo "<META HTTP-EQUIV="refresh" CONTENT="0">"
fi
%>
<%
if [ "$FORM_action" = "passwd_write" ]
then
    if [ $FORM_password = $FORM_password2 ]
    then
	(echo $FORM_password ; sleep 1; echo $FORM_password) | passwd root;
    else
        echo "<div id="info">Wpisz hasło dwa razy takie samo!</div>";sleep 5;
    fi
    echo "<META HTTP-EQUIV="refresh" CONTENT="0">"
fi
%>
<div id="info"></div>
</div>
</div>
<script type="text/javascript" src="../jquery.min.js"></script>
<script type="text/javascript" src="../jquery.collapse.js"></script>
<script type="text/javascript" src="../jquery.cookie.js"></script> <!-- If you want cookie support -->
<script type="text/javascript">
            $("#expand").collapse({show: function(){
                    this.animate({
                        opacity: 'toggle',
                        height: 'toggle'
                    }, 300);
                },
                hide : function() {

                    this.animate({
                        opacity: 'toggle',
                        height: 'toggle'
                    }, 300);
                }
            });
</script>
</body></html>
