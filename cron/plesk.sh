#!/bin/bash

CacheFile="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/../private/data/dyndns.data"
CronFile="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/../private/data/dyndns.cronrun"

if [ ! -f $CronFile ]; then
  echo "CronFile does not exist!"
  touch $CronFile;
  /opt/psa/admin/bin/dnsmng -update
fi

if [[ $CacheFile -nt $CronFile ]]; then
  echo "New DynDNS Entries available"
  touch $CronFile;
  /opt/psa/admin/bin/dnsmng -update
fi