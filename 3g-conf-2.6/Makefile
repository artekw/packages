# Copyright (C) 2009 artekw

include $(TOPDIR)/rules.mk

PKG_NAME:=3g-conf-2.6
PKG_VERSION:=0.3
PKG_RELEASE:=1

include $(INCLUDE_DIR)/package.mk

define Package/3g-conf-2.6
SECTION:=base
CATEGORY:=Base system
TITLE:=Kamikaze 3G-brcm-2.6
URL:=ftp://artekw.cerbero.pl/openwrt/openwrt-3G
DEPENDS:=
endef

define Package/3g-conf-2.6/description
Base config for Kamikaze 3G-brcm-2.6
endef

define Build/Prepare
endef

define Build/Configure
endef

define Build/Compile
endef

define Package/3g-conf-2.6/install
	$(INSTALL_DIR) $(1)/etc/
	$(CP) ./files/banner $(1)/etc/banner
	$(CP) ./files/opkg.conf $(1)/etc/opkg.conf
	$(SED) 's,$$$$R,$(REVISION),g' $(1)/etc/banner
	$(SED) 's,$$$$S,$(BOARD),g' -e 's,$$$$A,$(ARCH),g' $(1)/etc/opkg.conf
	$(INSTALL_DATA) ./files/usb_modeswitch.conf $(1)/etc/usb_modeswitch.conf
	$(INSTALL_DIR) $(1)/etc/hotplug.d/usb
	$(INSTALL_BIN) ./files/20-modem $(1)/etc/hotplug.d/usb/20-modem
	$(INSTALL_DIR) $(1)/etc/rc.d
	$(INSTALL_BIN) ./files/hsdpa.initd $(1)/etc/rc.d/S90hsdpa
#	$(INSTALL_DATA) ./files/resolv.conf $(1)/etc/resolv.conf
endef

$(eval $(call BuildPackage,3g-conf-2.6))

