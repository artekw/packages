#
# Copyright (C) 2012 OpenWrt.org
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=python-sqlalchemy
PKG_VERSION:=0.7.4
PKG_RELEASE:=1

PKG_BUILD_DIR:=$(BUILD_DIR)/SQLAlchemy-$(PKG_VERSION)
PKG_SOURCE:=SQLAlchemy-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=@SF/sqlalchemy
PKG_MD5SUM:=

include $(INCLUDE_DIR)/package.mk
$(call include_mk, python-package.mk)

define Package/python-sqlalchemy
  SECTION:=language-python
  CATEGORY:=Languages
  SUBMENU:=Python
  TITLE:=The Python SQL Toolkit and Object Relational Mapper
  URL:=http://www.sqlalchemy.org/
  DEPENDS:=+python +uclibcxx +python-mysql +python-sqlite
endef

define Package/python-sqlalchemy/description
The Python SQL Toolkit and Object Relational Mapper
endef

define Build/Compile
	$(call Build/Compile/PyMod,,install --prefix=/usr --root=$(PKG_INSTALL_DIR))
endef

define Package/python-sqlalchemy/install
	$(INSTALL_DIR) $(1)$(PYTHON_PKG_DIR)
	$(CP) \
		$(PKG_INSTALL_DIR)$(PYTHON_PKG_DIR)/* \
		$(1)$(PYTHON_PKG_DIR)
endef

$(eval $(call BuildPackage,python-sqlalchemy))
