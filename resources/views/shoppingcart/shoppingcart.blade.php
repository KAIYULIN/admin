@extends('layouts.app')

        <!-- 顯示驗證錯誤 -->
  
@section('content')
  @include('common.errors')
   	<body id="top">
		<!-- WRAPPER -->
		<div class="wrapper2">
						<!-- END NAVBAR -->
			<!-- MAIN CONTENT -->
			<div class="main-content" >
				<div class="container">
					<div class="row">
					<div class="col-md-12">
						<div id="checkout-wizard" class="wizard checkout-wizard">
						<div class="step-content">
							<!-- SHOPPING CART -->
							<div class="step-pane active" data-step="1">
								<h2 class="sr-only">Shopping Cart</h2>
								<form action="#" id="form1">
									<table class="table shopping-cart-table">
										<thead>
											<tr>
												<th>商品</th>
												<th>剩餘</th>
												<th>數量</th>
												<th>小計</th>
												<th>&nbsp;</th>
											</tr>
										</thead>
										
									</table>
									<hr>
									<div class="shopping-cart-bottom">
										<div class="row">
											<div class="col-xs-6">
												<div class="form-group coupon">
													<label for="input-coupon-code" class="coupon-label pull-left">帳戶餘額: $&nbsp;<span class="s_money" id="s_money" data="0">0</span></label>													
												</div>
												<br><p class="coupon-label pull-left" style="color:red;" id="money_err">
																								</p>
											</div>
											<div class="col-xs-6">
												<table class="table shopping-cart-summary text-right">
													<tbody>
														<tr>
															<td><strong>總計</strong></td>
															<td class="grand-total">$&nbsp;&nbsp;<strong id="total"></strong></td>
															</tr>
													</tbody>
												</table>
												
											</div>
										</div>
									</div>
								</form>
							</div>
							<!-- END SHOPPING CART -->
						</div>
						<!-- BUTTONS -->
						<div class="cartactions">
													</div>
						<form id="goshop" action="action_shopping.php" method="post">
						<input type="hidden" name="cartstr" id="cartstr" value="">
						</form>
						<!-- END BUTTONS -->
					</div>
					</div>
				</div></div>
			<br><br><br>	
				<!-- FOOTER -->
				<footer>
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-3 col-md-offset-1">
								<!-- COLUMN 1 -->
								<!-- END COLUMN 1 -->
							</div>
							<div class="col-md-2">
								<!-- COLUMN 2 -->
								<!-- END COLUMN 2 -->
							</div>
							<div class="col-md-1">
							</div>									
							<div class="col-md-3">
						</div>
						<!-- COPYRIGHT -->
						<div class="copyright">
						</div>
						<!-- END COPYRIGHT -->
					</div>
				
				</footer>
				<!-- END FOOTER -->
				<div class="back-to-top hidden-xs" style="display: block;">
					<a href="#top"><i class="fa fa-angle-up"></i></a>
				</div>			</div>
		</div>
			<!-- JAVASCRIPT -->
		<script src="assets/js/jquery-2.1.1.min.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/plugins/slick/slick.min.js"></script>
		<script src="assets/js/plugins/jquery.panelslider.min.js"></script>
		<script src="assets/js/plugins/scrollto/jquery.localscroll-1.2.7-min.js"></script>
		<script src="assets/js/plugins/scrollto/jquery.scrollTo-1.4.3.1-min.js"></script>
		<script src="assets/js/plugins/easing/jquery.easing.min.js"></script>
		<script src="assets/js/plugins/autohidingnavbar/jquery.bootstrap-autohidingnavbar.min.js"></script>
		<script src="assets/js/jquery.bootstrap-touchspin.min.js"></script>
		<script src="assets/js/repute-blog-scripts.js"></script>
		<script src="assets/js/repute-scripts.js"></script>
		<script src="assets/js/toastr.js"></script>	
		<script src="assets/js/toastr_action.js"></script>
		<script>
			localStorage.removeItem("cart");
			cartstr = '';
			$("input[class='btnnum']").TouchSpin({min: 0,max: 99999,stepinterval: 50,maxboostedstep: 10000000});
			$( "input[name='btnnum']" ).change(function() {	
				$("#price"+$(this).attr("data")).text($(this).val());
				totalm=0;
				$("input[name='btnnum']").each(function() {
					totalm = totalm + parseInt($(this).val());
				});
				$("#total").text(totalm);
				if( parseInt($("#s_money").attr("data")) <totalm){
					$("#money_err").html('帳戶餘額不足&nbsp;<a href="addmoney.php" class="btn btn-info">前往儲值</a>');
				}else{
					$("#money_err").html('');
				} 
			});
			$( "#checkout" ).click(function() {
					if($("#money_err").html().trim()==''){
						$("input[name='btnnum']").each(function() {
							cartstr = cartstr +"{\"id\":" + $(this).attr("data")+',\"num\":'+ $(this).val() + '},';
						});
						cartstr = cartstr.substring(0,cartstr.length - 1);
						cartstr = "["+cartstr+"]";
						$("#cartstr").val(cartstr);
						console.log(cartstr);
						$('form#goshop').submit();
					}
			});		
		</script>
	</body>

@endsection