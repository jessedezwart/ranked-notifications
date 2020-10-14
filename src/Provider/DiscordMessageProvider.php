<?php

namespace App\Provider;

class DiscordMessageProvider {

    /**
     * @var App\Service\DiscordService
     */
    protected $discordService;

    public function __construct($discordService)
    {
        $this->discordService = $discordService;
    }

    public function sendPromoteMessage($summonerName, $rank) {
        $this->discordService->sendMessage(
            "Gefeliciteerd, $summonerName!",
            "$summonerName is gepromote naar $rank.",
            3066993, // green
            $this->getRankImageUrl($rank)
        );
    }

    public function sendDemoteMessage($summonerName, $rank) {
        $this->discordService->sendMessage(
            "Helaas, $summonerName!",
            "$summonerName is gedemote naar $rank.",
            15158332, // red
            $this->getRankImageUrl($rank)
        );
    }

    private function getRankImageUrl($rank) {
        return $_ENV["RANK_IMAGE_HOST"] . str_replace(" ", "_", $rank) . ".png";
    }

}