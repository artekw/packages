#
# Copyright (C) 2008-2012 OpenWrt.org
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=python-mosquitto
PKG_VERSION:=1.0.3
PKG_RELEASE:=1

PKG_SOURCE:=mosquitto-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=http://mosquitto.org/files/source/
PKG_MD5SUM:=9c9e06c57fcca80d76068c53ad140186

PKG_BUILD_DIR:=$(BUILD_DIR)/mosquitto-$(PKG_VERSION)

include $(INCLUDE_DIR)/package.mk
$(call include_mk, python-package.mk)

define Package/python-mosquitto
	SECTION:=language-python
	CATEGORY:=Languages
	SUBMENU:=Python
	TITLE:=mosquitto - an MQTT message broker
	URL:=http://www.mosquitto.org/
	DEPENDS:=+python-mini +libmosquitto
endef

define Package/python-mosquitto/description
 mosquitto is a message broker that supports v3.1 of the MQ Telemetry
Transport protocol. MQTT provides a lightweight method for
messaging using a publish/subscribe model.
endef

define Build/Configure
endef

define Build/Compile
	$(INSTALL_DIR) $(PKG_INSTALL_DIR)$(PYTHON_PKG_DIR)
	$(INSTALL_DATA) $(PKG_BUILD_DIR)/lib/python/mosquitto.py $(PKG_INSTALL_DIR)$(PYTHON_PKG_DIR)
	$(INSTALL_DATA) $(PKG_BUILD_DIR)/lib/python/sub.py $(PKG_INSTALL_DIR)$(PYTHON_PKG_DIR)
endef

define Package/python-mosquitto/install
	$(INSTALL_DIR) $(1)$(PYTHON_PKG_DIR)
	$(INSTALL_DATA) $(PKG_INSTALL_DIR)$(PYTHON_PKG_DIR)/mosquitto.py $(1)$(PYTHON_PKG_DIR)
	$(INSTALL_DATA) $(PKG_INSTALL_DIR)$(PYTHON_PKG_DIR)/sub.py $(1)$(PYTHON_PKG_DIR)
endef

$(eval $(call BuildPackage,python-mosquitto))
