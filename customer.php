<?php
 session_start(); 
 
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header("Location: index.php");
    exit; //redirect to main page if user not registered
}
$user = $_SESSION['user']; //if user sign-in - get users data
    
$error = array();
include 'db.php';
$itmcount = isset($_SESSION['itmcount']) ? $_SESSION['itmcount'] : 0;
if ($itmcount == 0)
{ 
	$errors['main'] = 'Please add items to your shopping cart before checking out.';
}
else
{
	 
	 if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
         
            if(empty($_POST['firstname']))
                $error['firstname'] = "Enter a firstname!";
            else
                $_SESSION['firstname'] = $_POST['firstname']; 
            if(empty($_POST['lastname']))
                $error['lastname'] = "Enter a lastname!";
            else
                $_SESSION['lastname'] = $_POST['lastname']; 
            if(empty($_POST['email']))
                $error['email'] = "Enter a correct email!";
            else
                $_SESSION['email'] = $_POST['email']; 
            if(empty($_POST['address']))
                $error['address'] = "Enter an address!";
            else
                $_SESSION['address'] = $_POST['address']; 
            
            $_SESSION['address2'] = $_POST['address2']; 
            
            if(empty($_POST['city']))
                $error['city'] = "Type in a city!";
            else
                $_SESSION['city'] = $_POST['city']; 
            if(empty($_POST['zip']))
                $error['zip'] = "Enter a correct zip!";
            else
                $_SESSION['zip'] = $_POST['zip']; 
            if(empty($_POST['state']))
                $error['state'] = "Enter a state or county!";
            else
                $_SESSION['state'] = $_POST['state']; 
            if(empty($_POST['postcode']))
                $error['postcode'] = "Enter a correct postcode!";
            else
                $_SESSION['country'] = $_POST['postcode'];
            if(empty($_POST['phone']))
                $error['phone'] = "Enter a phone number!";
            else
                $_SESSION['phone'] = $_POST['phone'];
            if(empty($_POST['username']))
                $error['username'] = "Enter a username!";
			else
                $_SESSION['username'] = $_POST['username'];
            if(empty($_POST['password']))
                $error['password'] = "Enter a password!";
        
        
            //validate email
            if (!ValidateEmail($_POST['email'])) 
            { 
                $error['email'] = "Incorrect email";
            }
            
            validateAddress($_POST['address'],$error);
            validateZip($_POST['zip'],$_POST['postcode'],$error);
            validateCity($_POST['city'],$error);
            validatePhone($_POST['phone'],$error);
            validateUsername($_POST['username'],$error);
            validatePassword($_POST['password'],$error);
              
                
            if (empty($error)) {
                updateDetails($connect, $_SESSION['user']['id']);
              
			    $_SESSION['email'] = $_POST ['email'];
                if($_SESSION['country'] == "usa" || $_SESSION['country'] == "eu" || $_SESSION['country'] == "uk") {
                    header("Location: "."additionalpost.php");
                } else {
                    header("Location: "."checkout.php");
                }
            } 
    } 
}
$sql = "SELECT * FROM customerdetails WHERE user_id = '".$_SESSION['user']['id']."' LIMIT 1;";
$result = mysql_query($sql);
if($result) {
    $row = mysql_fetch_assoc($result);
}
$firstname =    	( isset($row)&& !empty($row['firstname']))  ? $row['firstname'] : (isset($_SESSION['firstname']) ? $_SESSION['firstname'] : '');
$lastname =       	(isset($row) && !empty($row['surname']))    ? $row['surname']   : (isset($_SESSION['lastname'])  ? $_SESSION['lastname'] : '');
$email = 		(isset($row) && !empty($row['email']))      ? $row['email']     : (isset($_SESSION['email'])     ? $_SESSION['email'] : '');
$address = 		(isset($row) && !empty($row['address']))    ? $row['address']   : (isset($_SESSION['address'])   ? $_SESSION['address'] : '');
$address2 =	        (isset($row) && !empty($row['address2']))   ? $row['address2']  : (isset($_SESSION['address2'])  ? $_SESSION['address2'] : '');
$city = 		(isset($row) && !empty($row['city']))       ? $row['city']      : (isset($_SESSION['city'])      ? $_SESSION['city'] : '');
$zip = 			(isset($row) && !empty($row['postcode']))   ? $row['postcode']  : (isset($_SESSION['zip'])       ? $_SESSION['zip'] : '');
$state = 		(isset($row) && !empty($row['state']))      ? $row['state']     : (isset($_SESSION['state'])     ? $_SESSION['state'] : '');
$country = 		(isset($row) && !empty($row['country']))    ? $row['country']   : (isset($_SESSION['country'])   ? $_SESSION['country'] : '');
$phone = 		(isset($row) && !empty($row['telephone']))  ? $row['telephone'] : (isset($_SESSION['phone'])     ? $_SESSION['phone'] : '');
function ValidateEmail($email) 
{ 
	$pattern = '/^([0-9a-z]([-.\w]*[0-9a-z])*@(([0-9a-z])+([-\w]*[0-9a-z])*\.)+[a-z]{2,6})$/i';
    return preg_match($pattern,$email);
}
function validateAddress($address, &$error)
{
    $stripped = preg_replace("/[\(\)\.\-\ ]/", "", $address);
    if(!preg_match("/^[A-Za-z0-9_]{1,}$/", $stripped))
        $error['address'] = "Incorrect address, allowed only letters, numbers, and underscores";
}
function validateUsername($username, &$error)
{
    $stripped = preg_replace("/[\(\)\.\-\ ]/", "", $username);
    if(!preg_match("/^\w/", $stripped))
        $error['username'] = "Incorrect username, allowed only letters, numbers, and underscores";
}
function validatePassword($password, &$error)
{
    $stripped = preg_replace("/[\(\)\.\-\ ]/", "", $password);
    if(!preg_match("/^(.*)/", $stripped))
        $error['password'] = "Incorrect password, must contains one uppercase letter and at least one number.";
}
    
