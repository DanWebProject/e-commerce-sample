<?php
session_start();
include "db.php"; //connecting database

//define a session start
define("PRODUCTCODE", 0);
//-product code is 0 in the array
define("PRODUCTNAME", 1);
//-product name is 1 in the array
define("QUANTITY", 2);
//-product quantity is 2 in the array
define("PRICE", 3);
//-product code is 3 in the array
define("quantity", 4);
//-product code is 4 in the array
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header("Location: index.php");
    exit; //redirect to main page if user not registered
}
$user = $_SESSION['user']; //if user signin - get users data
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['productcode'])) {
        $error = AddToCart();
        if(strlen($error) > 0) {
            $_SESSION['error'] = $error;
            header("Location: "."products.php");
            exit;
        }
    } else { $action = isset($_POST['action']) ? $_POST['action'] : '';
        //getting a value for the multi dimensional array
        $value = strtoupper(substr($action, 0, 5));
        switch ($value)
        {
            // continue shopping button
            case "CONTI": header("Location: "."products.php");
                break;
            // empty cart button
            case "EMPTY":
                EmptyCart();
                break;
            // user goes to via clicking checkout button
            case "CHECK":
                header("Location: "."customer.php");
                break;
        }
    }
}

function AddToCart() //add product or products to the cart
{
if(intval($_POST['quantity']) < 1) {
    	return "Don’t add 0 amount";
    }
	 if(!isset($_POST['productcode'])) {
 			return "missing data";
   }
     $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : NULL;
    //after session created
    $itmcount = isset($_SESSION['itmcount']) ? $_SESSION['itmcount'] : 0;
	 //check for an existing match
	 $found = FALSE;
   if($cart){
     $idx = 0;
     foreach($cart[PRODUCTCODE] as $idx => $product){
       if($product == $_POST['productcode']){
         $found = TRUE;
         break;
          }
		}
	 }
	 //if we found a match
	  if($found){
	 $cart[QUANTITY][$idx] += intval($_POST['quantity']);   
     $cart[PRICE][$idx] += $_POST['price'];
	 }
	 	 //otherwise add new item
		else 
	{
        $cart[PRODUCTCODE][$itmcount] = $_POST['productcode'];
        $cart[PRODUCTNAME][$itmcount] = $_POST['productname'];
        $cart[QUANTITY][$itmcount] = intval($_POST['quantity']);
        $cart[PRICE][$itmcount] =       $_POST['price'];
            
        $itmcount = $itmcount + 1;
	  	  //$itmcount = $itemcount++;
    }
  // if(strlen($error) == 0) { //if no errors
        $_SESSION['cart'] = $cart;
        $_SESSION['itmcount'] = $itmcount;
      // return $error;
    return "";	// no errors
   
}
function EmptyCart()
{
    $_SESSION['itmcount'] = 0;
    $_SESSION['cart'] = array();
}
?>

<html>
<head>
<title>Shopping Cart</title>
 
 <link rel="stylesheet" type="text/css" href="index.css">
  
 
 
</head>
<body>
<div id="container">
  <div id="wb_Text1" style="margin:0;padding:0;position:absolute;left:286px;top:514px;width:262px;height:36px;text-align:left;z-index:8;border:0px #C0C0C0 solid;overflow-y:hidden;background-color:transparent;">
    <div style="font-family:'Segoe UI';font-size:12px;color:#000000;">
      <div style="text-align:left"><span style="font-family:'Times New Roman';font-size:32px;color:#497DB2;">Your Shopping Cart</span></div>
    </div>
  </div>
  <div id="wb_Shape1" style="margin:0;padding:0;position:absolute;left:170px;top:133px;width:1px;height:403px;text-align:center;z-index:10;"> <img src="images/cart_0001.png" id="Shape1" align="top" alt="" title="" border="0" width="1" height="403"></div>
  <div id="NavigationBar1" style="position:absolute;left:-31px;top:361px;width:118px;height:99px;z-index:11;">
    <ul class="navbar">
      <li><a href="./index.php"  class="black"> Home</a></li>
      <li><a href="./products.php"class="black"> Products</a></li>
      <li><a href="./cart.php"class="black"> Cart</a></li>
      <?php
      
      if (!empty($user) && $user['type'] == "admin") {
          echo '
      <li><a href="orders.php"class="black"> Orders</a></li>
          ';
      }
      
      if (!empty($user)) {
          echo '
      <li><a href="signin.php?logout"class="black"> Logout</a></li>
          ';
      }
      
      ?>
    </ul>
  </div>
  <div id="wb_Form1" style="position:absolute;left:192px;top:194px;width:584px;height:423px;z-index:12;">
    <form name="frmCart" method="post" action="./cart.php" enctype=" " id="Form1">
        <input type="submit" id="Button1" name="action" value="Continue Shopping" style="position:absolute;left:11px;top:385px;width:138px;height:26px;z-index:0;">
      <input type="submit" id="Button2" name="action" value="Empty cart" style="position:absolute;left:154px;top:386px;width:139px;height:25px;z-index:1;">
      <input type="submit" id="Button3" name="action" value="Check out" style="position:absolute;left:303px;top:387px;width:124px;height:25px;z-index:2;">
    </form>
  </div>
