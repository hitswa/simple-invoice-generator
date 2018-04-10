<?php

if ( ! session_id() ) @ session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {

	foreach ($_REQUEST as $key => $value) {
		$_SESSION[$key] = $value;
	}

	echo 'success';
	exit();
}

$billingAmount = 0;

if(isset($_SESSION['arrArticles'][0]['lineTotal'])) {

	for($i=0; $i<count($_SESSION['arrArticles']); $i++) {
		$billingAmount = $billingAmount + $_SESSION['arrArticles'][$i]['lineTotal'];
	}
}

function numberToCurrency($number) {
    if(setlocale(LC_MONETARY, 'en_IN'))
      return money_format('%.0n', $number);
    else {
      $explrestunits = "" ;
      $number = explode('.', $number);
      $num = $number[0];
      if(strlen($num)>3){
          $lastthree = substr($num, strlen($num)-3, strlen($num));
          $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
          $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
          $expunit = str_split($restunits, 2);
          for($i=0; $i<sizeof($expunit); $i++){
              // creates each of the 2's group and adds a comma to the end
              if($i==0)
              {
                  $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
              }else{
                  $explrestunits .= $expunit[$i].",";
              }
          }
          $thecash = $explrestunits.$lastthree;
      } else {
          $thecash = $num;
      }
      if(!empty($number[1])) {
      	if(strlen($number[1]) == 1) {
      		return $thecash . '.' . $number[1] . '0';
      	} else {
      		return $thecash . '.' . $number[1];
      	}
      	// return $thecash . '.' . sprintf('%02d', $number[1]);
      } else {
      	return $thecash.'.00';
      }
      
      // return '₹ ' . $thecash;
    }
}

function dateFormatter($date) {
	$date = explode('-', $date);
	$year = $date[0];
	$month = $date[1];
	$day = $date[2];

	$monthArray = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

	return $monthArray[$month-1] . ' ' . $day . ', ' . $year;
}

?><!DOCTYPE html>
<html style="height:100%;">
<head>
	<title>Invoice</title>
	<style type="text/css">
		body {
		  -webkit-print-color-adjust: exact;
		}
		tr.border_bottom td {
		  border-bottom:1pt solid #ccc;
		}
		.allFullTr tr{
			width:100%;
		}
		.allHalfTd tr td {
			width: 50%
		}
		.borderBottom td {
			border-bottom: 1px solid #ccc;
		}
		table {
			width:100%;
		}
		.fullWidth {
			width:100%;
		}
		.halfWidth{
			width:100%;
		}
		.align-right {
			text-align: right;
		}
		.pull-right {
			float: right;
		}
		.grayscale {
			background-color: #ccc;
		}
		.tableRightValue {
			text-align: right;
			padding: 10px;
		}
		.tableLeftValue {
			text-align: left;
			padding: 10px;
		}
		.tableHeaderRightValue {
			background-color:#ccc;
			margin: 0;
			padding: 10px;
			text-align: right;
		}
		.tableHeaderLeftValue {
			background-color:#ccc;
			margin: 0;
			padding: 10px;
			text-align: left;
		}
		.finalLine {
			border: 3px solid #ccc;
			background-color: #ccc
		}
		.cut {
			border-top:2px dashed black;
		}
		.tenPadding {
			padding: 10px;
		}
		.alignTop {
			vertical-align:top;
		}
		.alignBottom {
			vertical-align:bottom;
		}
		.zeroMargin {
			margin: 0;
		}
		img {
			width:auto;
			height: 150px;
			float: right;
		}
		.fullWidth {
			width: 80%;
			margin-left: auto;
			margin-right: auto;
		}
		.button {
			display: block;
		    width: 115px;
		    height: 20px;
		    background: #4E9CAF;
		    padding: 10px;
		    text-align: center;
		    border-radius: 5px;
		    color: white;
		    font-weight: bold;
		    text-decoration: none;
		}
		@media print {
			body {
			  -webkit-print-color-adjust: exact;
			}
		 	.grayscale{
		 		background-color: #ccc;
		 	}
		 	.printOnBottom {
		 		position:absolute;
		 		bottom:0;
		 		width:100%;
		 		height:240px
		 	}
		 	.fullWidth {
		 		width: 100%
		 	}
		 	.hide {
		 		display: none;
		 	}
		}
	</style>
