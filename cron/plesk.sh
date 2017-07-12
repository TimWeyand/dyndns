#!/bin/bash

CacheFile="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/../private/data/dyndns.data"
CronFile="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/../private/data/dyndns.cronrun"

if [ ! -f $CronFile ]; then
  echo "CronFile does not exist!"
  touch $CronFile;
  plesk db -r -N -e 'SELECT DISTINCT(domains.name) as domain FROM domains INNER JOIN dns_recs ON domains.dns_zone_id=dns_recs.dns_zone_id WHERE dns_recs.time_stamp >= DATE_SUB(NOW(), INTERVAL 60 MINUTE);' | while read domain; do
    echo "Updating: $domain"
    /opt/psa/admin/bin/dnsmng -update $domain
  done
fi

if [[ $CacheFile -nt $CronFile ]]; then
  echo "New DynDNS Entries available"
  touch $CronFile;
  plesk db -r -N -e 'SELECT DISTINCT(domains.name) as domain FROM domains INNER JOIN dns_recs ON domains.dns_zone_id=dns_recs.dns_zone_id WHERE dns_recs.time_stamp >= DATE_SUB(NOW(), INTERVAL 60 MINUTE);' | while read domain; do
    echo "Updating: $domain"
    /opt/psa/admin/bin/dnsmng -update $domain
  done
fi