


aws configure 


AKIAPWINCOKAO3U4FWTN


us-east-1
json



aws s3 mb s3://backup-fileon




0 23 * * * root aws s3 cp /var/backups/fileonbackups/full.tar.gz s3://fileon




