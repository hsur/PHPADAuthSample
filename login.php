<?php

// Simple Active Directory Authentication written in PHP
// Based on https://www.exchangecore.com/blog/how-use-ldap-active-directory-authentication-php/

//debug
//ini_set( 'display_errors', 1 );

$adServer = "ldap://###LDAP_SERVER_NAME_OR_IP###";
$netbiosName = "###NETBIOS_DOMAIN###";
$baseDn = '###BASE_DN###';

header("Content-Type: text/html; charset=UTF-8");
if(isset($_POST['username']) && isset($_POST['password']) && $_POST['username'] != '' && $_POST['password'] != '' ){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $ldaprdn = $netbiosName . "\\" . $username;

    $ldap = ldap_connect($adServer);
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
    $bind = @ldap_bind($ldap, $ldaprdn, $password);

    if ($bind) {
        $filter="(sAMAccountName=$username)";
        $result = ldap_search($ldap, $baseDn, $filter);
        ldap_sort($ldap,$result,"sn");
        $info = ldap_get_entries($ldap, $result);
        for ($i=0; $i<$info["count"]; $i++)
        {
            if($info['count'] > 1)
                break;
            echo "<p>You are accessing <strong> ". $info[$i]["sn"][0] .", " . $info[$i]["givenname"][0] ."</strong> (" . $info[$i]["samaccountname"][0] .")</p>\n";
            echo '<pre>';
            var_dump($info);
            echo '</pre>';
            $userDn = $info[$i]["distinguishedname"][0];
        }
        @ldap_close($ldap);
    } else {
        $msg = "Login failed.";
        echo $msg;
    }

}else{
?>
<form action="#" method="POST">
        <label for="username">Username: </label><input id="username" type="text" name="username" />
        <label for="password">Password: </label><input id="password" type="password" name="password" />
        <input type="submit" name="submit" value="Submit" />
</form>
<?php } ?>