</head>
<body class="fullWidth">

<span class="hide"><br><a class="button" href='index.php'>BACK</a><br><br></span>

<table class="allHalfTd">
	<tr class="fullWidth">
		<td>
			<b><span class="yourName"><?php echo isset($_SESSION['firstName']) ? strtoupper($_SESSION['firstName'] . ' ' . $_SESSION['lastName']) : '' ?></span></b><br><br>
			Bank: <span class="yourBank"><?php echo isset($_SESSION['bankName']) ? strtoupper($_SESSION['bankName']) : '' ?></span><br>
			A/C No.: <span span="yourAccountNumber"><?php echo isset($_SESSION['accountNumber']) ? strtoupper($_SESSION['accountNumber']) : '' ?></span><br>
			IFSC Code: <span span="ifscCode"><?php echo isset($_SESSION['ifscCode']) ? strtoupper($_SESSION['ifscCode']) : '' ?></span><br>
			Email- <span span="yourEmail"><?php echo isset($_SESSION['yourEmail']) ? $_SESSION['yourEmail'] : '' ?></span><br>
			Contact No.: <span class="yourPhone"><?php echo isset($_SESSION['yourPhone']) ? $_SESSION['yourPhone'] : '' ?></span><br>
			Pan Card No: <span class="yourPanCard"><?php echo isset($_SESSION['yourPanCard']) ? strtoupper($_SESSION['yourPanCard']) : '' ?></span>
		</td>
		<td>
			<img class="logo" src="assets/img/logo.jpg">
		</td>
	</tr>
</table><br><br>

<table class="allHalfTd">
	<tr class="fullWidth">
		<td class="alignTop">
			<b><span class="clientCompanyName"><?php echo isset($_SESSION['companyName']) ? strtoupper($_SESSION['companyName']) : '' ?></span></b><br>
			<span class="clientName"><?php echo isset($_SESSION['clientName']) ? ucwords($_SESSION['clientName']) : '' ?></span><br>
			<span class="clientAddress"><?php echo isset($_SESSION['clientAddress']) ? $_SESSION['clientAddress'] : '' ?></span><br>
			Email: <span class="clientEmail"><?php echo isset($_SESSION['clientEmail']) ? $_SESSION['clientEmail'] : '' ?></span><br>
			Contact No.: <span class="clinetPhone"><?php echo isset($_SESSION['clientPhone']) ? $_SESSION['clientPhone'] : '' ?></span>
		</td>
		<td class="alignBottom">
			<span class="fullWidth">
				Invoice: #<span class="align-right invoiceNumber"><?php echo isset($_SESSION['invoiceNumber']) ? strtoupper($_SESSION['invoiceNumber']) : '' ?></span><br>
				Invoice Date: <span class="align-right invoiceDate"><?php echo isset($_SESSION['billingDate']) ? dateFormatter($_SESSION['billingDate']) : '' ?><!-- January 16, 2018 --></span><br>
			</span>
			<table>
				<tr class="fullWidth">
					<td class="grayscale fullWidth tenPadding alignTop">
						<b>Balance Due (INR) <span class="pull-right totalAmount">&#8377; <?php echo numberToCurrency($billingAmount); ?></span></b>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table><br><br>

<table>
	<tr class="grayscale" class="fullWidth tenPadding zeroMargin articlesHeader">
		<th class="tableHeaderLeftValue">Item</th>
		<th class="tableHeaderRightValue">Description</th>
		<th class="tableHeaderRightValue">Unit Cost</th>
		<th class="tableHeaderRightValue">Quantity</th>
		<th class="tableHeaderRightValue">Line Total</th>
	</tr>
	<?php
	if(isset($_SESSION['arrArticles'][0]['lineTotal'])) {
		for($i=0; $i<count($_SESSION['arrArticles']); $i++) {
			echo '<tr class="border_bottom tenPadding"><td class="tableLeftValue">'.$_SESSION['arrArticles'][$i]['item'].'</td><td class="tableRightValue">('.$_SESSION['arrArticles'][$i]['date'].')</td><td class="tableRightValue">'.numberToCurrency($_SESSION['arrArticles'][$i]['unitPrice']).'</td><td class="tableRightValue">'.$_SESSION['arrArticles'][$i]['quantity'].'</td><td class="tableRightValue">'.numberToCurrency($_SESSION['arrArticles'][$i]['lineTotal']).'</td>';
		}
	}
	?>
	<!-- <tr class="border_bottom tenPadding">
		<td class="tableLeftValue">Male for catalogue shoot</td>
		<td class="tableRightValue">(07-01-2018)</td>
		<td class="tableRightValue">2,000.00</td>
		<td class="tableRightValue">1</td>
		<td class="tableRightValue">2,000.00</td>
	</tr> -->
