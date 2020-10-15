<?php

namespace App\Helper;


class QueueHelper {
    const FANCY_QUEUE_NAMES = [
        "RANKED_SOLO_5x5" => "solo queue",
        "RANKED_FLEX_SR" => "flex queue"
    ];

    public static function getFancyQueueName($queueName) {
        return self::FANCY_QUEUE_NAMES[$queueName];
    }
}