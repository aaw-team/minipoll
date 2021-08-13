<?php
declare(strict_types = 1);

return [
    \AawTeam\Minipoll\Domain\Model\Answer::class => [
        'tableName' => 'tx_minipoll_answer',
    ],
    \AawTeam\Minipoll\Domain\Model\Participation::class => [
        'tableName' => 'tx_minipoll_participation',
    ],
    \AawTeam\Minipoll\Domain\Model\Poll::class => [
        'tableName' => 'tx_minipoll_poll',
    ],
    \AawTeam\Minipoll\Domain\Model\PollOption::class => [
        'tableName' => 'tx_minipoll_poll_option',
    ],
];
