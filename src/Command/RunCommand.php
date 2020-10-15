<?php

namespace App\Command;

use App\Helper\RankHelper;
use App\Provider\DiscordMessageProvider;
use App\Service\DatabaseService;
use App\Service\DiscordService;
use App\Service\RiotApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class RunCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'run';

    protected function configure()
    {
        // the short description shown while running "php bin/console list"
        $this->setDescription('Checks all the summoners in database for updates in rank and pushes changes to Discord.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Set up database connection
        $dbService = new DatabaseService(
            $_ENV["DATABASE_HOST"],
            $_ENV["DATABASE_USERNAME"],
            $_ENV["DATABASE_PASSWORD"],
            $_ENV["DATABASE_NAME"]
        );

        $summoners = $this->getSummoners($dbService);
       
        foreach ($summoners as $summoner) {
            // @todo region should not be defined for the whole api class
            //  since we would be creating and destructing classes
            //     "on the walking tyre" (aan de lopende band)
            $riotApi = new RiotApi($summoner["region"]);
            $output->writeln("Getting rank for " . $summoner["name"]);
            $ranks = $riotApi->getRank($summoner["id"]);
            
            foreach ($ranks as $rank) {
                $newRank = RankHelper::getSummarizedRank($rank["tier"], $rank["rank"]);

                if ($rank["queueType"] === "RANKED_SOLO_5x5") {
                    // Check if summoner has a rank at all
                    if (!$summoner["rank_solo"]) {

                        $dbService->updateSoloRank($summoner["id"], $newRank);

                    } elseif ($summoner["rank_solo"] !== $newRank) {
                        // Check if higher or lower, then notify
                        $isHigher = RankHelper::isRankHigher($summoner["rank_solo"], $newRank);

                        $discordService = new DiscordService;
                        $discordMessageProvider = new DiscordMessageProvider($discordService);
                        if ($isHigher) {
                            // Send promote message
                            $discordMessageProvider->sendPromoteMessage($summoner["name"], $newRank, $rank["queueType"]);
                        } else {
                            // Send demote message
                            $discordMessageProvider->sendDemoteMessage($summoner["name"], $newRank, $rank["queueType"]);
                        }

                        $dbService->updateSoloRank($summoner["id"], $newRank);
                    }

                } elseif ($rank["queueType"] === "RANKED_FLEX_SR") {
                    // Check if summoner has a rank at all
                    if (!$summoner["rank_flex"]) {

                        $dbService->updateFlexRank(
                            $summoner["id"],
                            RankHelper::getSummarizedRank($rank["tier"], $rank["rank"])
                        );

                    } elseif ($summoner["rank_flex"] !== $newRank) {
                        // Check if higher or lower, then notify
                        $isHigher = RankHelper::isRankHigher($summoner["rank_flex"], $newRank);

                        $discordService = new DiscordService;
                        $discordMessageProvider = new DiscordMessageProvider($discordService);
                        if ($isHigher) {
                            // Send promote message
                            $discordMessageProvider->sendPromoteMessage($summoner["name"], $newRank, $rank["queueType"]);
                        } else {
                            // Send demote message
                            $discordMessageProvider->sendDemoteMessage($summoner["name"], $newRank, $rank["queueType"]);
                        }

                        $dbService->updateFlexRank($summoner["id"], $newRank);
                    }
                }
            }
        }

        return Command::SUCCESS;
    }

    private function getSummoners($dbService) {
        return $dbService->getSummoners();
    }

}