#!/bin/bash

set -e

rm -rf /var/tmp/dump.sql
mysqldump -uroot -proot boulders_topo_test_test --skip-comments > /var/tmp/dump.sql
