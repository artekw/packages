#
# Copyright (C) 2006-2011 arteq <arteqw@gmail.com>
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#
# $Id$

include $(TOPDIR)/rules.mk

PKG_NAME:=simpleshaper
PKG_VERSION:=0.5
PKG_RELEASE:=2

PKG_BUILD_DIR := $(BUILD_DIR)/$(PKG_NAME)

include $(INCLUDE_DIR)/package.mk

define Package/simpleshaper
  SECTION:=net
  CATEGORY:=Network
  DEPENDS:=+tc +ip +kmod-sched +iptables-mod-conntrack-extra +iptables-mod-ipopt
  TITLE:=Very simple HTB based traffic shapper.
  URL:=http://openwrt.pl/doku.php/software:pasmo#simpleshaper
  PKGARCH:=all
endef

define Package/simpleshaper/conffiles
/etc/config/simpleshaper
endef

define Package/simpleshaper/description
Very simple HTB based traffic shapper.
endef

define Build/Prepare
endef

define Build/Configure
endef

define Build/Compile
endef

define Package/simpleshaper/install
	$(INSTALL_DIR) $(1)/usr/lib
	$(INSTALL_BIN) ./files/uci_shape.sh $(1)/usr/lib/shape
	$(INSTALL_DIR) $(1)/bin
	$(INSTALL_BIN) ./files/ss-start $(1)/bin/ss-start
	$(INSTALL_BIN) ./files/ss-stop $(1)/bin/ss-stop
	$(INSTALL_BIN) ./files/ss-stat $(1)/bin/ss-stat
	$(INSTALL_DIR) $(1)/etc/init.d/
	$(INSTALL_BIN) ./files/simpleshaper.init $(1)/etc/init.d/simpleshaper
	$(INSTALL_DIR) $(1)/etc/config/
	$(INSTALL_DATA) ./files/simpleshaper.config $(1)/etc/config/simpleshaper
	$(INSTALL_DIR) $(1)/etc/hotplug.d/iface/
	$(INSTALL_BIN) ./files/simpleshaper.hotplug $(1)/etc/hotplug.d/iface/38-simpleshaper
endef
$(eval $(call BuildPackage,simpleshaper))
