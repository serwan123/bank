<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\WalletRepository;
use App\Repository\AccountRepository;
use App\Entity\Wallet;

class WalletController extends AbstractController
{
    private $walletRepository;
    private $accountRepository;
    
    public function __construct(WalletRepository $walletRepository, AccountRepository $accountRepository)
    {
        $this->walletRepository=$walletRepository;
        $this->accountRepository=$accountRepository;
    }
    
    /**
     * @Route("/api/wallet", name="api_wallet_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['accountNumber'])) {
           return new JsonResponse(['message' => 'The account number is mandatory!'], Response::HTTP_BAD_REQUEST); 
        }
        
        $accountNumber = $data['accountNumber'];
        
        $account = $this->accountRepository->findOneByNumber($accountNumber);
        $wallet = new Wallet();
        $wallet->setAccount($account);
        $this->walletRepository->add($wallet, true);
        
        return new JsonResponse(['status' => 'Wallet created! Wallet ID: '.$wallet->getId()], Response::HTTP_CREATED);
    }
    
    /**
     * @Route("/api/wallet/{id}", name="api_wallet_balance", methods={"GET"})
     */
    public function balance($id)
    {
        $wallet = $this->walletRepository->find($id);
        //if($wallet)
        $data = ['id' => $wallet->getId(), 'accountId' => $wallet->getAccount()->getId(), 'balance' => $wallet->getSum()/100];
        
        return new JsonResponse($data, Response::HTTP_OK);
    }
}
