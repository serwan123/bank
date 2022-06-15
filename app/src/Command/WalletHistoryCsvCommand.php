<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\WalletRepository;

class WalletHistoryCsvCommand extends Command
{
    protected static $defaultName = 'wallet:history-csv';
    protected static $defaultDescription = 'Generates operations history on wallet in csv file.';
    private $walletRepository;
    
    public function __construct(WalletRepository $walletRepository)
    {
        $this->walletRepository=$walletRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('walletId', InputArgument::REQUIRED, 'The ID of the wallet.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $walletId=$input->getArgument('walletId');
        $wallet = $this->walletRepository->find($walletId);
        
        if(is_null($wallet)) {
            $io->error('Wallet not found!');
            return Command::INVALID;
        }
        
        $operations = $wallet->getOperations();
        
        $header=['Date', 'Amount'];
        $f = fopen("/var/www/app/csv/operations_history".date("YmdHis").".csv", "w+");
        fputcsv($f, $header);

        foreach($operations as $operation) {
            $fields=[$operation->getDate()->format("Y-m-d"), $operation->getAmount()];
            fputcsv($f, $fields);
        }
        fclose($f);

        $io->success('Operations history generated.');

        return Command::SUCCESS;
    }
}
