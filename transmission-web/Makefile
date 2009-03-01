#
# Copyright (C) 2009 OpenWrt.org
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=transmission
PKG_VERSION:=1.50
PKG_RELEASE:=4

PKG_SOURCE:=transmission-$(PKG_VERSION).tar.bz2
PKG_SOURCE_URL:=http://download.m0k.org/transmission/files
PKG_MD5SUM:=9342004249f09d5e8f87f9bd1476fa53

PKG_FIXUP = libtool

include $(INCLUDE_DIR)/package.mk

define Package/transmission-web
  SUBMENU:=BitTorrent
  SECTION:=net
  CATEGORY:=Network
  DEPENDS:=+libcurl +libopenssl +libpthread
  TITLE:=A free, lightweight BitTorrent client with webgui
  URL:=http://www.transmissionbt.com
endef

define Package/tranmission-web/description
 Transmission is a simple BitTorrent client.
 It features a very simple, intuitive interface
 on top on an efficient, cross-platform back-end.
endef

CONFIGURE_ARGS += \
	--enable-daemon \
	--disable-gtk \
	--disable-wx \
	--with-wx-config="$(STAGING_DIR)/usr/bin/" \

define Build/Compile
	$(MAKE) -C $(PKG_BUILD_DIR) \
		$(TARGET_CONFIGURE_OPTS) \
		CFLAGS="$(TARGET_CFLAGS)"
endef

define Package/transmission-web/install
	$(INSTALL_DIR) $(1)/usr/sbin
	$(INSTALL_BIN) $(PKG_BUILD_DIR)/daemon/transmission-daemon $(1)/usr/sbin/
	$(INSTALL_DIR) $(1)/usr/bin
	$(INSTALL_BIN) $(PKG_BUILD_DIR)/cli/transmissioncli $(1)/usr/bin/
	$(INSTALL_BIN) $(PKG_BUILD_DIR)/daemon/transmission-remote $(1)/usr/bin/
	$(INSTALL_DIR) $(1)/www/transmission
	$(CP) $(PKG_BUILD_DIR)/web/* $(1)/www/transmission/
	$(INSTALL_DIR) $(1)/etc/init.d/
	$(INSTALL_BIN) ./files/transmission.init $(1)/etc/init.d/transmission
	$(INSTALL_DIR) $(1)/etc/transmission-daemon
	$(INSTALL_DATA) ./files/settings.json $(1)/etc/transmission-daemon/settings.json
endef

$(eval $(call BuildPackage,transmission-web))
