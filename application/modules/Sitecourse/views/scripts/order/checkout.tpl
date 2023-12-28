<!-- if pament method is not enabled and order price is not free -->
<?php if($this->no_payment_gateway_enabled && !$this->totalOrderPriceFree) : ?>
	<div class="payment-error-page">
		<div class="tip">
			<span>
				<?php
				echo $this->translate("Payment gateway is not enabled by Course Owner. Please contact the respective course owner to complete your purchase. Thanks for your patience!");
				?>
			</span>
		</div> 

		<!-- crouse profile page redirection button -->
		<div class="go-back-btn">
			<?php
			$item = Engine_Api::_()->getItem('sitecourse_course', $this->course_id);
			echo $this->htmlLink(Engine_Api::_()->sitecourse()->getHref($item['course_id'], $item['owner_id'],null, $item['title']),$this->translate('Go Back'),array());
			?>
		</div>
	</div>

	<?php return; ?>
<?php endif; ?>


<?php
$temp_online_gateway = false;
$checkout_process = @unserialize($this->checkout_process);
$base_url = $this->layout()->staticBaseUrl; ?>

<?php if(!$this->no_payment_gateway_enabled || $this->totalOrderPriceFree) : ?>
	<div class="generic_layout_container layout_middle">
		<section class="sitecourse_checkout_process_form">
			<div class="sitecourse_checkout_process_title">
				<h1 class="sitecourse_checkout_process_normal mbot10">
					<?php echo $this->translate('Payment Method'); ?>
				</h1>
			</div>
			<div class="sitecourse_payment_methods_wrap">
				<!-- FREE COURSE -->
				<?php if($this->totalOrderPriceFree) : ?>
					<div for="free_order">
						<span><?php echo $this->translate("Free Order") ?></span>
					</div>
					<!-- PAYPAL PAYMENT OPTION  -->
				<?php else: ?>
					<div for="paypal_order">
						<h2><?php echo $this->translate("Paypal") ?></h2>
					</div>
				<?php endif; ?>
				<div class="sitecourse_checkout_process_button">
					<div id="checkout_place_order_error"></div>
					<div class='buttons'>
						<!-- PLACE ORDER -->
						<div class="fright m10">  
							<button type="button" name="place_order" onclick="paymentInformation()" class="fright"><?php echo $this->translate("Place Order") ?></button>
						</div>

						<!-- LOADING IMAGE ICON -->
						<div id="loading_image_4" class="fright mtop10 ptop10" style="display: inline-block;"></div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php endif; ?>

<script>

	function paymentInformation() {
		const priceFree = '<?php echo ($this->totalOrderPriceFree ? 'true':'false'); ?>';
		// not free course
		if(priceFree === 'false') {
			const isPaypalEnabled = '<?php echo (!$this->no_payment_gateway_enabled?'true':
			'false');?>'
			// process payment if paypal enabled otherwise show error
			if(isPaypalEnabled == 'true') {
				placeOrder(String(1),'');
			} else {

			}
		} else {
			placeOrder(String(5), '');
		}
	}

	function placeOrder(param, file) {
		var placeOrderUrl;
		placeOrderUrl = "sitecourse/order/place-order/course_id/<?php echo $this->course_id ?>";

		en4.core.request.send(scriptJquery.ajax({
			url: en4.core.baseUrl + placeOrderUrl,
			method: 'POST',
			beforeSend: function () {
			},
			data: {
				format: 'json',
				checkout_process: '<?php echo serialize($checkout_process); ?>',
				param: param,
				formValues: <?php echo json_encode($this->formValues); ?>,
				isPrivateOrder: 0,
				img : file
			},
			success: function (responseJSON)
			{ 
				console.log(responseJSON);
				if(responseJSON.gateway_id == 1) {
					<?php $payment_url = $this->url(array('action' => 'payment', 'course_id' => $this->course_id), 'sitecourse_order', true) ?>
					window.location = '<?php echo $payment_url ?>/gateway_id/' + responseJSON.gateway_id + '/order_id/' + responseJSON.order_id;
				} else {
					<?php $success_url = $this->url(array('action' => 'success','course_id'=>$this->course_id), 'sitecourse_order', true); ?> 
					window.location = '<?php echo $success_url ?>/success_id/' + responseJSON.order_id;
				}
			}
		}));

	}

</script>
