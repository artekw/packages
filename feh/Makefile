#
# Copyright (C) 2009 OpenWrt.org
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=feh
PKG_VERSION:=1.2.7
PKG_RELEASE:=1

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=http://linuxbrit.co.uk/downloads/
#PKG_MD5SUM:= 5b2e98da2310c71c703d4617b88a173f

include $(INCLUDE_DIR)/package.mk

define Package/feh
  SECTION:=utils
  CATEGORY:=Utilities
  TITLE:=feh
  URL:=http://sourceforge.net/projects/feh/
endef

define Package/feh/description
feh
endef

define Build/Configure
	$(call Build/Configure/Default)
endef

define Build/Compile
	$(MAKE) -C $(PKG_BUILD_DIR) \
		$(TARGET_CONFIGURE_OPTS) \
		CFLAGS="$(TARGET_CFLAGS)" \
		all
endef

define Package/feh/install
	$(INSTALL_DIR) $(1)/usr/sbin
	$(INSTALL_BIN) $(PKG_BUILD_DIR)/src/feh $(1)/usr/sbin/
endef

$(eval $(call BuildPackage,feh))
