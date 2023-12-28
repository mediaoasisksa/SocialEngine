<?php 

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/styles/style_sitecourse_dashboard.css');
?>

<div class="course_builder_dashboard">
	<div class="course_builder_dashboard_container">

		<?php 
		$id = $this->course_id;
		$blockId = 2;
		$liId ='transaction';
		include_once APPLICATION_PATH . '/application/modules/Sitecourse/views/scripts/dashboard/_menu.tpl'; ?>

		<div class="course_builder_dashboard_sections">
			<div class="course_builder_dashboard_sections_list">
				<div class="layout_middle">
					<div class="course_builder_dashboard_sections_header">
						<div class="course_builder_dashboard_sections_header_title">
							<img src="<?php echo $this->images['image_icon'];?>" alt="" />
							<h3><?php echo $this->translate('Course Dashboard'); ?></h3>
						</div>
						<?php include_once APPLICATION_PATH . '/application/modules/Sitecourse/views/scripts/dashboard/_dashboardNavigation.tpl'; ?>
					</div>      
					<div class="generic_layout_container">
						<!-- search filter form -->
						<form method="get" class="search__filter_form admin_search_form">
							<div class="dashboard_form_wrapper">
								<div class="dashboard_form_label">
									<label><?php echo $this->translate('User Name'); ?></label>
								</div>
								<div class="dashboard_form_element">
									<input type="text" name="user_name">
								</div>
							</div>
							<div class="dashboard_form_wrapper">
								<div class="dashboard_form_label">
									<label><?php echo $this->translate('Start Date'); ?></label>
								</div>
								<div class="dashboard_form_element">
									<input type="date" name="from">
								</div>
							</div>
							<div class="dashboard_form_wrapper">
								<div class="dashboard_form_label">
									<label><?php echo $this->translate('End Date'); ?></label>
								</div>
								<div class="dashboard_form_element">
									<input type="date" name="to">
								</div>
							</div>
							<div class="dashboard_form_wrapper">
								<button type="submit" name="search"><?php echo $this->translate('Search'); ?></button>
							</div>
						</form>
						<?php if(count($this->paginator)): ?>

							<table class='admin_table' width="100%">

								<thead>
									<th align="left">
										<?php echo $this->translate('ID'); ?></a>
									</th>

									<th align="left">
										<?php echo $this->translate('User Name'); ?></a>
									</th>
									<th align="left" >
										<?php echo $this->translate('Transaction ID'); ?></a>
									</th>

									<th align="left" >
										<?php echo $this->translate('Status'); ?></a>
									</th>
									<th align="left">
										<?php echo $this->translate('Date'); ?></a>
									</th>

								</thead>

								<?php foreach($this->paginator as $item): ?>

									<tbody>
										<td class='admin_table_bold admin-txt-normal'>
											<?php echo $item->transaction_id; ?>
										</td>
										<td class='admin_table_bold'> 
											
											<?php $user = Engine_Api::_()->user()->getUser($item->user_id); 
											echo $this->htmlLink($user->getHref(), $user->getTitle(), array('target' => '_blank'));?>
										</td>
										<td class="admin_table_bold">
										<?php if( $item->gateway_transaction_id ): ?>
											<?php echo $this->htmlLink(array(
												'route' => 'admin_default',
												'module' => 'sitecourse',
												'controller' => 'manage',
												'action' => 'detail-transaction',
												'transaction_id' => $item->transaction_id,
											), $item->gateway_transaction_id, array(
												'target' => '_blank',
											)) ?>
										<?php else: ?>
											-
										<?php endif; ?>
									</td>
									<td class='admin_table_bold admin-txt-normal'>
											Successful
										</td>
									<td>
										<?php echo date('d-M-Y',strtotime($item->date)); ?>
									</td>
								</tbody>
							<?php endforeach; ?>
						</table>

					<?php else: ?>
						<div class="tip">
							<span><?php echo $this->translate('No Transactions Found.'); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
</div>










