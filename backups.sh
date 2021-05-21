#!/bin/sh

tar --exclude=/var/datacloud/appdata* -zcvf /var/backups/fileonbackups/full.tar.gz /var/datacloud/


mega-backup /var/backups/fileonbackups /fileon_backups --period="0 0 0 * * *" --num-backups=3
