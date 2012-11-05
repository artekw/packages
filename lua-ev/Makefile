#
# Copyright (C) 2012 OpenWrt.org
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk


PKG_NAME:=lua-ev
PKG_VERSION:=1.3
PKG_RELEASE:=1
PKG_REV:=2848fbc3cf98fa62703e65ac2dcd204c20bc726f

PKG_SOURCE:=lua-ev-$(PKG_VERSION).tar.bz2
PKG_SOURCE_SUBDIR:=lua-ev-$(PKG_VERSION)
PKG_SOURCE_PROTO:=git
PKG_SOURCE_URL:=git://github.com/brimworks/lua-ev.git
PKG_SOURCE_VERSION:=$(PKG_REV)

PKG_BUILD_DIR:=$(BUILD_DIR)/lua-ev-$(PKG_VERSION)

include $(INCLUDE_DIR)/package.mk
include $(INCLUDE_DIR)/cmake.mk


define Package/lua-ev
  SUBMENU:=Lua
  SECTION:=lang
  CATEGORY:=Languages
  TITLE:=lua-ev
  URL:=https://github.com/brimworks/lua-ev
  DEPENDS:=+lua +libev
endef

define Package/lua-ev/description
  lua-ev
endef

#TARGET_CFLAGS += -I$(STAGING_DIR)/usr/include
#TARGET_LDFLAGS += -L$(STAGING_DIR)/usr/lib

CMAKE_OPTIONS = \
	-DLUA_LIBRARIES=$(STAGING_DIR)/usr/lib/lua \
	-DLIBEV_LIBRARY=$(STAGING_DIR)/usr/lib/ev \
	-DLIBEV_INCLUDE_DIR=$(STAGING_DIR)/usr/include/ev \

define Package/lua-ev/install
	$(INSTALL_DIR) $(1)/usr/lib/lua
	$(CP) $(PKG_BUILD_DIR)/ev.so $(1)/usr/lib/lua/

endef

$(eval $(call BuildPackage,lua-ev))
