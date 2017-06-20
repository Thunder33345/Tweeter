<?php

namespace NFGamerMC;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;
use NFGamerMC\TwitterAPI\TwitterAPIExchange;

class Base extends PluginBase implements Listener{
    public function onLoad(){
        $this->getLogger()->info("Plugin Loading");
    }
    public function onEnable()
    {
        $this->saveDefaultConfig();
        $this->reloadConfig();
        $settings = array(
            'oauth_access_token' => $this->getConfig()->get("access-token"),
            'oauth_access_token_secret' => $this->getConfig()->get("access-secret"),
            'consumer_key' => $this->getConfig()->get("consumer-key"),
            'consumer_secret' => $this->getConfig()->get("consumer-secret")
        );
        $url = 'https://api.twitter.com/1.1/statuses/update.json';
        $requestMethod = 'POST';
        $postfields = array(
            'status' => $this->getConfig()->get("startup"),
        );
        $twitter = new TwitterAPIExchange($settings);
        echo $twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest(true, [
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
    }
    public function onDisable(){
        $this->getLogger()->info("Plugin Disabled");
        $settings = array(
            'oauth_access_token' => $this->getConfig()->get("access-token"),
            'oauth_access_token_secret' => $this->getConfig()->get("access-secret"),
            'consumer_key' => $this->getConfig()->get("consumer-key"),
            'consumer_secret' => $this->getConfig()->get("consumer-secret")
        );
        $url = 'https://api.twitter.com/1.1/statuses/update.json';
        $requestMethod = 'POST';
        $msg = (implode(" ", array_slice($args, 0)));
        $postfields = array(
            'status' => $this->getConfig()->get("shutdown"),
        );
        $twitter = new TwitterAPIExchange($settings);
        echo $twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest(true, [
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
    }
    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        switch (strtolower($command->getName())) {
            case "post":
                if ($sender instanceof Player) {
                    $settings = array(
                        'oauth_access_token' => $this->getConfig()->get("access-token"),
                        'oauth_access_token_secret' => $this->getConfig()->get("access-secret"),
                        'consumer_key' => $this->getConfig()->get("consumer-key"),
                        'consumer_secret' => $this->getConfig()->get("consumer-secret")
                    );
                    $url = 'https://api.twitter.com/1.1/statuses/update.json';
                    $requestMethod = 'POST';
                    $msg = (implode(" ", array_slice($args, 0)));
                    $postfields = array(
                        'status' => $msg,
                    );
                    $twitter = new TwitterAPIExchange($settings);
                    echo $twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest(true, [
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_SSL_VERIFYPEER => false
                    ]);
                    $sender->sendMessage(C::AQUA . C::BOLD . "Twitter: " . C::RESET . C::GRAY . "Sent " . $msg);
                    break;
                } else {
                    $sender->sendMessage(C::DARK_RED . "Please run this command IN-GAME!");
                }
                return true;
            case "get":
                if ($sender instanceof Player) {
                    $settings = array(
                        'oauth_access_token' => $this->getConfig()->get("access-token"),
                        'oauth_access_token_secret' => $this->getConfig()->get("access-secret"),
                        'consumer_key' => $this->getConfig()->get("consumer-key"),
                        'consumer_secret' => $this->getConfig()->get("consumer-secret")
                    );
                    $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
                    $requestMethod = "GET";
                    $user = (implode(" ", array_slice($args, 0)));
                    $count = (implode(" ", array_slice($args, 1)));
                    $getfield = '?screen_name=' . $user . '&count=' . $count;
                    $twitter = new TwitterAPIExchange($settings);
                    $response = $twitter->setGetfield($getfield)
                        ->buildOauth($url, $requestMethod)
                        ->performRequest(true, [
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_SSL_VERIFYPEER => false
                        ]);
                    $tweets = json_decode($response);

                    foreach ($tweets as $tweet) {
                        $sender->sendMessage(C::AQUA . C::BOLD . "Twitter:" . C::RESET . " " . $tweet->text);
                    }
                } else {
                    $sender->sendMessage(C::DARK_RED . "Please run this command IN-GAME!");
                }
                return true;
            case "dm":
                if ($sender instanceof Player) {
                    $settings = array(
                        'oauth_access_token' => $this->getConfig()->get("access-token"),
                        'oauth_access_token_secret' => $this->getConfig()->get("access-secret"),
                        'consumer_key' => $this->getConfig()->get("consumer-key"),
                        'consumer_secret' => $this->getConfig()->get("consumer-secret")
                    );
                    $url = 'https://api.twitter.com/1.1/direct_messages/new.json';
                    $requestMethod = 'POST';
                    $user = (implode(" ", array_slice($args, 0)));
                    $msg = (implode(" ", array_slice($args, 1)));
                    $postfields = array(
                        'screen_name' => $args[0],
                        'text' => $msg,
                    );
                    $twitter = new TwitterAPIExchange($settings);
                    echo $twitter->buildOauth($url, $requestMethod)->setPostfields($postfields)->performRequest(true, [
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_SSL_VERIFYPEER => false
                    ]);
                } else {
                    $sender->sendMessage("Please run this command IN-GAME!");
                }
                return true;
        }
    }
}
