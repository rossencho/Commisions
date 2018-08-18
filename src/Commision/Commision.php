<?php
namespace Commision;
require "vendor/autoload.php";

use Defines\Defines;
use Commision\Fee;

class Commision {
    
    private $inputData;
//    private $cashInData;
//    private $cashOutData;
    public function __construct() {
        $this->inputData = [];
    }
    
    public function readInputData($fileName) {
        if (($file = \fopen($fileName, "r")) !== FALSE) {
            $index=0;
            while(!feof($file)) {
                foreach (fgetcsv($file) as $line) {
                    $this->inputData[$index][] = $line;
                }
                $index++;
            }
            fclose($file);
        }
    }
    
//    public function setOperationsData() {
//        foreach ($this->inputData as $transaction) {
//            if ($transaction[Defines::OPERATION_TYPE] === "cash_in") {
//                $this->cashInData[] = $transaction;
//            } elseif ($transaction[Defines::OPERATION_TYPE] === "cash_out") {
//                $this->cashOutData[] = $transaction;
//            }
//        }
//    }
    
    public function processData() {
        $fee = new Fee();
        $num=0;
        foreach ($this->inputData as $transaction) {
            if ($transaction[Defines::OPERATION_TYPE] === "cash_in") {
                $this->outputResult($fee->getCashInFee($transaction));
            } elseif ($transaction[Defines::OPERATION_TYPE] === "cash_out") {
                $this->outputResult($fee->getCashOutFee($transaction, $this->inputData, $num));
            } else {
                throw new \DomainException(sprintf("Unrecognized financial operation (%s) ", $transaction[Defines::OPERATION_TYPE]));
            }
            $num++;
        }
    }

    public function outputResult(string $text) {
        $handle = fopen( 'php://stdout', 'w' ) ;
        fwrite( $handle, $text."\n");
        fclose( $handle );
    }
    
}

$commision = new Commision();
$commision->readInputData($argv[1]);
$commision->processData();





