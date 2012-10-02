# Detection of registered domains by reg-dom libs

Registered domains are top-level-domains (TLDs) and subdomains that have their own registrar, making them 'effective TLDs' that cannot be used arbitrarily, so while you can register any `.com` domain, the same is not true of `.uk` and many other domains. They are useful to know since there are a limited number of them (about 6,000), and that may be used to partially validate domain names in various circumstances without incurring the overhead of DNS lookups. For example, the `co.uk` domain has its own registrar, so the registered domain of `abc.xyz.co.uk` is `co.uk`, and the name is probably valid (a DNS lookup will confirm it). In the other direction, there is no such TLD as `zz`, so a domain name of `abc.xyz.zz` will be spotted as invalid without needing a DNS lookup. The list of known registered domains is maintained by [Mozilla](http://mxr.mozilla.org/mozilla-central/source/netwerk/dns/effective_tld_names.dat?raw=1) and is updated approximately monthly. You need a database of names to do this because otherwise it's not possible to tell which part of a domain name is registered.

The reg-dom libs are available in PHP, C and Perl. This library includes procedural PHP code. There are similar implementations available for python and ruby.

Example code:

    $ingoingDomain = 'abc.xyz.example.com';
    require 'effectiveTLDs.inc.php';
    $tldTree = require 'regDomain.inc.php';
    $registeredDomain = getRegisteredDomain($ingoingDomain, $tldTree);

Return values:

1. NULL if ingoingDomain is a TLD (or effective TLD)
2. The registered domain name if TLD is known
3. just <domain>.<tld> if <tld> is unknown
   This case was added to support new TLDs in outdated reg-dom libs
   by a certain likelihood. This fallback method is implemented in the
   last conversion step and can be simply commented out.

You can regenerate the effective TLD tree structure with the script `generateEffectiveTLDs.php` with the following parameters:

    ./generateEffectiveTLDs.php > effectiveTLDs.inc.php
    ./generateEffectiveTLDs.php perl > effectiveTLDs.pm
    ./generateEffectiveTLDs.php c    > tld-canon.h

The code compiles to a large array; loading it is quite an expensive operation, so it's best used for batch processing large lists of domains.

---

    # Licensed to the Apache Software Foundation (ASF) under one or more
    # contributor license agreements.  See the NOTICE file distributed with
    # this work for additional information regarding copyright ownership.
    # The ASF licenses this file to you under the Apache License, Version 2.0
    # (the "License"); you may not use this file except in compliance with
    # the License.  You may obtain a copy of the License at:
    #
    #     http://www.apache.org/licenses/LICENSE-2.0
    #
    # Unless required by applicable law or agreed to in writing, software
    # distributed under the License is distributed on an "AS IS" BASIS,
    # WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    # See the License for the specific language governing permissions and
    # limitations under the License.


Reg-dom was written by Florian Sager, 2009-02-05, sager@agitos.de

Original code is available at http://www.dkim-reputation.org/regdom-lib-downloads/

This version cleaned up for E_STRICT and PHP 5.4 by Marcus Bointon http://github.com/Synchro