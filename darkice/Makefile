#
# Copyright (C) 2010 OpenWrt.org
#
# This is free software, licensed under the GNU General Public License v2.
# See /LICENSE for more information.
#

include $(TOPDIR)/rules.mk

PKG_NAME:=darkice
PKG_VERSION:=1.0
PKG_RELEASE:=2

PKG_SOURCE:=$(PKG_NAME)-$(PKG_VERSION).tar.gz
PKG_SOURCE_URL:=http://darkice.googlecode.com/files/
PKG_MD5SUM:= 1804e63d42a9703d01fe378c9a77c473

PKG_FIXUP:=libtool
PKG_INSTALL:=1

include $(INCLUDE_DIR)/package.mk

define Package/darkice
  SECTION:=sound
  CATEGORY:=Sound
  TITLE:=DarkIce is a live audio streamer.
  DEPENDS:=+lame +alsa-lib
endef

define Package/darkice/description
DarkIce is a live audio streamer. It records audio from an audio interface (e.g. sound card), encodes it and sends it to a streaming server.
endef

CONFIGURE_ARGS += \
	--with-lame \
	--without-twolame \
	--with-oss \
	--without-alsa \
	--without-vorbis \
	--without-jack \
	--without-aacplus \
	--without-faac \
	--without-samplerate
			
define Build/Compile
	$(MAKE) -C $(PKG_BUILD_DIR) \
	CXXFLAGS="$(TARGET_CFLAGS)" \
	$(TARGET_CONFIGURE_OPTS) \
	CFLAGS="$(TARGET_CFLAGS) -I$(STAGING_DIR)/usr/include" \
	LDFLAGS="$(TARGET_LDFLAGS)" \
	DESTDIR="$(PKG_INSTALL_DIR)" \
	all install
endef
define Package/darkice/install
	$(INSTALL_DIR) $(1)/usr/sbin
	$(INSTALL_BIN) $(PKG_BUILD_DIR)/src/darkice $(1)/usr/sbin/
	$(INSTALL_DIR) $(1)/etc
	$(INSTALL_BIN) $(PKG_BUILD_DIR)/darkice.cfg $(1)/etc/
endef

$(eval $(call BuildPackage,darkice))
