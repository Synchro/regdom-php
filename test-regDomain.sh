#!/bin/sh
FQDNS="registered.com sub.registered.com parliament.uk sub.registered.valid.uk registered.somedom.kyoto.jp invalid-fqdn org academy.museum sub.academy.museum subsub.sub.academy.museum sub.nic.pa registered.sb sub.registered.sb abc.xyz.co.uk abc.xyz.zz example.uk subsub.registered.something.zw subsub.registered.9.bg registered.co.bi sub.registered.bi subsub.registered.ee ua"

./test-regDomain.php $FQDNS
