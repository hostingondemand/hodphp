<?php
namespace hodphp\helper;
use hodphp\lib\helper\BaseHelper;

class Dns extends BaseHelper
{
    function gethostbyname($host){
        return \gethostbyname($host);
    }

    function gethostbyname6($host, $try_a = false) {
// get AAAA record for $host
// if $try_a is true, if AAAA fails, it tries for A
// the first match found is returned
// otherwise returns false

        $dns = $this->gethostbynamel6($host, $try_a);
        if ($dns == false) { return false; }
        else { return $dns[0]; }
    }

    function gethostbynamel6($host, $try_a = false) {
// get AAAA records for $host,
// if $try_a is true, if AAAA fails, it tries for A
// results are returned in an array of ips found matching type
// otherwise returns false

        $dns6 = dns_get_record($host, DNS_AAAA);
        if ($try_a == true) {
            $dns4 = dns_get_record($host, DNS_A);
            $dns = array_merge($dns4, $dns6);
        }
        else { $dns = $dns6; }
        $ip6 = array();
        $ip4 = array();
        foreach ($dns as $record) {
            if ($record["type"] == "A") {
                $ip4[] = $record["ip"];
            }
            if ($record["type"] == "AAAA") {
                $ip6[] = $record["ipv6"];
            }
        }
        if (count($ip6) < 1) {
            if ($try_a == true) {
                if (count($ip4) < 1) {
                    return false;
                }
                else {
                    return $ip4;
                }
            }
            else {
                return false;
            }
        }
        else {
            return $ip6;
        }
    }
}
