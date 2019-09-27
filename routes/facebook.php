<?php
$botman->hears('multi response', function (BotMan $bot) {
    $bot->reply("Tell me more!");
    $bot->reply("And even more");
});