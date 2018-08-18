<?php

class Conversion {
    const EUR_USD = 1.1497;
    const EUR_JPY = 129.53;
    
    public function UsdToEur($sum) {
        return ($sum !== 0)?$sum/static::EUR_USD:0;
    }
    
     public function JpyToEur($sum) {
        return ($sum !== 0)?$sum/static::EUR_JPY:0;
    }
    
    public function EurToUsd($sum) {
        return $sum*static::EUR_USD;
    }
    
     public function EurToJpy($sum) {
        return $sum*static::EUR_JPY;
    }
    
    public function MakeLocalToEur($sum, $currency) {
        $result = $sum;
       
        if ($currency == "USD") {
            $result = $this->UsdToEur($sum);
        } elseif ($currency == "JPY") {
            $result = $this->JpyToEur($sum);
        }
        return $result;
    }
    
    public function MakeEurToLocal($sum, $currency) {
        $result = $sum;
        if ($currency == "USD") {
            $result = $this->EurToUsd($sum);
        } elseif ($currency === "JPY") {
            $result = $this->EurToJpy($sum);
        }
        return $result;
    }
        
    
}
