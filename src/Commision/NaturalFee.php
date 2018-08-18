<?php

namespace Commision;

use Defines\Defines;
use Commision\Fee;
use Conversion;

class NaturalFee extends Fee {
    private $transaction;
    private $inputData;
    private $index;
    
    public function __construct(array $transaction, array $inputData, $index) {
        $this->transaction = $transaction;
        $this->inputData = $inputData;
        $this->index = $index;
    }
    
    
    /**
     * Counts Weekly transactions, preparing it for Bonus check
     * @param int $userId
     * @return array
     */           
    private function countWeekOperationsPerUser(int $userId): array {
        $count = 0;
        $sum = 0;
        $converion = new Conversion();
        $current = strtotime($this->inputData[$this->index][Defines::DATE]);
        for ($i = $this->index; $i>=0; $i--) {
            $previous = strtotime($this->inputData[$i][Defines::DATE]);
            $currency = $this->inputData[$i][Defines::CURRENCY];
            if (    $this->inputData[$i][Defines::ID] == $userId && 
                    $this->inputData[$i][Defines::OPERATION_TYPE] == "cash_out" &&
                    date('oW', $current) === date('oW', $previous) &&
                    date('Y', $current) === date('Y', $previous)
                ) {
                $count++;
                $sum += $converion->MakeLocalToEur($this->inputData[$i][Defines::AMMOUNT], $currency);
            }
        }
        return ["count"=>$count, "sum"=>$sum];
    }
    
    /**
     * Calculates the Fee, depending on bonus schema 
     * @param type $userId
     * @return float
     */
    private function calculateFee($userId): float {
        $currency = $this->transaction[Defines::CURRENCY];
        $weekOperations = $this->countWeekOperationsPerUser($userId);
        $conversion = new Conversion();
        if ($weekOperations["count"] < 4 && $weekOperations["sum"] <= 1000 ) {
            return $this->roundUpFee(0);
        } elseif ($weekOperations["count"] < 4 && $weekOperations["sum"] > 1000) {
            $currentAmmountInEur = $conversion->MakeLocalToEur($this->transaction[Defines::AMMOUNT], $currency);
            //check if previous sum of transactions exceeded the limit of 1000 so we put tax on the whole current ammount
            if ($weekOperations["sum"] - $currentAmmountInEur > 1000) {
                return $this->transaction[Defines::AMMOUNT]*Defines::CASHOUT_PERCENTAGE;
            } else {
                $result = ($weekOperations["sum"] - 1000)*Defines::CASHOUT_PERCENTAGE;
                return $conversion->MakeEurToLocal($result, $currency);
            }
        } elseif ($weekOperations["count"] >= 4) {
            return $this->transaction[Defines::AMMOUNT]*Defines::CASHOUT_PERCENTAGE;
        }
    }
     
    /**
     * Main fee method
     * @return type
     */
    public function getFee() {
        $fee = $this->calculateFee($this->transaction[Defines::ID]);
        return $this->roundUpFee($fee);
    }
}