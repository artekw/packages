#
# Copyright (C) 2011 OpenWrt.org
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=catty
PKG_VERSION:=2006.03.13
PKG_RELEASE:=1

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=@SF/catty
PKG_MD5SUM:= eed76a2e7d3fa34b1ba1605f52587190

include $(INCLUDE_DIR)/package.mk

define Package/catty
  SECTION:=utils
  CATEGORY:=Utilities
  TITLE:=shell interface to serial tty
  MAINTAINER:=Artur Wronowski <arteqw@gmail.com>
endef

define Package/catty/description
catty is a simple shell interface to serial tty lines
endef

define Build/Compile
	$(CP) ./files/Makefile $(PKG_BUILD_DIR)
	$(MAKE) -C $(PKG_BUILD_DIR) \
	CXXFLAGS="$(TARGET_CFLAGS)" \
	$(TARGET_CONFIGURE_OPTS) \
	CFLAGS="$(TARGET_CFLAGS) -I$(STAGING_DIR)/usr/include" \
	LDFLAGS="$(TARGET_LDFLAGS)" \
	DESTDIR="$(PKG_INSTALL_DIR)" \

endef

define Package/catty/install
	$(INSTALL_DIR) $(1)/usr/sbin
	$(INSTALL_BIN) $(PKG_BUILD_DIR)/catty $(1)/usr/sbin/
endef

$(eval $(call BuildPackage,catty))
