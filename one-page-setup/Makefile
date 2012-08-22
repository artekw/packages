#
# Copyright (C) 2010 Artur Wronowski <arteqw@gmail.com>
#
# This is free software, licensed under the GNU General Public License v2.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=one-page-setup
PKG_VERSION:=0.07
PKG_RELEASE:=3

PKG_BUILD_DIR := $(BUILD_DIR)/$(PKG_NAME)

include $(INCLUDE_DIR)/package.mk

define Package/one-page-setup
  SECTION:=admin
  CATEGORY:=Administration
  DEPENDS:=+uhttpd +haserl +ip
  MAINTAINER:=Artur Wronowski <arteqw@gmail.com>
  TITLE:=Simple configuration page.
  URL:=http://openwrt.pl
  PKGARCH:=all
endef

define Package/one-page-setup/description
Simple configuration page.
endef

define Package/one-page-setup/postinst
#!/bin/sh	

if [ -z "$${IPKG_INSTROOT}" ]; then
	if [ `ps | grep uhttpd | grep -v grep | wc -l` = "0" ];
	then
		/etc/init.d/uhttpd enable 2>/dev/null
		/etc/init.d/uhttpd start 2>/dev/null
		sleep 2
	fi
fi
echo "Put Your Browser to http://router-ip/wizard.html. Enjoy"
endef

define Build/Prepare
endef

define Build/Configure
endef

define Build/Compile
endef

define Package/one-page-setup/install
	mkdir -p $(PKG_BUILD_DIR)
	$(INSTALL_DIR) $(1)/etc/config
	$(CP) ./files/dhcp.config $(1)/etc/config/dhcp
	$(INSTALL_DIR) $(1)/www/cgi-bin
	$(INSTALL_BIN) ./files/wizard.cgi $(1)/www/cgi-bin/wizard.cgi
	$(INSTALL_DIR) $(1)/www
	for f in jquery.collapse.js jquery.cookie.js jquery.min.js styl.css wizard.html img; do \
		$(CP) ./files/$$$$f $(1)/www/; \
	done
endef

$(eval $(call BuildPackage,one-page-setup))
