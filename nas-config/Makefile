# Copyright (C) 2009 ArteK

include $(TOPDIR)/rules.mk

PKG_NAME:=nas-config
PKG_VERSION:=0.1
PKG_RELEASE:=4

# PKG_BUILD_DIR := $(BUILD_DIR)/$(PKG_NAME)

include $(INCLUDE_DIR)/package.mk

define Package/nas-config
SECTION:=base
CATEGORY:=Base system
TITLE:=Services config for Kamikaze NAS Edition
URL:=http://artekw.cerbero.pl:81/openwrt/openwrt-nas-edition
DEPENDS:=
endef

define Package/nas-config/description
Base config for Kamikaze NAS Edition
endef

define Build/Prepare
endef

define Build/Configure
endef

define Build/Compile
endef

define Package/nas-config/install
	$(INSTALL_DIR) $(1)/etc/
	$(CP) ./files/banner $(1)/etc/banner
	$(CP) ./files/opkg.conf $(1)/etc/opkg.conf
	$(SED) 's,$$$$R,$(REVISION),g' $(1)/etc/banner
	$(SED) 's,$$$$S,$(BOARD),g' -e 's,$$$$A,$(ARCH),g' $(1)/etc/opkg.conf
	$(INSTALL_DATA) ./files/nfsd.exports $(1)/etc/exports
	$(INSTALL_DIR) $(1)/etc/config
	$(INSTALL_DATA) ./files/fstab $(1)/etc/config/fstab
	$(INSTALL_DIR) $(1)/etc/samba
	$(INSTALL_DATA) ./files/smb.conf $(1)/etc/samba/smb.conf
	$(INSTALL_DIR) $(1)/etc/hotplug.d/block
	$(INSTALL_BIN) ./files/10-mount $(1)/etc/hotplug.d/block/10-mount
	$(INSTALL_DIR) $(1)/etc/hotplug.d/usb
	$(INSTALL_BIN) ./files/10-usbdrive $(1)/etc/hotplug.d/usb/10-usbdrive
	$(INSTALL_DIR) $(1)/etc/init.d
	$(INSTALL_BIN) ./files/folders.init $(1)/etc/init.d/folders
endef

$(eval $(call BuildPackage,nas-config))

