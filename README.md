# cartRecommendations
This module shows product suggestions on cart to achieve the amount when customer can gain free shipping.

After copying module under your  root folder, add this code into:
app\design\frontend\base\default\template\checkout\cart.phtml

and put this part where you wish:

<?php echo $this->getChildHtml('cart_suggestions'); ?>
