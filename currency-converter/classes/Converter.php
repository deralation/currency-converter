<?php

class Converter {
   
  public static $converterUrl = array(
    "endpoint" => "http://data.fixer.io/api/",
    "accessToken" => "74912690979f1f9ac90d73af2e7b3d6f"
  );

  private datetime $date;
  public int|float $rate;
  private int|float $sourceAmount;
  private int|float $targetAmount;

  public function __construct(
      public string $sourceCurrency,
      public string $targetCurrency,
      
    ) {
      $this->sourceCurrency = $sourceCurrency;
      $this->targetCurrency = $targetCurrency;
    }

  public function setDate($date){
    $this->date = $date;
  }

  public function setRate($rate){
    $this->rate = $rate;
  }

  public function setSourceAmount($amount){
    $this->sourceAmount = $amount;
  }

  public function setTargetAmount($amount){
    $this->targetAmount = $amount;
  }

  public function getDate(){
    return $this->date;
  }

  public function getRate(){
    return $this->rate;
  }

  public function getLatestRate(){
    try {

      if(!isset($this->sourceCurrency))
        throw new Exception("Please provice source currency");

      if(!isset($this->targetCurrency))
        throw new Exception("Please provice target currency");

      if(!isset($this->sourceAmount))
        throw new Exception("Please provide amount you like to convert");

      $client = new \GuzzleHttp\Client();
      $response = $client->request('GET', SELF::$converterUrl["endpoint"].'latest?access_key='.SELF::$converterUrl["accessToken"]."&symbols=".$this->sourceCurrency.",".$this->targetCurrency."&format=1");

      if($response->getStatusCode()!=200)
        throw new Exception("Could not get the latest currency rates");
      
      $response->getHeaderLine('content-type');
      $json = $response->getBody()->getContents();

      $return = json_decode($json);
      $data = json_decode(json_encode($return), true);
 
      $amount = $this->calculationOfTargetAmount($data);
      
      $data["rate"] = $amount["rate"];
      $data["targetAmount"] = $amount["amount"];
    
      $currencyData = $this->add($data);

      return $currencyData;

    } catch (Exception $e) {
      $this->errorMessage = $e->getMessage();
      return false;
    }
  }

  public function add($data){
    try {

      if(!is_array($data)){
        throw new Exception("Unknown parameters for Converters Table");
      }

      global $database;

      // Mapped data to db table
      $array = array(
        "createDate" => date("Y-m-d H:i:s"),
        "date" => $data["date"],
        "sourceCurrency" => $this->sourceCurrency,
        "targetCurrency" => $this->targetCurrency,
        "baseCurrency" => $data["base"],
        "sourceAmount" => $this->sourceAmount,
        "targetAmount" => $data["targetAmount"],
        "rate"  => $data["rate"]
      );

      if(!$database->insert("Converters",$array))
        throw new Exception("Could not saved to the database.");

      $lastId = $database->getInsertID();

      $insertedData = array_values($database->get("Converters",$lastId));
    
      return $insertedData;

    } catch (Exception $e) {
      $this->errorMessage = $e->getMessage();
      return false;
    }
  }

  // calculation of source currency to target currency
  public function calculationOfTargetAmount($data){
    try {

      if(!is_array($data))
        throw new Exception("Unknown input");

      if(!isset($data["rates"]))  
        throw new Exception("Unknown rates");

      if(!isset($this->sourceCurrency))
        throw new Exception("Unknown source currency");

      if(!isset($this->targetCurrency))
        throw new Exception("Unknown target currency");

      $rate = $data["rates"][$this->sourceCurrency]>$data["rates"][$this->targetCurrency] ? $data["rates"][$this->sourceCurrency] / $data["rates"][$this->targetCurrency]:$data["rates"][$this->targetCurrency]/$data["rates"][$this->sourceCurrency];
      $value = $this->sourceAmount>$rate ? $this->sourceAmount/$rate:$this->sourceAmount*$rate;
      
      $return = array(
        "rate" => $rate,
        "amount" => $value
      );

      return $return; 

    } catch (Exception $e) {
      $this->errorMessage = $e->getMessage();
      return false;
    }
  }

}