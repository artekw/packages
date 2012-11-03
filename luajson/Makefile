#
# Copyright (C) 2012 OpenWrt.org
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=luajson
PKG_VERSION:=1.3
PKG_RELEASE:=1
PKG_REV:=31694907d4eb68e6af7af1ab80e637e0fd1b09d6

PKG_SOURCE:=luajson-$(PKG_VERSION).tar.bz2
PKG_SOURCE_SUBDIR:=luajson-$(PKG_VERSION)
PKG_SOURCE_PROTO:=git
PKG_SOURCE_URL:=git://github.com/harningt/luajson.git
PKG_SOURCE_VERSION:=$(PKG_REV)

PKG_BUILD_DIR:=$(BUILD_DIR)/luajson-$(PKG_VERSION)

include $(INCLUDE_DIR)/package.mk

define Package/luajson
  SUBMENU:=Lua
  SECTION:=lang
  CATEGORY:=Languages
  TITLE:=LuaJson
  URL:=https://github.com/harningt/luajson/
  DEPENDS:=+lua +lpeg
endef

define Package/luajson/description
  LuaJson
endef

define Build/Configure
endef

define Build/Compile
endef


define Package/luajson/install
	$(INSTALL_DIR) $(1)/usr/lib/lua
	$(INSTALL_DATA) $(PKG_BUILD_DIR)/lua/json.lua $(1)/usr/lib/lua
	$(INSTALL_DIR) $(1)/usr/lib/lua/json
	$(INSTALL_DATA) $(PKG_BUILD_DIR)/lua/json/{decode,encode,util}.lua $(1)/usr/lib/lua/json
	$(INSTALL_DIR) $(1)/usr/lib/lua/json/encode
	$(INSTALL_DATA) $(PKG_BUILD_DIR)/lua/json/encode/{array,calls,number,object,others,output,output_utility,strings}.lua $(1)/usr/lib/lua/json/encode
	$(INSTALL_DIR) $(1)/usr/lib/lua/json/decode
	$(INSTALL_DATA) $(PKG_BUILD_DIR)/lua/json/decode/{composite,number,others,state,strings,util}.lua $(1)/usr/lib/lua/json/decode
endef

$(eval $(call BuildPackage,luajson))
