<?php 


function productListTemplate($r,$o) {
	return $r.<<<HTML
	<a class="col-xs-12 col-md-4" href="product_item.php?id=$o->id">
		<figure class="figure product display-flex flex-column">
			<div class="flex-stretch">
			<img src="../img/$o->thumbnail" alt="">
			</div>
			<figcaption class="flex-none">
				<div>&dollar;$o->price</div>
				<div>$o->model</div>
			</figcaption>
		</figure>
	</a>

	HTML;
}

function selectAmount($amount=1,$total=10) {
	$output = "<select name='amount'>";
	for($i=1;$i<=$total;$i++) {
		$output .= "<option ".($i==$amount?"selected":"").">$i</option>";
	}
	$output .= "</select>";
	return $output;
}


function cartListTemplate($r,$o) {
$totalfixed = number_format($o->total,2,'.','');
$selectamount = selectAmount($o->amount,10);
return $r.<<<HTML
<div class="display-flex">
		<div class="flex-none images-thumbs">
				<img src="../img/$o->thumbnail">
		</div>
		<div class="flex-stretch">
				<strong>$o->model</strong>
				<form action="cart_actions.php?action=delete-cart-item" method="post">
					<input type="hidden" name="id" value="$o->id">
					<input type="submit" class="form-button inline" value="Delete" style="font-size: 0.6em">
				</form>
		</div>
		<div class="flex-none">
				<div>&dollar;$totalfixed</div>
				<form action="cart_actions.php?action=update-cart-item" method="post" onchange="this.submit()">
						<input type="hidden" name="id" value="$o->id">
				 		<div class="form-select" style="font-size: 0.6em">
				 			$selectamount
						</div>
				</form>
		</div>
</div>
HTML;
}

function cartTotals() {
	$cart = getCartItems ();

	$cartprice = array_reduce($cart,function($r,$o){return $r + $o->total;},0);

	$pricefixed = number_format($cartprice,2,'.','');
	$taxfixed = number_format($cartprice*0.0912,2,'.','');
	$taxedfixed = number_format($cartprice*1.0912,2,'.','');

	return <<<HTML
				<div class="card-sction display-flex">
					<div class="flex-stretch"><strong>Sub Total</strong></div>
					<div class="flex-none">&dollar;$pricefixed</div>
				</div>

				<div class="card-sction display-flex">
					<div class="flex-stretch"><strong>Taxes</strong></div>
					<div class="flex-none">&dollar;$taxfixed</div>
				</div>

				<div class="card-sction display-flex">
					<div class="flex-stretch"><strong>Total</strong></div>
					<div class="flex-none">&dollar;$taxedfixed</div>
				</div>
				

	HTML;
}


function recommendedProducts($a) {
$products = array_reduce($a,'productListTemplate');
echo <<<HTML
<div class="grid gap productlist">$products</div>
HTML;
}


function recommendedAnything($limit=3) {
	$result = makeQuery(makeConn(),"SELECT * FROM `products` ORDER BY rand() DESC LIMIT $limit");
	recommendedProducts($result);
}

function recommendedCategory($cat,$limit=3) {
	$result = makeQuery(makeConn(),"SELECT * FROM `products` WHERE `category` = '$cat' ORDER BY `date_create` DESC LIMIT $limit");
	recommendedProducts($result);
}

function recommendedSimilar($cat,$id=0,$limit=3) {
	$result = makeQuery(makeConn(),"SELECT * FROM `products` WHERE `category` = '$cat' AND `id`<>$id ORDER BY rand() LIMIT $limit");
	recommendedProducts($result);
}