<?php
namespace Tests;

use Commision\Fee;
use Commision\LegalFee;
use Commision\NaturalFee;
use PHPUnit\Framework\TestCase; 

class CommisionTests extends TestCase
{
    const  INPUT_CASH_OUT = [
        ['2016-01-06',2,'legal','cash_out',300.00,'EUR'],
        ['2016-01-06',1,'natural','cash_out',30000,'JPY'],
        ['2016-01-07',1,'natural','cash_out',1000.00,'EUR'],
        ['2016-01-07',1,'natural','cash_out',100.00,'USD'],
        ['2016-01-10',1,'natural','cash_out',100.00,'EUR'],
        ['2016-01-10',3,'natural','cash_out',1000.00,'EUR'],
        ['2016-02-15',1,'natural','cash_out',300.00,'EUR'],
    ];
    
    const INPUT_CASH_IN = ['2016-01-05',1,'natural','cash_in',200.00,'EUR'];
    
    /**
     * @dataProvider GetCashOutNaturalFeeDataProvider
     */
    public function testGetCashOutNaturalFee($expected, $index) {
        
        $cashOutNaturalFee = new NaturalFee(static::INPUT_CASH_OUT[$index], static::INPUT_CASH_OUT, $index);
        $this->assertEquals($expected, $cashOutNaturalFee->getFee());
    }
  
    public function GetCashOutNaturalFeeDataProvider() {
        return [
            [0, 1],
            [0.7, 2],
            [0.3, 3],
            [0.3, 4],
            [0, 5],
            [0, 6]
        ];
    }
    
    public function testGetCashOutLegalFee() {
        $cashOutLegalFee = new LegalFee(static::INPUT_CASH_OUT[0]);
        $this->assertEquals(0.9, $cashOutLegalFee->getFee());
    }

    public function testGetCashInFee() {
        $cashInFee = new Fee();
        $this->assertEquals(0.06,$cashInFee->getCashInFee(static::INPUT_CASH_IN));
    }
    
}