function validateZip($postcode, $country, &$error)
{
        if (empty($postcode))
        {
            $error['postcode'] = "You didn't enter a postcode.";
        }
        else
        {
            switch($country)
            {
                case 'usa':
                    $stripped = preg_replace("/[\(\)\.\-\ ]/", "", $postcode);
                    if(!preg_match("/^[\d]{5}$/", $stripped))
                        $error['zip'] = "The zipcode has incorrect format please enter correct eg 94105 .";
                    break;
                case 'uk':
                    if(!preg_match("/^[A-Z]?[A-Z][0-9][A-Z0-9]?\s[0-9][A-Z]{2}$/", $postcode))
                        $error['zip'] = "The postcode has incorrect format please enter correct eg SE5 0EG.";
                    break;
                case 'eu':
                    $stripped = preg_replace("/[\(\)\.\-\ ]/", "", $postcode);
                    if(!preg_match("/^[\d]{4}$/", $stripped))
                        $error['zip'] = "The postcode has incorrect form please enter in the format.";
                    break;
            }
        }
}
    
function validateCity($city, &$error)
{
    $stripped = preg_replace("/[\(\)\.\-\ ]/", "", $city);
    if(!preg_match("/^[a-zA-Z]{1,}$/", $stripped))
        $error['city'] = "The city has incorrect format.";
}
    
function validatePhone($city, &$error)
{
    $stripped = preg_replace("/[\(\)\.\-\ ]/", "", $city);
    if(!preg_match("/^[\d]{9,11}$/", $stripped))
        $error['phone'] = "The phone is incorrect enter correct eg. 01234569512,  only digits allowed.";
}
function updateDetails($connect, $userID)
{
    //insert or update depend on available information in database
    $sql = "SELECT COUNT(*) FROM customerdetails WHERE user_id = '".$_SESSION['user']['id']."' LIMIT 1;";
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    if($row[0] > 0) {
        $query = "UPDATE `customerdetails` SET"
            ."`firstname`  = '"   . $_SESSION['firstname'] . "',"
            ." `surname`   = '"   . $_SESSION['lastname'] . "',"
            ." `email`     = '"   . $_SESSION['email'] . "',"
            ." `address`   = '"   . $_SESSION['address'] . "',"
            ." `address2`  = '"   . $_SESSION['address2'] . "',"
            ." `city`      = '"   . $_SESSION['city'] . "',"
            ." `postcode`  = '"   . $_SESSION['zip'] . "',"
            ." `state`     = '"   . $_SESSION['state'] . "',"
            ." `country`   = '"   . $_SESSION['country'] . "',"
            ." `telephone` = '"   . $_SESSION['phone'] . "' ";
        $query .=
            " WHERE `user_id` = '$userID' LIMIT 1;";
    } else {
        $query = "INSERT INTO `customerdetails`(`user_id`, `firstname`, `surname`, `email`, `address`, `address2`, `city`, `postcode`, `state`, `country`, `telephone`)
            VALUES (";
        $query .= "'" . $userID . "',";
        $query .= "'" . $_SESSION['firstname'] . "',";
        $query .= "'" . $_SESSION['lastname'] . "',";
        $query .= "'" . $_SESSION['email'] . "',";
        $query .= "'" . $_SESSION['address'] . "', '" . $_SESSION['address2'] . "', ";
        $query .= "'" . $_SESSION['city'] . "', ";
        $query .= "'" . $_SESSION['zip'] . "', ";
        $query .= "'" . $_SESSION['state'] . "',";
        $query .= "'" . $_SESSION['country'] . "', ";
        $query .= "'" . $_SESSION['phone'] . "'";
        $query .=
            ");";
    }
    if (mysql_query($query))
        return mysql_insert_id();
    else
        die(mysql_error());
}
	
?>
	
	



<script type="text/javascript" src="./js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
   $("#NavigationBar1 .navbar a").hover(function()
   {
      $(this).children("span").hide();
   }, function()
   {
      $(this).children("span").show();
   })
});
</script><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Customer Information</title>
   <link rel="stylesheet" type="text/css" href="index.css">
  
		
