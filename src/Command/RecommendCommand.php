<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\RecommendService;
use App\Entity\Order;
use App\Entity\OrderItem;
use DateTime;
use DateTimeImmutable;

/**
 * Console command used to manually test the recommend service.
 *
 * docker compose run --rm php php bin/console app:recommend
 */
class RecommendCommand extends Command
{
    private $estimator;

    protected static $defaultName = 'app:recommend';

    public function __construct(RecommendService $recommend)
    {
        parent::__construct();

        $this->recommend = $recommend;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->recommend->scheme = RecommendService::SCHEME_WEIGHTED_SLOPEONE;
        // $this->recommend->scheme = RecommendService::SCHEME_COSINE;
        // $this->recommend->scheme = RecommendService::SCHEME_WEIGHTED_COSINE;

        $output->writeln("Ping Pong");
        $output->writeln(json_encode($this->recommend->product()));

        return Command::SUCCESS;
    }
}