</div>
<div 			 style="text-align:left">   
		   
			  <div align="center">
		     <?php
        $itmcount = $_SESSION['itmcount'];
				$cart = $_SESSION['cart'];
        for ($i=0; $i<$itmcount; $i++){
          $film_in_stock = checkStock($cart[PRODUCTCODE][$i]); //get now the stock of the film
        	if($cart[QUANTITY][$i] > $film_in_stock ) { 
        ?>
            <span  style="font-family:'Times New Roman';font-size:16px;">Out <span class="style5">of</span> stock</span>
        <?php 
        	}//IF 
        }//For 
?>
		      
            </div>
</div>
<div align="center">
 
 <?php 
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : '';
$itmcount = isset($_SESSION['itmcount']) ? $_SESSION['itmcount'] : 0;
$HTML = ""; 
if ($itmcount == 0)
{ $HTML = "<h3>Your Shopping Cart Is Empty Add Some Products! </h3>"; 
} 
else 
{ 
$HTML = "<div style=\"overflow:auto; height=358px;\">"."\n"; 
$HTML .= "<table border=\"0\" cellpadding=\"3\" cellspacing=\"2\" width=\"100%\">"."\n"; 
$HTML .= "<tr>"."\n"; 
$HTML .= "<td>Product code			</td>"."\n"; 
$HTML .= "<td>Product name			</td>"."\n"; 
$HTML .= "<td>Quantity				</td>"."\n"; 
$HTML .= "<td>Price					</td>"."\n"; 
$HTML .= "<td>Total					</td>
		  </tr>"."\n";
 $total = 0; 
 //the total loop
 for ($i=0; $i<$itmcount; $i++)
 //displaying cart details product,code,name, quantity making it read-only 
 { 
 	$HTML .= "<tr>"."\n"; 
 	$HTML .= "<td>".$cart[PRODUCTCODE][$i]."									
			  </td>"."\n"; 
 	$HTML .= "<td>".$cart[PRODUCTNAME][$i]."									
			  </td>"."\n"; 
 	$HTML .= "<td>
	<input 
 value=\"".$cart[QUANTITY][$i]."\" size=\"3\" readonly>							
			  </td>"."\n"; 
// //number format for price
 	$HTML .= "<td>"."&pound;".number_format($cart[PRICE][$i],2)."						
			  </td>"."\n"; 
		//number format for product total
	$HTML .= "<td>"."&pound;".number_format($cart[PRICE][$i]*$cart[QUANTITY][$i],2)."	
			  </td>"."\n"; 
	$HTML .= "</tr>"."\n"; $total = $total + ($cart[PRICE][$i]*$cart[QUANTITY][$i]); } 
	$HTML .= "<tr>"."\n";
	 $HTML .="<td></td><td></td><td>											
			  </td>"."\n";
	 $HTML .="<td>Total</td>"."\n"; 
	 //number format for cart total
	 $HTML .="<td>"."&pound;".number_format($total, 2)."								
			  </td>"."\n"; 
	 $HTML .="</tr>"."\n"; 
	 $HTML .="</table>"."\n"; 
	 $HTML .="</div>"."\n"; } 
	 echo $HTML;
	  ?>
</span>
</body>
</html>
</div>
</body>
</html>