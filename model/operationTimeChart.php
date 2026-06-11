
<?php

class OperationTimeChart{

    private $quantity;
    private $totalMinutes;
    private $average;

    public function setQuantity($quantity){
        $this->quantity = $quantity;
    }
    public function getQuantity(){
        return $this->quantity;
    }

    public function setTotalMinutes($totalMinutes){
        $this->totalMinutes = $totalMinutes;
    }
    public function getTotalMinutes(){
        return $this->totalMinutes;
    }

    public function setAverage($average){
        $this->average = $average;
    }
    public function getAverage(){
        return $this->average;
    }
}
?>