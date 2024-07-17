<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
date_default_timezone_set('Europe/London');
$defport = "3333";
// Uncomment the following block and add server definitions, then run to
// create a starting json file which you can then edit
/*
$server_data = array();
$server_data[] = array("server" => "my-first-server", "port" => "42485");
$server_data[] = array("server" => "my-second-server");

$sarray = array( "defport" => "$defport", "servers" => $server_data);
file_put_contents("alive.json", json_encode($sarray, JSON_PRETTY_PRINT));
exit;
*/

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Alive Servers</title>
        <style>
            .red {color: #ff0000;}
            .blue { color: #0000ff; }
        </style>
    </head>
    <body>
<?php

$json = json_decode(file_get_contents("alive.json"), true);
$defport = array_key_exists("defport", $json) ? $json["defport"] : $defport;
$servers = $json["servers"];
$msg = "alive\n";
$html = "Last Update: ".date("Y-M-d ")."<span class=blue>".date("H:i:s")."</span><br><hr><table border=0 cellpadding=3px>";
$rows = array();
$online = 0;
$total = 0;
foreach( $servers as $server ){
    $name = $server["server"];
    $port = array_key_exists("port", $server) ? $server["port"] : $defport;
    $url = "tcp://$name:$port";
    $fp = @fsockopen($name, $port, $errno, $errstr, 3);
    $rmsg = "Online";
    $class = "blue";
    $total++;

    if (!$fp) {
        $rmsg = "missing";
        $class = "red";
    }
    else {
        fwrite( $fp, $msg );
        $resp = fgets($fp, 128);
        $rmsg = ($resp == $msg) ? "Online" : "Issues";
        fclose($fp);
        $online++;
    }
    $ip = "unknown";
    $ipa = dns_get_record($name,DNS_A);
    if( $ipa ) $ip = $ipa[0]["ip"]; //var_export($ipa);
    //if( $ipa ) $ip = print_r($ipa, true);

    $rows["$name"] = "<tr><td align=right class=$class>$name:</td><td>$rmsg<td><td>$ip</td><tr>";
}
asort($rows);
foreach( $rows as $row ){
    $html .= $row;
}
echo "$html</table><hr>".
     "<table border=0 cellpadding=3px>".
     "<tr><td>Online</td><td align=right>$online</td></tr>".
     "<tr><td>Offline</td><td align=right>".$total-$online."</td></tr>".
     "<tr><td>Total</td><td align=right>$total</td></tr>".
     "</table>\n";
?>
</body>
</html>
