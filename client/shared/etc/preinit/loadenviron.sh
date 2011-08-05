#!/bin/bash
#/**
#@file    loadenviron.sh
#@brief   Script de carga de la API de funciones de OpenGNSys.
#@warning License: GNU GPLv3+
#@version 0.9
#@author  Ramon Gomez, ETSII Universidad de Sevilla
#@date    2009-10-10
#*/

# Idioma por defecto.
export LANG="${LANG:-es_ES}"

# Directorios del projecto OpenGnSys.
export OPENGNSYS="${OPENGNSYS:-/opt/opengnsys}"
if [ -d $OPENGNSYS ]; then
    export OGBIN=$OPENGNSYS/bin
    export OGETC=$OPENGNSYS/etc
    export OGLIB=$OPENGNSYS/lib
    export OGAPI=$OGLIB/engine/bin
    export OGSCRIPTS=$OPENGNSYS/scripts
    export OGIMG=$OPENGNSYS/images
    export OGCAC=$OPENGNSYS/cache
    export OGLOG=$OPENGNSYS/log

    export PATH=$PATH:/sbin:/usr/sbin:/usr/local/sbin:/bin:/usr/bin:/usr/local/bin:/opt/oglive/rootfs/opt/drbl/sbin
 
    export PATH=$OGSCRIPTS:$PATH:$OGAPI:$OGBIN
   
    # Exportar parámetros del kernel.
    for i in $(cat /proc/cmdline); do
        echo $i | grep -q "=" && export $i
    done
   
    # Cargar fichero de idioma.
    LANGFILE=$OGETC/lang.${LANG%@*}.conf
    if [ -f $LANGFILE ]; then
	source $LANGFILE
	for i in $(awk -F= '{if (NF==2) print $1}' $LANGFILE); do
	    export $i
	done
    fi
    echo "$MSG_LOADAPI"

    # Cargar mapa de teclado.
    loadkeys ${LANG%_*} >/dev/null

    # Cargar API de funciones.
    for i in $OGAPI/*.lib; do
        source $i
    done

    for i in $(typeset -F | cut -f3 -d" "); do
	export -f $i
    done

    # Carga de las API segun engine
    if [ -n "$ogengine" ]
    then
    	for i in $OGAPI/*.$ogengine; do
            [ -f $i ] && source $i 
    	done
    fi
   
    # Fichero de registros.
    export OGLOGFILE="$OGLOG/$(ogGetIpAddress).log"
    
    # Configuracion de la red (valido offline)
    cat /tmp/initrd.cfg | grep DEVICECFG && export $(cat /tmp/initrd.cfg | grep DEVICECFG)
    source $DEVICECFG 2>/dev/null
    
    # FIXME Pruebas para grupos de ordenadores
    #export OGGROUP=$(ogGetGroup)
    export OGGROUP="$group"
    
    ROOTREPO=${ROOTREPO:-"$OGSERVERIMAGES"}
fi

# Declaración de códigos de error.
export OG_ERR_FORMAT=1		# Formato de ejecución incorrecto.
export OG_ERR_NOTFOUND=2	# Fichero o dispositivo no encontrado.
export OG_ERR_PARTITION=3	# Error en partición de disco.
export OG_ERR_LOCKED=4		# Partición o fichero bloqueado.
export OG_ERR_IMAGE=5		# Error al crear o restaurar una imagen.
export OG_ERR_NOTOS=6		# Sin sistema operativo.
export OG_ERR_NOTEXEC=7		# Programa o función no ejecutable.
#codigo 8-13 reservados por ogAdmClient.h
export OG_ERR_NOTWRITE=14	# No hay acceso de escritura
export OG_ERR_NOTCACHE=15	# No hay particion cache en cliente
export OG_ERR_CACHESIZE=16	# No hay espacio en la cache para almacenar fichero-imagen
export OG_ERR_REDUCEFS=17	# Error al reducir sistema archivos
export OG_ERR_EXTENDFS=18	# Error al expandir el sistema de archivos

export OG_ERR_IMGSIZEPARTITION=30   #Error al restaurar: Particion mas pequeña que la imagen. 

export OG_ERR_UCASTSYNTAXT=50  # Error en la generación de sintaxis de transferenica unicast
export OG_ERR_UCASTSENDPARTITION=51  # Error en envio UNICAST de una particion
export OG_ERR_UCASTSENDFILE=52  # Error en envio UNICAST de un fichero
export OG_ERR_UCASTRECEIVERPARTITION=53  #Error en la recepcion UNICAST de una particion
export OG_ERR_UCASTRECEIVERFILE=54  #Error en la recepcion UNICAST de un fichero
export OG_ERR_MCASTSYNTAXT=55 # Error en la generacion de sintaxis de transferenica Multicast.
export OG_ERR_MCASTSENDFILE=56  # Error en envio MULTICAST de un fichero
export OG_ERR_MCASTRECEIVERFILE=57  #Error en la recepcion MULTICAST de un fichero
export OG_ERR_MCASTSENDPARTITION=58  # Error en envio MULTICAST de una particion
export OG_ERR_MCASTRECEIVERPARTITION=59  # Error en la recepcion MULTICAST de una particion
export OG_ERR_PROTOCOLJOINMASTER=60 # Error en la conexion de una sesion UNICAST|MULTICAST con el MASTER




