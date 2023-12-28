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
.form_container{
  padding:20px;
}
.form_container > h1{
  text-align:center;
  font-size:1.5rem;
  margin-bottom:15px;
}
.form_container form{
  max-width: 600px;
  margin: 0 auto;
  background-color: #fff;
  border-radius: 10px;
  padding: 15px 20px;
  box-shadow: 0 10px 10px rgb(0 0 0 / 20%);
}
</style>
</head>
<body>
<?php
$price = $_GET['price'];
$id = $_GET['id'];
$card = $_GET['card'] ?  $_GET['card'] : 'card';
?>
<div class="form_container">
  <h1>Please fill the Billing Information</h1>
  <form action="/next-step.php" name="myForm" method="post">
    <div class="row">
      <div class="col-md-6 mb-3 col-sm-6">
        <label for="givenName" class="form-label">First name:</label><br>
        <input type="text" class="form-control" id="givenName" name="givenName" required>
      </div>

      <div class="col-md-6 mb-3 col-sm-6">
        <label for="surname" class="form-label">Last name:</label>
        <input type="text" class="form-control" id="surname" name="surname" required >
      </div>
      <div class="col-md-12 mb-3">
        <label for="mobile" class="form-label">Mobile:</label>
        <input type="text" class="form-control" id="mobile" name="mobile" required placeholder="+966" value="+966">
      </div>
      <div class="col-md-12 mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="text" class="form-control" id="email" name="email" required>
      </div>
      <div class="col-md-12 mb-3">
        <label for="street1" class="form-label">Street:</label>
        <input type="text" class="form-control" id="street1" name="street1" required>
      </div>
      <div class="col-md-6 mb-3 col-sm-6">
        <label for="city" class="form-label">City:</label><br>
        <input type="text" class="form-control" placeholder="Riyadh" id="city" name="city" required>
      </div>
      <div class="col-md-6 mb-3 col-sm-6" class="form-label">
        <label for="state">State:</label>
        <input type="text" class="form-control" placeholder="Riyadh" id="state" name="state" required>
      </div>
      <div class="col-md-6 mb-3 col-sm-6" class="form-label">
        <label for="country">Country:</label>
         <input type="text" class="form-control" id="country" name="country" required disabled="disabled"  VALUE="SA">
      </div>
      <div class="col-md-6 mb-3 col-sm-6" class="form-label">
        <label for="zipcode">Zipcode:</label>
        <input type="text" class="form-control" id="zipcode" name="zipcode" required>
      </div>
      <div class="col-md-12">
        <input type="hidden" value="<?php echo $price;?>" name="price">
        <input type="hidden" value="<?php echo $card;?>" name="card">
        <input type="hidden" value="<?php echo $id;?>" name="id">
        <input type="submit" value="Continue" class="btn btn-primary">
      </div>
    </div>
  </form>
</div>
</body>
</html>