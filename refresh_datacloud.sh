#!/bin/sh

#Mira los archivos creados y borrados en la carpeta /var/datacloud
inotifywait -mr /var/datacloud -e create,delete --format '%w %e %T' --timefmt '%H%M%S' |
	while read file event tm; do
		current=$(date +'%H%M%S')
		#Comprueba los tiempos de actualizcion en caso de que se actualizan muchos archivos a la vez
		delta=$(expr $current - $tm)
		if [ $delta -lt 2 -a $delta -gt -2 ]; then
			sleep 1
			#Cambia el usuario FTP al usuario de apache
			sudo chown -R www-data:www-data /var/datacloud/*
			#Refresca el listado de archivos
			sudo su -s /bin/sh www-data -c 'php /var/www/nextcloud/occ files:scan --all'
		fi
	done
