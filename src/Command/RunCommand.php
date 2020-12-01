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

        // Set up discord and discord message service
        $discordService = new DiscordService;
        $discordMessageProvider = new DiscordMessageProvider($discordService);

        // Get all summoner info
        $summoners = $dbService->getSummoners();
       
        foreach ($summoners as $summoner) {
            // @todo region should not be defined for the whole api class
            //  since we would be creating and destructing classes
            //     "on the walking tyre" (aan de lopende band)
            $riotApi = new RiotApi($summoner["region"]);
            $output->writeln("Getting rank for " . $summoner["name"]);
            $ranks = $riotApi->getRank($summoner["id"]);
            
            foreach ($ranks as $rank) {
                
                // Get fancy rank name
                $newRank = RankHelper::getSummarizedRank($rank["tier"], $rank["rank"]);

                // Get current rank from database
                $currentRankAndLp = $dbService->getCurrentRank($summoner["id"], $rank["queueType"]);

                // If no rank is set, update into database and continue
                if (!$currentRankAndLp) {
                    $dbService->updateRank($summoner["id"], $rank["queueType"], $newRank, $rank["leaguePoints"]);
                    continue;
                }

                // If current rank is set but doesnt matches new rank, update in database
                if ($currentRankAndLp["rank"] != $newRank) {
                    if (RankHelper::isRankHigher($currentRankAndLp["rank"], $newRank)) {
                        $discordMessageProvider->sendPromoteMessage($summoner["name"], $newRank, $rank["queueType"]);
                    } else {
                        $discordMessageProvider->sendDemoteMessage($summoner["name"], $newRank, $rank["queueType"]);
                    }
                    $dbService->updateRank($summoner["id"], $rank["queueType"], $newRank, $rank["leaguePoints"]);
                } elseif ($currentRankAndLp["lp"] != $rank["rank"]) {
                    $dbService->updateRank($summoner["id"], $rank["queueType"], $newRank, $rank["leaguePoints"]);
                }
                
            }
        }

        return Command::SUCCESS;
    }

}