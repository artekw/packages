include $(TOPDIR)/rules.mk

PKG_NAME:=lpeg
PKG_VERSION:=0.9
PKG_RELEASE:=1

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=http://www.inf.puc-rio.br/~roberto/lpeg
PKG_MD5SUM:=84a4f5fb4b87b90bb1b7284ec6bb69bc

include $(INCLUDE_DIR)/package.mk

define Package/lpeg
  SUBMENU:=Lua
  SECTION:=lang
  CATEGORY:=Languages
  TITLE:=LPeg
  URL:=http://www.inf.puc-rio.br/~roberto/lpeg/
  DEPENDS:=+liblua
endef

define Package/lpeg/description
	Parsing Expression Grammars for Lua
endef

define Build/Configure
endef

define Build/Compile
	$(MAKE) -C $(PKG_BUILD_DIR) \
		CC=$(TARGET_CC) \
		CFLAGS="$(TARGET_CFLAGS) $(TARGET_CPPFLAGS) $(FPIC) --std=c99" \
		LDFLAGS="$(TARGET_LDFLAGS) -llua"
endef

define Package/lpeg/install
	$(INSTALL_DIR) $(1)/usr/lib/lua
	$(INSTALL_BIN) $(PKG_BUILD_DIR)/lpeg.so $(1)/usr/lib/lua
	$(INSTALL_DATA) $(PKG_BUILD_DIR)/re.lua $(1)/usr/lib/lua
endef

$(eval $(call BuildPackage,lpeg))
