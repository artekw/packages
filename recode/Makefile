#
# Copyright (C) 2010 OpenWrt.org
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=recode
PKG_VERSION:=3.6
PKG_RELEASE:=1

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=ftp://ftp.gnu.org/pub/gnu/recode/
#PKG_MD5SUM:= 5b2e98da2310c71c703d4617b88a173f

include $(INCLUDE_DIR)/package.mk

define Package/recode
  SECTION:=utils
  CATEGORY:=Utilities
  TITLE:=recode
  URL:=http://sourceforge.net/projects/recode/
endef

define Package/recode/description
recode
endef

CONFIGURE_ARGS += \
	--disable-nls \
	--disable-static \

define Build/Configure
	$(call Build/Configure/Default)
endef
define Build/Compile
	$(MAKE) -C $(PKG_BUILD_DIR) \
		$(TARGET_CONFIGURE_OPTS) \
		CFLAGS="$(TARGET_CFLAGS)" \
		all
endef

define Package/recode/install
	$(INSTALL_DIR) $(1)/bin
	$(INSTALL_BIN) $(PKG_BUILD_DIR)/src/recode $(1)/bin/
endef

$(eval $(call BuildPackage,recode))
