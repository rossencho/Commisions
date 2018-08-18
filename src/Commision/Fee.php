<?php
namespace Commision;

use Conversion;
use Defines\Defines;
use Commision\LegalFee;

class Fee {
    
    public function getCashInFee(array $transaction): float {
        $fee = $transaction[Defines::AMMOUNT]*Defines::CASHIN_PERCENTAGE;
        $currency = $transaction[Defines::CURRENCY];
        $conversion = new Conversion();
        $feeInEur = $conversion->MakeLocalToEur($fee, $currency);
        $maxLocalFee = $conversion->MakeEurToLocal(Defines::MAX_CASHIN_FEE_EUR, $currency);
        
        if ($feeInEur > Defines::MAX_CASHIN_FEE_EUR) {
            return $maxLocalFee;
        } else {
            return $this->roundUpFee($fee);
        }
    }
    
    
    public function getCashOutFee(array $transaction, array $input, $index): float {
        $cashOutFee;
        if ($transaction[Defines::USER_TYPE] === "legal") {
            $cashOutFee = new LegalFee($transaction);
        } elseif ($transaction[Defines::USER_TYPE] === "natural") {
            $cashOutFee = new NaturalFee($transaction, $input, $index);
        }
        return $cashOutFee->getFee();
    }
    
    protected function roundUpFee($sum) {
        return ceil($sum*100)/100;
    }
}

