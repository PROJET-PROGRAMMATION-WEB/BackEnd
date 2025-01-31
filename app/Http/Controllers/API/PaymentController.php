<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CinetPayService;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private $cinetpayService;
    
    public function __construct(CinetPayService $cinetpayService)
    {
        $this->cinetpayService = $cinetpayService;
    }
    
    public function initiate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email'
        ]);

        try {
            $transactionId = 'TRX-' . Str::random(100);
            
            $response = $this->cinetpayService->initiatePayment(
                $request->amount,
                $transactionId,
                $request->customer_name,
                $request->customer_email
            );
            
            // Sauvegarder la transaction dans votre base de données
            Payment::create([
                'transaction_id' => $transactionId,
                'amount' => $request->amount,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
            ]);
            // ...
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function notify(Request $request)
    {
        \Log::info('CinetPay Notification:', $request->all());
        
        try {
            $paymentStatus = $this->cinetpayService->verifyPayment($request->transaction_id);
            
            if ($paymentStatus['status'] == 'ACCEPTED') {
                // Mettre à jour le statut de la transaction dans votre base de données
                // Envoyer une confirmation par email
                // ...
            }
            
            return response()->json(['status' => 'success']);
            
        } catch (\Exception $e) {
            \Log::error('CinetPay Notification Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }
    
    public function return(Request $request)
    {
        // Gérer le retour du client après le paiement
        return response()->json([
            'status' => 'success',
            'message' => 'Payment process completed'
        ]);
    }
}
