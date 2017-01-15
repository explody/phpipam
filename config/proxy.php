<?php

$c = IpamConfig::config();

/**
 * proxy to use for every internet access like update check
 */
if ($c->proxy->proxy_enabled) {
    
    $proxy_auth = base64_encode($c->proxy->proxy_user . ":" . $c->proxy->proxy_pass);

    if ($c->proxy->proxy_use_auth) {
        stream_context_set_default(
            array('http' => array(
                'proxy' => 'tcp://' . $c->proxy->proxy_server . ':' . $c->proxy->proxy_port,
                'request_fulluri' => true,
                'header' => 'Proxy-Authorization: Basic ' . $c->proxy->proxy_auth
                )
            )
        );
    } else {
        stream_context_set_default(
            array('http' => array(
                'proxy' => 'tcp://'.$c->proxy->proxy_server.':'.$c->proxy->proxy_port
                )
            )
        );
    }
    
}

?>