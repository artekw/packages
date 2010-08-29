#
# Copyright (C) 2010 OpenWrt.org
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=sabnzbd
PKG_VERSION:=0.5.4
PKG_RELEASE:=1

PKG_SOURCE:=SABnzbd-$(PKG_VERSION)-src.tar.gz
PKG_SOURCE_URL:=@SF/sabnzbdplus
PKG_MD5SUM:=b64e5d47ea4c115b98dd96744946c89d

PKG_BUILD_DIR:=$(BUILD_DIR)/$(subst -src.tar.gz,,$(PKG_SOURCE))

include $(INCLUDE_DIR)/package.mk

define Package/sabnzbd
  SUBMENU:=NNTP
  SECTION:=net
  CATEGORY:=Network
  TITLE:=SABnzbd
  URL:=http://sabnzbd.org/
  DEPENDS:=+python +cheetah +libpar2 +unrar +python-sqlite3
  MAINTAINER:=Artur Wronowski <arteqw@gmail.com>
endef

define Package/sabnzbd/description
SABnzbd is an Open Source Binary Newsreader written in Python.
SABnzbd makes Usenet as simple and streamlined as possible by 
automating everything we can. All you have to do is add an .nzb. 
SABnzbd takes over from there, where it will be automatically downloaded, 
verified, repaired, extracted and filed away with zero human interaction.
endef

define Build/Compile
endef

define Package/sabnzbd/install
	$(INSTALL_DIR) $(1)/etc/init.d/
	$(INSTALL_BIN) files/sabnzbd.init $(1)/etc/init.d/sabnzbd
	$(INSTALL_DIR) $(1)/usr/share/$(PKG_NAME)
	for f in cherrypy interfaces language licenses sabnzbd; do \
		$(CP) $(PKG_BUILD_DIR)/$$$$f $(1)/usr/share/$(PKG_NAME)/ ; \
	done
		$(INSTALL_DIR) $(1)/usr/share/$(PKG_NAME)
	for f in SABnzbd.py README.txt COPYRIGHT.txt GPL2.txt GPL3.txt LICENSE.txt nzb.ico; do \
		$(CP) $(PKG_BUILD_DIR)/$$$$f $(1)/usr/share/$(PKG_NAME)/ ; \
	done
endef

$(eval $(call BuildPackage,sabnzbd))
