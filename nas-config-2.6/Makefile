# Copyright (C) 2009 arteq

include $(TOPDIR)/rules.mk

PKG_NAME:=nas-config-2.6
PKG_VERSION:=0.6
PKG_RELEASE:=1

# PKG_BUILD_DIR := $(BUILD_DIR)/$(PKG_NAME)

include $(INCLUDE_DIR)/package.mk

define Package/nas-config-2.6
SECTION:=base
CATEGORY:=Base system
TITLE:=Services config for Kamikaze NAS Edition 2.6
URL:=ftp://artekw.cerbero.pl/openwrt/openwrt-nas-edition
DEPENDS:=
endef

define Package/nas-config-2.6/description
Base config for Kamikaze NAS Edition, kernel 2.6
endef

define Build/Prepare
endef

define Build/Configure
endef

define Build/Compile
endef

define Package/nas-config-2.6/install
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
	$(INSTALL_DATA) ./files/vsftpd.conf $(1)/etc/vsftpd.conf
	$(INSTALL_DIR) $(1)/etc/hotplug.d/block
	$(INSTALL_BIN) ./files/10-mount $(1)/etc/hotplug.d/block/10-mount
	$(INSTALL_DIR) $(1)/etc/hotplug.d/usb
	$(INSTALL_BIN) ./files/10-usbdrive $(1)/etc/hotplug.d/usb/10-usbdrive
	$(INSTALL_DIR) $(1)/etc/rc.d
	$(INSTALL_BIN) ./files/folders.init $(1)/etc/rc.d/S90folders
	$(INSTALL_DIR) $(1)/bin
	$(INSTALL_BIN) ./files/rapid/rsget $(1)/bin/rsget
endef

$(eval $(call BuildPackage,nas-config-2.6))

