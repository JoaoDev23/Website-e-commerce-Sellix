<?php

namespace KeyAuth;

session_start();

class api
{
    public string $name;
    public string $ownerid;

    public function __construct(string $name, string $ownerid)
    {
        $this->name = $name;
        $this->ownerid = $ownerid;
    }

    function init()
    {
        if ($this->name == "" || strlen($this->ownerid) != 10) {
            die("Go to <a href=\"https://keyauth.cc/app/\" target=\"blank\">https://keyauth.cc/app/</a> and click the <b>PHP</b> button in the App credentials code. Copy that & paste in <code style=\"background-color: #eee;border-radius: 3px;font-family: courier, monospace;padding: 0 3px;\">credentials.php</code>");
        }

        $data = [
            "type" => "init",
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $response = $this->req($data);

        if ($response == "KeyAuth_Invalid") {
            die("Go to <a href=\"https://keyauth.cc/app/\" target=\"blank\">https://keyauth.cc/app/</a> and click the <b>PHP</b> button in the App credentials code. Copy that & paste in <code style=\"background-color: #eee;border-radius: 3px;font-family: courier, monospace;padding: 0 3px;\">credentials.php</code>");
        }

        $json = json_decode($response);

        if ($json->message == "This program hash does not match, make sure you're using latest version") {
            die("You must disable hash check at <a href=\"https://keyauth.cc/app/?page=app-settings\" target=\"blank\">https://keyauth.cc/app/?page=app-settings</a>");
        }

        if (!$json->success)
            die($json->message);
        else if ($json->success) {
            $_SESSION['sessionid'] = $json->sessionid;
        }
    }

    function login(string $username, string $password)
    {
        $data = [
            "type" => "login",
            "username" => $username,
            "pass" => $password,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            unset($_SESSION['sessionid']);
            $this->error($json->message);
        } else if ($json->success)
            $_SESSION["user_data"] = (array)$json->info;

        return $json->success;
    }

    function register(string $username, string $password, string $key)
    {
        $data = [
            "type" => "register",
            "username" => $username,
            "pass" => $password,
            "key" => $key,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            unset($_SESSION['sessionid']);
            $this->error($json->message);
        } else if ($json->success)
            $_SESSION["user_data"] = (array)$json->info;

        return $json->success;
    }

    function license(string $key)
    {
        $data = [
            "type" => "license",
            "key" => $key,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            unset($_SESSION['sessionid']);
            $this->error($json->message);
        } else if ($json->success)
            $_SESSION["user_data"] = (array)$json->info;

        return $json->success;
    }

    function upgrade(string $username, string $key)
    {
        $data = [
            "type" => "upgrade",
            "username" => $username,
            "key" => $key,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            unset($_SESSION['sessionid']);
            $this->error($json->message);
        }

        return $json->success;
    }

    function var(string $varid)
    {
        $data = [
            "type" => "var",
            "varid" => $varid,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            unset($_SESSION['sessionid']);
            $this->error($json->message);
        } else if ($json->success)
            return $json->message;
    }

    function log(string $message)
    {
        $User = gethostname();

        $data = [
            "type" => "log",
            "pcuser" => $User,
            "message" => $message,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $this->req($data);
    }

    function setvar(string $varname, string $data)
    {
        $data = [
            "type" => "setvar",
            "var" => $varname,
            "data" => $data,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $this->req($data);
    }

    function getvar(string $varid)
    {
        $data = [
            "type" => "getvar",
            "var" => $varid,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            return null;
        } else if ($json->success)
            return $json->response;
    }

    function webhook(string $webid, string $param, string $body = "", string $conttype = "")
    {
        $data = [
            "type" => "webhook",
            "webid" => $webid,
            "params" => $param,
            "body" => $body,
            "conttype" => $conttype,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            return null;
        } else if ($json->success)
            return $json->response;
    }

    function FetchOnline() {
        $data = [
            "type" => "fetchOnline",
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            return null;
        } else if ($json->success)
            return $json->response;
    }

    function checkBlack() {
        $data = [
            "type" => "checkBlack",
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            return null;
        } else if ($json->success)
            return $json->response;
    }
    
    function Ban(string $reason) {
        $data = [
            "type" => "ban",
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid,
            "reason" => $reason
        ];

        $response = $this->req($data);
        $json = json_decode($response);

        if ($json->success) {
            return true;
        } else {
            $this->error($json->message);
            return false;
        }
    }

    function ChatGet(string $channel) {
        $data = [
            "type" => "chatget",
            "channel" => $channel,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            return null;
        } else if ($json->success)
            return $json->messages;
    }

    function ChatSend(string $message, string $channel) {
        $data = [
            "type" => "chatsend",
            "message" => $message,
            "channel" => $channel,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->ownerid
        ];

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            return null;
        } else if ($json->success)
            return $json->message;

    }

    private function req(array $data)
    {
        $curl = curl_init("https://keyauth.win/api/1.2/");
        curl_setopt($curl, CURLOPT_USERAGENT, "KeyAuth");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function error(string $msg)
    {
        echo '
                <script type=\'text/javascript\'>
                
                const notyf = new Notyf();
                notyf
                  .error({
                    message: \'' . addslashes($msg) . '\',
                    duration: 3500,
                    dismissible: true
                  });                
                
                </script>
                ';
    }

    public function success(string $msg)
    {
        echo '
                <script type=\'text/javascript\'>
                
                const notyf = new Notyf();
                notyf
                  .success({
                    message: \'' . addslashes($msg) . '\',
                    duration: 3500,
                    dismissible: true
                  });                
                
                </script>
                ';
    }
}


?>
