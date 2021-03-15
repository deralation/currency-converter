<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{   
    public function testThatExchangeCalculationWork(){
        $data = array(
            "success" => true,
            "timestamp" => 1615792326,
            "base" => "EUR",
            "date" => "2021-03-15",
            "rates" => array(
                "DKK" => 7.4361740000000003,
                "USD" => 1.192985
            ),
            "sourceAmount" => 60
        );

        $converter = new Converter("DKK","USD");
        $converter->setSourceAmount(60);
        $result = $converter->calculationOfTargetAmount($data);

        $this->assertEquals(9.6257968143295187,$result["amount"]);
    }
}