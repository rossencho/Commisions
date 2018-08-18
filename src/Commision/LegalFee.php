<?php
namespace Commision;

use Defines\Defines;
use Commision\Fee;
use Conversion;

class LegalFee extends Fee {
    private $transaction;
    
    public function __construct(array $transaction) {
        $this->transaction = $transaction;
    }
    
    /**
     * Main fee method
     * @return float
     */
    public function getFee() {
        $fee = $this->transaction[Defines::AMMOUNT]*Defines::CASHOUT_PERCENTAGE;
        $currency = $this->transaction[Defines::CURRENCY];
        $conversion = new Conversion();
        $feeInEur = $conversion->MakeEurToLocal($fee, $currency);
        $minLocalFee = $conversion->MakeEurToLocal(Defines::MIN_CASHOUT_LEGAL_FEE_EUR, $currency);
        
        if ($feeInEur < Defines::MIN_CASHOUT_LEGAL_FEE_EUR) {
            return $minLocalFee;
        } else {
            return $this->roundUpFee($fee);
        }
    }
}