</head>
<body>
<div id="Text2" style="position:absolute;left:13px;top:229px;width:171px;height:99px;z-index:11;">
  <div align="left"><a href="./index.php"  class="black">Home</a>
    <div align="left"><a href="./products.php"class="black">Products</a>
      <div align="left"><a href="./cart.php"class="black">Cart</a></div>
      <?php
      $user = $_SESSION['user']; //if user sign-in - get users data
      if (!empty($user) && $user['type'] == "admin") {
          echo '
      <div align="left"><a href="orders.php"class="black"> Orders</a></div>
          ';
      }
      
      if (!empty($user)) {
          echo '
      <div align="left"><a href="signin.php?logout"class="black"> Logout</a></div>
          ';
      }
      
      ?>
    </div>
  </div>
</div>
<div id="container">
    <div style="color:red;"><?php if(isset($errors['main'])) echo $errors['main'];  ?></div>
<div id="Image1" style="margin:0; padding:0; position:absolute; left:3px; top:497px; width:780px; height:79px; text-align:left; z-index:23; background-image: url(footer.gif); layer-background-image: url(footer.gif); border: 1px none #000000;">
<img src="body.gif" alt="" name="Image1" width="780" height="4" border="0" id="Image1" style="width:780px;height:92px;"></div>
<div id="Image2" style="margin:0; padding:0; position:absolute; left:3px; top:36px; width:792px; height:55px; text-align:left; z-index:24;"><img src="header.gif" alt="" name="Image2" width="780" height="87" border="0" id="Image2" style="width:780px;height:87px;"></div>
<div id="Image3" style="margin:0;padding:0;position:absolute;left:3px;top:119px;width:774px;height:429px;text-align:left;z-index:25;"><img src="body.gif" alt="" width="780" height="4" border="0" style="width:780px;height:379px;"></div>
<div id="Text1" style="margin:0;padding:0;position:absolute;left:31px;top:49px;width:485px;height:36px;text-align:left;z-index:28;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
<div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
<div style="text-align:left"><span style="font-family:'Times New Roman';font-size:32px;color:#497DB2;">Customer Information</span></div>
</div>
</div>
<div id="Shape2" style="margin:0;padding:0;position:absolute;left:178px;top:133px;width:1px;height:403px;text-align:center;z-index:29;">
<img src="images/customer_0005.png" id="Shape2" align="top" alt="" title="" border="0" width="1" height="403"></div>
</div>
<div id="Form1" style="position:absolute;left:224px;top:116px;width:799px;height:452px;z-index:26;">
  <form name="customer" method="post" action="customer.php" enctype="0" id="Form1">
    <div id="Text3" style="margin:0;padding:0;position:absolute;left:113px;top:17px;width:438px;height:21px;text-align:left;z-index:0;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
      <div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
        <div style="text-align:left"><span style="font-family:'Times New Roman';font-size:19px;color:#497DB2;">Enter your details to sign up:</span></div>
      </div>
    </div>
    <div id="Text5" style="margin:0;padding:0;position:absolute;left:7px;top:49px;width:103px;height:19px;text-align:left;z-index:1;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
      <div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
        <div style="text-align:left"><span style="font-family:'Times New Roman';font-size:15px;">First name:</span></div>
      </div>
    </div>
    <div id="Text4" style="margin:0;padding:0;position:absolute;left:7px;top:79px;width:103px;height:19px;text-align:left;z-index:2;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
      <div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
        <div style="text-align:left"><span style="font-family:'Times New Roman';font-size:15px;">Last name:</span></div>
      </div>
    </div>
    <div id="Text7" style="margin:0;padding:0;position:absolute;left:7px;top:109px;width:103px;height:19px;text-align:left;z-index:3;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
      <div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
        <div style="text-align:left"><span style="font-family:'Times New Roman';font-size:15px;">E-mail:</span></div>
      </div>
    </div>
    <div id="Text6" style="margin:0;padding:0;position:absolute;left:7px;top:139px;width:103px;height:19px;text-align:left;z-index:4;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
      <div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
        <div style="text-align:left"><span style="font-family:'Times New Roman';font-size:15px;">Address:</span></div>
      </div>
    </div>
    <div id="Text9" style="margin:0;padding:0;position:absolute;left:6px;top:200px;width:103px;height:19px;text-align:left;z-index:5;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
      <div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
        <div style="text-align:left"><span style="font-family:'Times New Roman';font-size:15px;">City:</span></div>
      </div>
    </div>
    <div id="Text8" style="margin:0;padding:0;position:absolute;left:7px;top:229px;width:103px;height:19px;text-align:left;z-index:6;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
      <div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
        <div style="text-align:left"><span style="font-family:'Times New Roman';font-size:15px;">Post code:</span></div>
      </div>
    </div>
    <div id="Text11" style="margin:0;padding:0;position:absolute;left:7px;top:261px;width:103px;height:19px;text-align:left;z-index:7;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
      <div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
        <div style="text-align:left"><span style="font-family:'Times New Roman';font-size:15px;">State:</span></div>
      </div>
    </div>
    <div id="Text10" style="margin:0;padding:0;position:absolute;left:7px;top:289px;width:103px;height:19px;text-align:left;z-index:8;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
      <div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
        <div style="text-align:left"><span style="font-family:'Times New Roman';font-size:15px;">Country:</span></div>
      </div>
    </div>
    <div id="NavigationBar1" style="margin:0;padding:0;position:absolute;left:7px;top:319px;width:103px;height:19px;text-align:left;z-index:9;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
      <div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
        <div style="text-align:left"><span style="font-family:'Times New Roman';font-size:15px;">Telephone:</span></div>
      </div>
    </div>
    
    <div id="NavigationBar2" style="margin:0;padding:0;position:absolute;left:8px;top:349px;width:113px;height:19px;text-align:left;z-index:9;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
      <div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
        <div style="text-align:left"><span style="font-family:'Times New Roman';font-size:15px;">Verify Username:</span></div>
      </div>
    </div>
    <div id="NavigationBar2" style="margin:0;padding:0;position:absolute;left:7px;top:379px;width:114px;height:19px;text-align:left;z-index:9;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
      <div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
        <div style="text-align:left"><span style="font-family:'Times New Roman';font-size:15px;">Verify Password:</span></div>
      </div>
    </div>
    
    <input type="submit" id="Button1" name="Input" value="Continue" style="position:absolute;left:469px;top:407px;width:97px;height:26px;z-index:11;">
    <input type="text" 	 id="Editbox1" style="position:absolute;left:128px;top:47px;width:331px;height:20px;line-height:20px;z-index:12;<?php if(isset($error['firstname'])) echo "background-color:red;"; ?>" name="firstname" value="<?php if(isset($firstname)) echo $firstname; ?>">
    <div class="errorfield" style="position:absolute;left:471px;top:48px;width:133px;height:20px;line-height:20px;z-index:12;color:red;font-size:10px;" ><?php if(isset($error['firstname'])) echo $error['firstname']; ?></div>
    
    <input type="text" 	 id="Editbox2" style="position:absolute;left:128px;top:78px;width:332px;height:20px;line-height:20px;z-index:13;<?php if(isset($error['lastname'])) echo "background-color:red;"; ?>" name="lastname" value="<?php if(isset($lastname)) echo $lastname; ?>">
    <div class="errorfield" style="position:absolute;left:470px;top:79px;width:134px;height:20px;line-height:20px;z-index:12;color:red;font-size:10px;" ><?php if(isset($error['lastname'])) echo $error['lastname']; ?></div>
    
    <input type="text"	 id="Editbox3" style="position:absolute;left:127px;top:109px;width:334px;height:20px;line-height:20px;z-index:14;<?php if(isset($error['email'])) echo "background-color:red;"; ?>" name="email" value="<?php if(isset($email)) echo $email; ?>">
    <div class="errorfield" style="position:absolute;left:471px;top:113px;width:123px;height:20px;line-height:20px;z-index:12;color:red;font-size:10px;" ><?php if(isset($error['email'])) echo $error['email']; ?></div>
    
    <input type="text" 	 id="Editbox4" style="position:absolute;left:129px;top:139px;width:330px;height:20px;line-height:20px;z-index:15;<?php if(isset($error['address'])) echo "background-color:red;"; ?>" name="address" value="<?php if(isset($address)) echo $address; ?>">
    <div class="errorfield" style="position:absolute;left:473px;top:142px;width:211px;height:20px;line-height:20px;z-index:12;color:red;font-size:10px;" ><?php if(isset($error['address'])) echo $error['address']; ?></div>
    
    <input type="text" 	 id="Editbox5" style="position:absolute;left:127px;top:169px;width:333px;height:20px;line-height:20px;z-index:16;<?php if(isset($error['address2'])) echo "background-color:red;"; ?>" name="address2" value="<?php if(isset($address2)) echo $address2; ?>">
    <div class="errorfield" style="position:absolute;left:473px;top:172px;width:243px;height:20px;line-height:20px;z-index:12;color:red;font-size:10px;" ><?php if(isset($error['address2'])) echo $error['address2']; ?></div>
    
    <input type="text" 	 id="Editbox6" style="position:absolute;left:128px;top:199px;width:331px;height:20px;line-height:20px;z-index:17;<?php if(isset($error['city'])) echo "background-color:red;"; ?>" name="city" value="<?php if(isset($city)) echo $city; ?>">
    <div class="errorfield" style="position:absolute;left:474px;top:204px;width:172px;height:19px;line-height:20px;z-index:12;color:red;font-size:10px;" ><?php if(isset($error['city'])) echo $error['city']; ?></div>
    
    <input type="text" 	 id="Editbox8" style="position:absolute;left:130px;top:259px;width:331px;height:20px;line-height:20px;z-index:18;<?php if(isset($error['state'])) echo "background-color:red;"; ?>" name="state" value="<?php if(isset($state)) echo $state; ?>">
    <div class="errorfield" style="position:absolute;left:474px;top:279px;width:149px;height:20px;line-height:20px;z-index:12;color:red;font-size:10px;" ><?php if(isset($error['state'])) echo $error['state']; ?></div>
    
    <input type="text" 	 id="Editbox7" style="position:absolute;left:129px;top:229px;width:331px;height:20px;line-height:20px;z-index:19;<?php if(isset($error['zip'])) echo "background-color:red;"; ?>" name="zip" value="<?php if(isset($zip)) echo $zip; ?>">
    <div class="errorfield" style="position:absolute;left:474px;top:230px;width:133px;height:20px;line-height:20px;z-index:12;color:red; font-size:10px;" ><?php if(isset($error['zip'])) echo $error['zip']; ?></div>
    
    <select name="postcode" style="position:absolute;left:127px;top:289px;width:334px;height:20px;line-height:20px;z-index:20;<?php if(isset($error['country'])) echo "background-color:red;"; ?>">
        <option value="usa" <?php if($country == 'usa') echo "selected"; ?>>USA</option>
        <option value="uk" <?php if($country == 'uk') echo "selected"; ?>>UK</option>
        <option value="eu" <?php if($country == 'eu') echo "selected"; ?>>Europe</option>
    </select>

    <input type="text" 	 id="Editbox10" style="position:absolute;left:130px;top:319px;width:330px;height:20px;line-height:20px;z-index:21;<?php if(isset($error['phone'])) echo "background-color:red;"; ?>" name="phone" value="<?php if(isset($phone)) echo $phone; ?>">
    <div class="errorfield" style="position:absolute;left:473px;top:300px;width:215px;height:20px;line-height:20px;z-index:12;color:red;font-size:10px;" ><?php if(isset($error['phone'])) echo $error['phone']; ?></div>
    
    <input type="text" 	 id="Editbox11" style="position:absolute;left:129px;top:351px;width:328px;height:20px;line-height:20px;z-index:21;" name="username" value="<?php echo $_SESSION['user']['username']; ?>">
    <div class="errorfield" style="position:absolute;left:476px;top:343px;width:207px;height:20px;line-height:20px;z-index:12;color:red; font-size:10px;" ><?php if(isset($error['username'])) echo $error['username']; ?></div>
    
    <input type="password" id="password" style="position:absolute;left:128px;top:379px;width:332px;height:20px;line-height:20px;z-index:21;" name="password" value="<?php echo $_SESSION['user']['password']; ?>">
    <div class="errorfield" style="position:absolute;left:475px;top:384px;width:128px;height:20px;line-height:20px;z-index:12;color:red; font-size:10px;" ><?php if(isset($error['password'])) echo $error['password']; ?></div>
    
  </form>
</div>
</body>
</html>
