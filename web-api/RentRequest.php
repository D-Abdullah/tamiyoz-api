<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Document</title>
<style>

</style>


    <?php
//session_start();
//$name =$_SESSION['name'];

    ?>

</head>
<body>
<img style="position: relative" src="../uploads/rentReguest.png" >

    <div style="font-size: 26px; width: 380px; position: absolute; left:150px; top: 200px;"  ><?php echo $dateTime; ?>  </div>


<div style="margin: 0px"></div>
<br>

<div style="font-size: 26px; width: 100px; position: absolute; right:320px; top: 320px;">
    <?php echo $statonID; ?>
    
</div>

<br>

<div style="font-size: 26px; width: 100px; position: absolute; right:100; top: 320px;">
    <?php echo $numofRent; ?>
</div>

<div style="font-size: 26px; width: 380px; position: absolute; right:140px; top: 400px;">
    <?php echo $data['name']; ?>
</div>

<div style="font-size: 26px; width: 380px; position: absolute; right:140px; top: 450px;">
    <?php echo $data['commercial_registration_no']; ?>
</div>


<div style="font-size: 26px; width: 380px; position: absolute; right:140px; top: 500px;">
    <?php echo $data['activity_type']?$data['activity_type']:'غير محدد '; ?>
</div>


<div style="font-size: 26px; width: 380px; position: absolute; right:130px; top: 540px;">
      <span><?php echo $data['rante_time']; ?> </span>
</div>


<div  style="font-size: 26px; width: 380px; position: absolute; right:140px; top: 590px;">
     <span><?php echo $data['phone']; ?> </span>
</div>


<div  style="font-size: 26px; width: 380px; position: absolute;  right:300px; top: 630px;">
     <span><?php echo $data['email']; ?> </span>
</div>


<div style="font-size: 26px; width: 380px; position: absolute; right:120px; top: 700px;">
    <?php echo $data['notes']?$data['notes']:'لايوجد'; ?>
</div>









</body>
</html>