</table>

<hr class="finalLine">

<table class="allHalfTd">
	<tr class="fullWidth">
		<td></td>
		<td>


			<table class="allFullTr allHalfTd">
				<tr>
					<td><b>Total</b></td>
					<td class="align-right sumTotal"><b><?php echo numberToCurrency($billingAmount); ?></b></td>
				</tr>
				<tr">
					<td>Amount Payed</td>
					<td class="align-right amountPayed">0.00</td>
				</tr>
				<tr class="grayscale tenPadding">
					<td class="tenPadding"><b>Balance due (INR)</b></td>
					<td class="tableRightValue totalAmount"><b>&#8377; <?php echo numberToCurrency($billingAmount); ?></b></td>
				</tr>
			</table>


		</td>
	</tr>
</table><br><br>

<p class="halfWidth"><b>Terms</b><br>* Conditions Apply</p>

<div class="printOnBottom">
	<hr class="cut">

	<h1>PAYMENT SLAB</h1>

	<table class="allHalfTd">
		<tr class="fullWidth">
			<td>
				<b><span class="yourName"><?php echo isset($_SESSION['firstName']) ? strtoupper($_SESSION['firstName'] . ' ' . $_SESSION['lastName']) : '' ?></span></b><br>
				Bank: <span class="yourBank"><?php echo isset($_SESSION['bankName']) ? strtoupper($_SESSION['bankName']) : '' ?></span><br>
				A/C No.: <span span="yourAccountNumber"><?php echo isset($_SESSION['accountNumber']) ? strtoupper($_SESSION['accountNumber']) : '' ?></span><br>
				IFSC Code: <span span="ifscCode"><?php echo isset($_SESSION['ifscCode']) ? strtoupper($_SESSION['ifscCode']) : '' ?></span><br>
				Email- <span span="yourEmail"><?php echo isset($_SESSION['yourEmail']) ? $_SESSION['yourEmail'] : '' ?></span><br>
				Contact No.: <span class="yourPhone"><?php echo isset($_SESSION['yourPhone']) ? $_SESSION['yourPhone'] : '' ?></span><br>
				Pan Card No: <span class="yourPanCard"><?php echo isset($_SESSION['yourPanCard']) ? strtoupper($_SESSION['yourPanCard']) : '' ?></span>
			</td>
			<td>
				<table class="allFullTr allHalfTd">
					<tr>
						<td><b>Client</b></td>
						<td class="align-right clientCompanyName"><?php echo isset($_SESSION['companyName']) ? strtoupper($_SESSION['companyName']) : '' ?></td>
					</tr>
					<tr>
						<td><b>Invoice #</b></td>
						<td class="align-right invoiceNumber"><?php echo isset($_SESSION['invoiceNumber']) ? strtoupper($_SESSION['invoiceNumber']) : '' ?></td>
					</tr>
					<tr class="borderBottom">
						<td><b>Invoice Date</b></td>
						<td class="align-right invoiceDate"><?php echo isset($_SESSION['billingDate']) ? dateFormatter($_SESSION['billingDate']) : '' ?><!-- January 16, 2018 --></td>
					</tr>
					<tr class="borderBottom">
						<td><b>Balance Due (INR)</b></td>
						<td class="align-right totalAmount">&#8377; <?php echo numberToCurrency($billingAmount); ?></td>
					</tr>
					<tr class="borderBottom">
						<td><b>Amount Enclosed</b></td>
						<td class="align-right totalAmount">&#8377; <?php echo numberToCurrency($billingAmount); ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

</div>

</body>
</html>