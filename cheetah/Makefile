#
# Copyright (C) 2010 OpenWrt.org
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=cheetah
PKG_VERSION:=2.4.2.1
PKG_RELEASE:=1

PKG_SOURCE:=Cheetah-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=http://pypi.python.org/packages/source/C/Cheetah/
PKG_MD5SUM:=f58ab28548b6e4000c6f4d3b14307187

PKG_BUILD_DIR:=$(BUILD_DIR)/$(subst .tar.gz,,$(PKG_SOURCE))

include $(INCLUDE_DIR)/package.mk
$(call include_mk, python-package.mk)

define Package/cheetah
  SUBMENU:=Python
  SECTION:=lang
  CATEGORY:=Languages
  TITLE:=cheetah
  URL:=http://www.cheetahtemplate.org/
  DEPENDS:=+python
  MAINTAINER:=Artur Wronowski <arteqw@gmail.com>
endef

define Package/cheetah/description
Cheetah is an open source template engine and code generation tool, 
written in Python. It can be used standalone or combined with other tools and frameworks.
endef

define Build/Compile
	$(call Build/Compile/PyMod,,install --prefix=/usr --root=$(PKG_INSTALL_DIR))
endef

define Package/cheetah/install
	$(INSTALL_DIR) $(1)$(PYTHON_PKG_DIR)
	$(CP) \
	    $(PKG_INSTALL_DIR)$(PYTHON_PKG_DIR)/* \
	    $(1)$(PYTHON_PKG_DIR)
endef

$(eval $(call BuildPackage,cheetah))
