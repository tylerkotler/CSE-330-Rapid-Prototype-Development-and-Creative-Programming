<?php
$num1 = $_GET["num1"];
$num2 = $_GET["num2"];
$operation = (String)$_GET["operation"];
$result = 0;
if(is_numeric($num1) && is_numeric($num2)){
    if($operation == "plus"){
        $result = $num1+$num2;
    }
    elseif($operation == "minus"){ 
        $result = $num1-$num2;
    }
    elseif($operation == "multiply"){
        $result = $num1*$num2; 
    }
    else if($operation == "divide"){
        if($num2==0){
            $result = "undefined"; 
        }
        else{
            $result = $num1/$num2; 
        }
    }
    else{
        $result = "choose an operation";
    }
    if($result > 1.8E+308 || $result < -1.8E+308){
        $result = "infinity";
    }
    echo htmlentities("The result: $result");
}
else{
    echo htmlentities("Bad input - enter two numbers");
}
?>


