#!/bin/bash

set -x

base=apic_addon_appcreds_sync_fp8

rm $base.tar.gz

tar cvf $base.tar $base/

gzip $base.tar

