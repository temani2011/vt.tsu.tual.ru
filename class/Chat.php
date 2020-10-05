<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
require_once('../vendor/autoload.php');
require_once('../class/db.php');
class Chat implements MessageComponentInterface {
    protected $clients;
    private $subscriptions;
    private $users;
    protected $dal;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
        $this->users = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $conn, $data) {
        $dal = new DAL();
        $data = json_decode($data);
        switch ($data->command) {
            case "subscribe":
                $this->subscriptions[$conn->resourceId] = $data->channel; echo "subscriptions[{$conn->resourceId}] = {$data->channel}";
            break;
            case "message":
                if (isset($this->subscriptions[$conn->resourceId])) {
                    $user = $dal->get_user_information_by_id($data->id);
                    if($dal->set_message_in_dialog($data->id, $data->did, $data->message))
                    {
                        $date = date('H:i d.m.Y');
                        $div = '<table id="chats" class="table table-striped">
                        <tbody>
                        <tr>
                        <td valign="top">
                        <div style="display: inline;">
                        <strong>' . $user[0]['surname'] . " " . $user[0]['name'] . " " . $user[0]['midname'] . '</strong>
                        <div>' . $data->message . '</div>
                        <td align="right" valign="top">
                        </div>' . $date . 
                        '</td>
                        </tr>
                        </tbody>
                        </table>';
                    }
                    else
                    {
                        $div = '<div class="alert alert-warning msg" role="alert">
                        Не удалось добавить сообщение
                        </div>';
                    }
                    $target = $this->subscriptions[$conn->resourceId];
                    foreach ($this->subscriptions as $id=>$channel) {
                        if ($channel == $target) {
                            $this->users[$id]->send($div);
                        }
                    }
                }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
        unset($this->subscriptions[$conn->resourceId]);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

$server->run();
?>