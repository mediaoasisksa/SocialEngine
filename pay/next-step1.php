<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Billing Information</title>
 <meta name="viewport" content="width=device-width, initial-scale=1.0" >
 <!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
<style type="text/css">
body{
  background-color:#f8f9fa;
  padding:0;
  margin:0;
}
.wpwl-container {
    margin-top:50px;
}
</style>
</head>
<body>
<?php


function request() {
	$url = "https://eu-prod.oppwa.com/v1/checkouts";
    
    
    $data = $_POST;
    $data['entityid'] = "8acda4ca87e645180187fa20f303477a";
    if($data['card'] == "mada") {
        $data['entityid'] = "8acda4ca87e645180187fa217298477f";
    }
    
    
    $data['merchantTransactionId']=$data['id'];
    $data['givenName']=$data['givenName'];
    $data['surname']=$data['surname'];
    $data['email']=$data['email'];
    $data['mobile']=$data['mobile'];
    $data['street1']= $data['street1'];
    $data['city']=$data['city'];
    $data['state']=$data['state'];
    $data['country']=$data['country'];
    $data['postcode']=$data['zipcode'];
    $data['amount']= $data['price'];
    $data['currency']='SAR';
    $requestData = "entityId=" .$data['entityid'].
        "&amount=".$data['amount'].
        "&currency=".$data['currency'].
        "&paymentType=DB".
        "&merchantTransactionId=".$data['merchantTransactionId'].
        "&customer.givenName=".$data['givenName'].
        "&customer.surname=".$data['surname'].
        "&customer.mobile=".$data['mobile'].
        "&customer.email=".$data['email'].
        "&billing.street1=".$data['street1'].
        "&billing.city=".$data['city'].
        "&billing.state=".$data['state'].
        "&billing.country=".$data['country'].
        "&billing.postcode=".$data['zipcode']
        ;
   // $requestData = $requestData . "&testMode=EXTERNAL";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Authorization:Bearer OGFjZGE0Y2E4N2U2NDUxODAxODdmYTIwNzk4YTQ3NzV8MzlReW54blhqUA=='));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);// this should be set to true in production
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$responseData = curl_exec($ch);
	if(curl_errno($ch)) {
		return curl_error($ch);
	}
	curl_close($ch);
	return $responseData;
}

$responseData = json_decode(request(), true);
?>
<?php if($responseData):?>
<?php $id = $responseData['id']; ?>
<script src="https://eu-prod.oppwa.com/v1/paymentWidgets.js?checkoutId=<?php echo $id;?>"></script>
<?php endif;?>
<?php if($_POST['card'] == "mada"):?>
<form action="https://consl2.com/bookings/transaction/finish/state/complete/card/mada/order_id/<?php echo $_POST['id'];?>" class="paymentWidgets" data-brands="MADA"></form>
<?php else:?>
<form action="https://consl2.com/bookings/transaction/finish/state/complete/card/card/order_id/<?php echo $_POST['id'];?>" class="paymentWidgets" data-brands="VISA MASTER"></form>
<?php endif;?>

</body>
</html>