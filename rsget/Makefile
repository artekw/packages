#
# Copyright (C) 2010 OpenWrt <arteqw@gmail.com>
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=rsget
PKG_VERSION:=12234
PKG_RELEASE:=1

PKG_SOURCE:=$(PKG_NAME).pl-svn-$(PKG_VERSION).tar.bz2
PKG_SOURCE_URL:=http://rsget.pl/download/
#PKG_MD5SUM:= 5b2e98da2310c71c703d4617b88a173f
PKG_BUILD_DIR := $(BUILD_DIR)/$(PKG_NAME).pl-svn-$(PKG_VERSION)

include $(INCLUDE_DIR)/package.mk

define Package/rsget
  SECTION:=net
  CATEGORY:=Network
  DEPENDS:=+perl +perlbase-autoloader +perlbase-base +perlbase-bytes +perlbase-config +perlbase-cwd +perlbase-digest +perlbase-dynaloader +perlbase-errno +perlbase-essential +perlbase-fcntl +perlbase-file +perlbase-io +perlbase-mime +perlbase-posix +perlbase-selectsaver +perlbase-socket +perlbase-symbol +perlbase-tie +perlbase-time +perlbase-xsloader +perl-www-curl +perl-uri
  TITLE:=rsget
  URL:=http://rsget.pl
  PKGARCH:=all
  MAINTAINTER:=Artur Wronowski <arteqw@gmail.com>
endef

define Package/rsget/description
A powerful perl application designed to retrieve files from download services (like RapidShare, MegaUpload and many more).
endef

define Build/Prepare
	tar -xjvf $(DL_DIR)/$(PKG_SOURCE) -C $(BUILD_DIR)/
endef

define Build/Configure
endef

define Build/Compile
endef

define Package/rsget/install
	$(INSTALL_DIR) $(1)/root/$(PKG_NAME)
	for f in Video RSGet Link Image Get Direct data Audio rsget.pl; do \
		$(CP) $(PKG_BUILD_DIR)/$$$$f $(1)/root/$(PKG_NAME)/ ; \
	done
	$(INSTALL_DIR) $(1)/root/.rsget.pl
	$(CP) $(PKG_BUILD_DIR)/README.config $(1)/root/.rsget.pl/config

endef
$(eval $(call BuildPackage,rsget))
