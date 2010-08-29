#
# Copyright (C) 2010 OpenWrt.org
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=plowshare
PKG_VERSION:=0.9.1
PKG_RELEASE:=1

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tgz
PKG_SOURCE_URL:=http://plowshare.googlecode.com/files/
PKG_MD5SUM:=df652252faa364965c122159769f7e56

include $(INCLUDE_DIR)/package.mk

define Package/plowshare
  SECTION:=net
  CATEGORY:=Network
  DEPENDS:=+curl +bash +recode +coreutils-tr
  TITLE:=Download and upload files from Megaupload, Rapidshare and other file-sharing websites.
  URL:=http://code.google.com/p/plowshare/
endef

define Package/plowshare/description
Download and upload files from Megaupload, Rapidshare and other file-sharing websites.
Include modules for Rapidshare, depositfiles, storage.to, x7.to
endef

define Package/plowshare/postinst
#!/bin/sh
ln -s $${IPKG_INSTROOT}/proc/self/fd $${IPKG_INSTROOT}/dev/fd
endef

define Package/plowshare/postrm
#!/bin/sh
rm $${IPKG_INSTROOT}/dev/fd
rm -r $${IPKG_INSTROOT}/usr/bin/modules
endef

define Build/Compile
endef

define Package/plowshare/install
	# common library
	$(INSTALL_DIR) $(1)/usr/share
	cp $(PKG_BUILD_DIR)/src/lib.sh $(1)/usr/share/
	cp $(PKG_BUILD_DIR)/src/*.pl $(1)/usr/share/
	# modules
	$(INSTALL_DIR) $(1)/usr/bin/modules
	cp $(PKG_BUILD_DIR)/src/modules/rapidshare.sh $(1)/usr/bin/modules/
	cp $(PKG_BUILD_DIR)/src/modules/depositfiles.sh $(1)/usr/bin/modules/
	cp $(PKG_BUILD_DIR)/src/modules/storage_to.sh $(1)/usr/bin/modules/
	cp $(PKG_BUILD_DIR)/src/modules/x7_to.sh $(1)/usr/bin/modules/
	# tesseract
#	$(INSTALL_DIR) $(1)/usr/share/tesseract
#	cp $(PKG_BUILD_DIR)/src/tesseract/* $(1)/usr/share/tesseract/
	# binary files
	$(INSTALL_DIR) $(1)/usr/bin/
	cp $(PKG_BUILD_DIR)/src/lib.sh $(1)/usr/bin/
	cp $(PKG_BUILD_DIR)/src/download.sh $(1)/usr/bin/plowdown
	cp $(PKG_BUILD_DIR)/src/upload.sh $(1)/usr/bin/plowup
	cp $(PKG_BUILD_DIR)/src/delete.sh $(1)/usr/bin/plowdel
endef
$(eval $(call BuildPackage,plowshare))
