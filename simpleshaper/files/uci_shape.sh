#!/bin/sh
# Copyright (C) 2006-2011 arteq <arteqw@gmail.com>
# Documentation http://openwrt.pl or http://artekw.net
# Thanks for shibby & obsy for all suggestion on that project

# global
clients=1

. /etc/functions.sh

IPT="echo iptables"
IPT=iptables
TC="echo tc"
TC=tc

modules() {
        insmod cls_fw >&- 2>&-
        insmod sch_htb >&- 2>&-
        insmod sch_sfq >&- 2>&-
        insmod ipt_CONNMARK >&- 2>&-
#       insmod xt_hashlimit >&- 2>&-
}

setting_rule() {
        local config="$1"

        config_get LAN lan ifname
        config_get WAN wan ifname

        config_get LAN_IP lan ipaddr
        config_get LAN_NETMASK lan netmask
        
        local line_download
        local line_upload
        local def_guaranted
        local def_max

        config_get line_download $1 line_download
        config_get line_upload $1 line_upload
        
        config_get def_guaranted $1 def_guaranted
        [ -n "$def_guaranted" ] || def_guaranted=128
        
        config_get def_max $1 def_max
        [ -n "$def_max" ] || def_max=128

        $TC qdisc add dev "$LAN" root handle 1: htb
        $TC qdisc add dev "$WAN" root handle 2: htb
        $TC class add dev "$LAN" parent 1: classid 1:1 htb rate "$line_download"kbit
        $TC class add dev "$WAN" parent 2: classid 2:1 htb rate "$line_upload"kbit
        # default class for internet thiefs
        add_lan_def_rule() {
                $TC class add dev "$LAN" parent 1:1 classid 1:999 htb rate "$def_guaranted"kbit ceil "$def_max"kbit prio 7
                $TC qdisc add dev "$LAN" parent 1:999 handle 999: sfq perturb 10
                $TC filter add dev "$LAN" parent 1:0 prio 7 protocol ip handle 999 fw flowid 1:999
                $IPT -t mangle -A POSTROUTING ! -s "$LAN_IP"/"$LAN_NETMASK" -j MARK --set-mark 999
        }
        add_wan_def_rule() {
                $TC class add dev "$WAN" parent 2:1 classid 2:999 htb rate "$def_guaranted"kbit ceil "$def_max"kbit prio 7
                $TC qdisc add dev "$WAN" parent 2:999 handle 999: sfq perturb 10
                $TC filter add dev "$WAN" parent 2:0 prio 7 protocol ip handle 999 fw flowid 2:999
                $IPT -t mangle -A PREROUTING ! -d "$LAN_IP"/"$LAN_NETMASK" -j MARK --set-mark 999
        }
        add_lan_def_rule
        add_wan_def_rule
}

tc_rule() {
        local config="$1"

        local ip_addr
        local guaranted_dl
        local guaranted_ul
        local max_dl
        local max_ul
        local prio

        config_get line_download settings line_download
        config_get line_upload settings line_upload
        
        config_get ip_addr $1 ip_addr
        config_get guaranted_dl $1 guaranted_dl
        config_get guaranted_ul $1 guaranted_ul

        config_get max_dl $1 max_dl
        config_get max_ul $1 max_ul
        config_get prio $1 prio
        [ -n "$prio" ] || prio=2
        
        config_get WAN wan ifname
        config_get LAN lan ifname
        config_get LAN_IP lan ipaddr
        config_get LAN_NETMASK lan netmask

        clients=$((clients+1))
        c=$((clients))
        multi=10

        add_dl_rule() {
                # echo "dl rules for" $ip_addr
                $TC class add dev "$LAN" parent 1:1 classid 1:"$((multi*c))" htb rate "$guaranted_dl"kbit ceil "$max_dl"kbit prio "$prio"
                $TC filter add dev "$LAN" parent 1: prio "$prio" protocol ip handle "$((multi*c))" fw flowid 1:"$((multi*c))"
                $TC qdisc add dev "$LAN" parent 1:"$((multi*c))" handle "$((multi*c))":0 sfq perturb 10
                $IPT -t mangle -A POSTROUTING ! -s "$LAN_IP"/"$LAN_NETMASK" --dst "$ip_addr" -j MARK --set-mark "$((multi*c))"
        }
        add_ul_rule() {
                # echo "ul rules for" $ip_addr
                $TC class add dev "$WAN" parent 2:1 classid 2:"$((multi*c))" htb rate "$guaranted_ul"kbit ceil "$max_ul"kbit prio "$prio"
                $TC filter add dev "$WAN" parent 2: prio "$prio" protocol ip handle "$((multi*c))" fw flowid 2:"$((multi*c))"
                $TC qdisc add dev "$WAN" parent 2:"$((multi*c))" handle "$((multi*c))":0 sfq perturb 10
                $IPT -t mangle -A PREROUTING ! -d "$LAN_IP"/"$LAN_NETMASK" --src "$ip_addr" -j MARK --set-mark "$((multi*c))"
        }
        add_dl_rule
        add_ul_rule
}

sh_init() {
        include /lib/network
        scan_interfaces

        config_load simpleshaper        
        modules
        echo "Delete old rules..."
        sh_stop
        echo "Reload settings..."
        config_foreach setting_rule settings
        echo "Reload tc rules..."
        config_foreach tc_rule shape
}

sh_stop() {
        config_get LAN lan ifname
        config_get WAN wan ifname

        $IPT -t mangle -F
        $IPT -t mangle -X 
        $TC qdisc del dev "$LAN" root 2>&- >&-
        $TC qdisc del root dev "$WAN" 2>&- >&-
        }