
Sip-Tools - 라즈베리 파이를 SIP 자동응답기로 만드는 방법 (pjsip client 사용) 

major git https://github.com/fabianhu/SIP-Pi

updated 2017 06 10 by OLSSOO FACTORY

norman.southcastle@gmail.com
=================================================

SIP 설정샘플

```
cat sipserv.cfg 
sr=OLSSOO
sd=101.250.108.143:55520
su=3651
sp=3651ok!
```

Sip-Tools - Automated calls and answering machine
=================================================
- sipcall - Automated calls over SIP/VOIP with TTS
- sipserv - Answering machine for SIP/VOIP with TTS

Dependencies:
- PJSUA API (http://www.pjsip.org)
- eSpeak (http://espeak.sourceforge.net)

Copyright (C) 2012 by _Andre Wussow_, desk@binerry.de

major changes 2017 by _Fabian Huslik, github.com/fabianhu_

For more informations please visit http://binerry.de/post/29180946733/raspberry-pi-caller-and-answering-machine.

Installation on Raspberry Pi 2/3 with Raspian
=============================================
1. Build and install PjSIP as explained below
2. install eSpeak `sudo apt-get install espeak espeak-data`
2. Copy Project folder to Raspberry Pi and hit`make` in this folder
2. configure `sipserv.cfg` to your needs (see example configuration)
2. test drive using`./sipserv --config-file sipserv.cfg` 
2. this is not(yet) a "real" service, so include `./sipserv-ctrl.sh start` command into your favourite autostart.
2. stop the SIP service using `sipserv-ctrl.sh stop`
2. install lame `sudo apt-get install lame` for the MP3 compression of recordings (mail.sh)

sipserv
=======
Pickup a call, have a welcome message played or read.
Do some actions by pressing (DTMF) keys on your phone.
This service uses a generic approach. All actions are configurable via config file.
One special usage is the special ability to record the caller while playing the intro.
Please contact your lawyer, if this is legal in your country.
With the sample configuration you can have a blacklist and only the special (=blacklisted) calls answered.

##Usage:   
  `sipserv [options]`   

##Commandline:   
###Mandatory options:   
* --config-file=string   _Set config file_   

###Optional options:   
* -s=int       _Silent mode (hide info messages) (0/1)_   

##Config file:   
###Mandatory options:  

* sr=string   _Set sip realm._ 
* sd=string   _Set sip provider domain._   
* su=string   _Set sip username._   
* sp=string   _Set sip password._   
* ln=string   _Language identifier for espeak TTS (e.g. en = English or de = German)._

* tts=string  _String to be read as a intro message_

###_and at least one dtmf configuration (X = dtmf-key index):_   
* dtmf.X.active=int           _Set dtmf-setting active (0/1)._   
* dtmf.X.description=string   _Set description._   
* dtmf.X.tts-intro=string     _Set tts intro._   
* dtmf.X.tts-answer=string    _Set tts answer._   
* dtmf.X.cmd=string           _Set shell command._   

###Optional options:   
* rc=int      _Record call (0=no/1=yes)_   
* af=string   _announcement wav file to play; tts will not be read, if this parameter is given. File format is Microsoft WAV (signed 16 bit) Mono, 22 kHz;_ 
* cmd=string  _command to check if the call should be taken; the wildcard # will be replaced with the calling phone number; should return a "1" as first char, if you want to take the call._
* am=string   _aftermath: command to be executed after call ends. Will be called with two parameters: $1 = Phone number $2 = recorded file name_

##a sample configuration can be found in sipserv-sample.cfg
  
##sipserv can be controlled with 
```bash
./sipserv-ctrl.sh start and 
./sipserv-ctrl.sh stop
```
Build PjSIP 
===========
build directly on Raspberry Pi:
--------------------------

## 최신버젼의 pjsip 설치
## https://www.raspberrypi.org/forums/viewtopic.php?f=28&t=178384

1. Install dependencies
```bash
sudo apt-get install libasound2-dev libssl-dev libv4l-dev libsdl2-dev libsdl2-gfx-dev libsdl2-image-dev libsdl2-mixer-dev libsdl2-net-dev libsdl2-ttf-dev libx264-dev libavformat-dev libavcodec-dev libavdevice-dev libavfilter-dev libavresample-dev libavutil-dev libavcodec-extra-56 libopus-dev libopencore-amrwb-dev libopencore-amrnb-dev libvo-amrwbenc-dev subversion gobjc++
```

2. Create a directory for the source code
```bash
mkdir -p ~/usr/src/olssoo && cd ~/usr/src/olssoo
```

3. Build and install OpenH264
```bash
wget https://github.com/cisco/openh264/archive/v1.6.0.tar.gz
tar -xf v1.6.0.tar.gz
cd openh264-1.6.0
make
sudo make install
cd ..
```

4. Download and extract PJSIP source
```bash
svn checkout http://svn.pjsip.org/repos/pjproject/trunk
cd trunk
./configure 
make dep 
make
sudo make install
```

5. Enable video support
```bash
pi@raspberrypi2b:~/software/source/pjproject-2.6 $ nano pjlib/include/pj/config_site.h
pi@raspberrypi2b:~/software/source/pjproject-2.6 $ cat pjlib/include/pj/config_site.h
#define PJMEDIA_HAS_VIDEO 1
```

6. Set compiler options
```bash
pi@raspberrypi2b:~/software/source/pjproject-2.6 $ nano user.mak
pi@raspberrypi2b:~/software/source/pjproject-2.6 $ cat user.mak
# You can create user.mak file in PJ root directory to specify
# additional flags to compiler and linker. For example:
export CFLAGS += -march=armv7-a -mfpu=neon-vfpv4 -ffast-math -mfloat-abi=hard
export LDFLAGS +=
```

7. Modify third_party/build/os-auto.mak.in (ing)
```bash
pi@raspberrypi2b:~/software/source/pjproject-2.6 $ nano third_party/build/os-auto.mak.in
pi@raspberrypi2b:~/software/source/pjproject-2.6 $ cat third_party/build/os-auto.mak.in

ifneq (@ac_no_gsm_codec@,1)
ifeq (@ac_external_gsm@,1)
# External
else
DIRS += gsm
endif
endif

ifneq (@ac_no_ilbc_codec@,1)
DIRS += ilbc
endif

ifneq (@ac_no_speex_codec@,1)
ifeq (@ac_external_speex@,1)
# External speex
else
DIRS += speex
endif
endif

ifneq (@ac_no_g7221_codec@,1)
DIRS += g7221
endif

ifneq ($(findstring pa,@ac_pjmedia_snd@),)
ifeq (@ac_external_pa@,1)
# External PA
else
#DIRS += portaudio
endif
endif

ifeq (@ac_external_srtp@,1)
# External SRTP
else
DIRS += srtp

ifeq (@ac_ssl_has_aes_gcm@,0)
CIPHERS_SRC = crypto/cipher/aes.o crypto/cipher/aes_icm.o       \
              crypto/cipher/aes_cbc.o
HASHES_SRC  = crypto/hash/sha1.o crypto/hash/hmac.o       \
         # crypto/hash/tmmhv2.o
RNG_SRC     = crypto/rng/rand_source.o crypto/rng/prng.o    \
         crypto/rng/ctr_prng.o
else
CIPHERS_SRC = crypto/cipher/aes_icm_ossl.o crypto/cipher/aes_gcm_ossl.o
HASHES_SRC  = crypto/hash/hmac_ossl.o
RNG_SRC     = crypto/rng/rand_source_ossl.o
SRTP_OTHER_CFLAGS = -DOPENSSL
endif


endif

ifeq (@ac_pjmedia_resample@,libresample)
DIRS += resample
endif

ifneq (@ac_no_yuv@,1)
ifeq (@ac_external_yuv@,1)
# External yuv
else
DIRS += yuv
endif
endif

ifneq (@ac_no_webrtc@,1)
ifeq (@ac_external_webrtc@,1)
# External webrtc
else
DIRS += webrtc
WEBRTC_OTHER_CFLAGS = -fexceptions -DWEBRTC_POSIX=1 @ac_webrtc_cflags@
#ifneq ($(findstring sse2,@ac_webrtc_instset@),)
#    WEBRTC_SRC = \
#             modules/audio_processing/aec/aec_core_sse2.o       \
#         modules/audio_processing/aec/aec_rdft_sse2.o            \
#         modules/audio_processing/aecm/aecm_core_c.o            \
#         modules/audio_processing/ns/nsx_core_c.o                    \
#         system_wrappers/source/cpu_features.o
#else ifneq ($(findstring neon,@ac_webrtc_instset@),)
WEBRTC_SRC = \
    modules/audio_processing/aec/aec_core_neon.o               \
    modules/audio_processing/aec/aec_rdft_neon.o               \
    modules/audio_processing/aecm/aecm_core_c.o                \
    modules/audio_processing/aecm/aecm_core_neon.o             \
    modules/audio_processing/ns/nsx_core_c.o                   \
    modules/audio_processing/ns/nsx_core_neon.o                \
    common_audio/signal_processing/cross_correlation_neon.o    \
    common_audio/signal_processing/downsample_fast_neon.o      \
    common_audio/signal_processing/min_max_operations_neon.o
WEBRTC_OTHER_CFLAGS += -DWEBRTC_HAS_NEON
#else ifneq ($(findstring mips,@ac_webrtc_instset@),)
#    WEBRTC_SRC = \
#              modules/audio_processing/aec/aec_core_mips.o               \
#         modules/audio_processing/aec/aec_rdft_mips.o               \
#         modules/audio_processing/aecm/aecm_core_mips.o             \
#         modules/audio_processing/ns/nsx_core_mips.o                \
#         common_audio/signal_processing/cross_correlation_mips.o    \
#         common_audio/signal_processing/downsample_fast_mips.o      \
#         common_audio/signal_processing/min_max_operations_mips.o
#
#    WEBRTC_OTHER_CFLAGS += -DMIPS_FPU_LE
#else # Generic fixed point
#    WEBRTC_SRC = \
#         modules/audio_processing/aecm/aecm_core_c.o                \
#         modules/audio_processing/ns/nsx_core_c.o                   \
#         common_audio/signal_processing/complex_fft.o
#endif
endif
endif
```



You will have plenty of time to brew some coffe during `make`. Enjoy while waiting.

Cross build of PjSIP for Raspberry:
--------------------------

```sh
export CC=/opt/raspi_tools/tools/arm-bcm2708/gcc-linaro-arm-linux-gnueabihf-raspbian-x64/bin/arm-linux-gnueabihf-gcc
export LD=/opt/raspi_tools/tools/arm-bcm2708/gcc-linaro-arm-linux-gnueabihf-raspbian-x64/bin/arm-linux-gnueabihf-gcc
export CROSS_COMPILE=/opt/raspi_tools/tools/arm-bcm2708/gcc-linaro-arm-linux-gnueabihf-raspbian-x64/bin/arm-linux-gnueabihf-
#export AR+=" -rcs"

export LDFLAGS="-L/opt/raspi_tools/tools/arm-bcm2708/gcc-linaro-arm-linux-gnueabihf-raspbian-x64/lib/gcc/arm-linux-gnueabihf/4.8.3 -L/opt/raspi_tools/tools/arm-bcm2708/gcc-linaro-arm-linux-gnueabihf-raspbian-x64/arm-linux-gnueabihf/lib -ldl -lc"

./aconfigure --host=arm-elf-linux --prefix=$(pwd)/tmp_build --disable-video 

make dep

make
```

sipcall
=======
Make outgoing calls with your Pi.

##Usage:   
* sipcall [options]   

##Mandatory options:  

* sr=string   _Set sip realm._ 
* -sd=string   _Set sip provider domain._   
* -su=string   _Set sip username._   
* -sp=string   _Set sip password._   
* -pn=string   _Set target phone number to call_   
* -tts=string  _Text to speak_   

##Optional options:   
* -ttsf=string _TTS speech file name_   
* -rcf=string  _Record call file name_   
* -mr=int      _Repeat message x-times_   
* -s=int       _Silent mode (hide info messages) (0/1)_   
  
_see also source of sipcall-sample.sh_


License
=======
This tools are free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This tools are distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
Lesser General Public License for more details.
