<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\WalletRepository;
use App\Repository\OperationRepository;
use App\Entity\Operation;
use App\Entity\Wallet;

class OperationController extends AbstractController
{
    private $operationRepository;
    private $walletRepository;
    
    public function __construct(OperationRepository $operationRepository, WalletRepository $walletRepository)
    {
        $this->operationRepository=$operationRepository;
        $this->walletRepository=$walletRepository;
    }
    
    /**
     * @Route("/api/operation/add", name="api_operation_add", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        return $this->operation($request);
    }
    
    /**
     * @Route("/api/operation/substract", name="api_operation_substract", methods={"POST"})
     */
    public function substract(Request $request): JsonResponse
    {
        return $this->operation($request, 'substract');
    }
    
    private function operation(Request $request, string $operation = 'add')
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['walletId']) || empty($data['value'])) {
           return new JsonResponse(['message' => 'The wallet ID is mandatory!'], Response::HTTP_BAD_REQUEST); 
        }
        
        $walletId = (int)$data['walletId'];
        $value = (int)($data['value']*100);
        $status = 'Add operation saved!';
        if($operation == 'substract') {
            $value *= -1;
            $status = 'Substract operation saved!';
        }
        
        $wallet = $this->walletRepository->find($walletId);
        $this->saveOperation($wallet, $value);
        $this->walletRepository->updateWalletSum($wallet);
        
        return new JsonResponse(['status' => $status], Response::HTTP_CREATED);
    }

    private function saveOperation(Wallet $wallet, int $value): void
    {
        $operation = new Operation();
        $operation->setWallet($wallet);
        $operation->setAmount($value);
        $operation->setDate(new \DateTime());
        $this->operationRepository->add($operation, true);
        
    }
    
}
