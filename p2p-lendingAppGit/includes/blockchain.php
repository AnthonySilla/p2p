<?php
function mineBlock($prevHash, $data, $difficulty = '0000') {
    $nonce = 0;
    do {
        $text = $prevHash . $data . $nonce;
        $hash = hash('sha256', $text);
        $nonce++;
    } while (substr($hash, 0, strlen($difficulty)) !== $difficulty);
    
    return [
        'hash' => $hash,
        'nonce' => $nonce - 1,
        'data' => $data,
        'prevHash' => $prevHash
    ];
}
