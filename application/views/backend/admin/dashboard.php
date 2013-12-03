<section id="main">
	<!-- START Bootstrap Navbar -->
	<div class="navbar navbar-static-top">
		<div class="navbar-inner">
			<!-- Breadcrumb -->
			<ul class="breadcrumb">
				<li>
					<a href="#">Dashboard</a><span class="divider"></span>
				</li>
			</ul>
			<!--/ Breadcrumb -->
		</div>
	</div>
	<!--/ END Bootstrap Navbar -->

	<!-- START Content -->
	<div class="container-fluid">
		<!-- START Row -->
		<div class="row-fluid">
			<!-- START Page/Section header -->
			<div class="span12">
				<div class="page-header line1">
					<h4>Dashboard <small>This is the place where everything started</small></h4>
				</div>
			</div>
			<!--/ END Page/Section header -->
		</div>
		<!--/ END Row -->

		<!--Page Content Start  -->
		<div id="Dashboard">
			<!-- START Row -->
			<div class="row-fluid">
				<!-- START Circular Summary -->
				<div class="span12 widget borderless">
					<section class="body">
						<div class="body-inner no-padding" style="text-align:center;">
							<div class="span3">
								<div class="dashboard-stat blue">
									<div style="text-align:center;" class="details">
										<div class="number">
											<?php echo $TargetPendingCount; ?>
										</div>
										<div class="desc">
											Pending Targets
										</div>
									</div>
									<div class="more"></div>
									<!--a class="more" href="#"> View more <i class="m-icon-swapright m-icon-white"></i> </a-->
								</div>
							</div>
							<div class="span3">
								<div class="dashboard-stat green">
									<div class="details">
										<div class="number">
											<?php echo $NewInquiryCount; ?>
										</div>
										<div class="desc">
											New Inquiry
										</div>
									</div>
									<div class="more"></div>
									<!--a class="more" href="#"> View more <i class="m-icon-swapright m-icon-white"></i> </a-->
								</div>
							</div>
							<div class="span3">
								<div class="dashboard-stat purple">
									<div class="details">
										<div class="number">
											<?php echo $StudentResigsterCount; ?>
										</div>
										<div class="desc">
											Student Register
										</div>
									</div>
									<div class="more"></div>
									<!--a class="more" href="#"> View more <i class="m-icon-swapright m-icon-white"></i> </a-->
								</div>
							</div>
							<div class="span3">
								<div class="dashboard-stat yellow">
									<div class="details">
										<div class="number">
											<?php echo $FacultyCount; ?>
										</div>
										<div class="desc">
											Total Faculty
										</div>
									</div>
									<div class="more"></div>
									<!--a class="more" href="#"> View more <i class="m-icon-swapright m-icon-white"></i> </a-->
								</div>
							</div>
						</div>
					</section>
				</div>
				<!--/ END Circular Summary -->
			</div>
			<!--/ END Row -->

			<!-- START Row -->
			<div class="row-fluid">
				<!-- START Page/Section header -->
				<div class="span12">
					<div class="page-header line1">
						<h4>Charts <small>organizing days for social, religious, commercial, or administrative purposes.</small></h4>
					</div>
				</div>
				<!--/ END Page/Section header -->
			</div>
			<!--/ END Row -->

			<!-- START Row -->
			<div class="row-fluid">
				<!-- START Line Chart - Filled -->
				<div class="span12 widget stacked">
					<header>
						<h4 class="title">Rich Chart</h4>
					</header>
					<section class="body">
						<div class="body-inner">
							<!-- START nested Grid -->
							<div class="row-fluid">
								<div class="span6">
									<div class="flot" id="site_activities" style="height:300px;"></div>
								</div>
								<div class="span6">
									<div class="flot" id="site_statistics" style="height:300px;"></div>
								</div>
							</div>
							<!--/ END nested Grid -->
						</div>
						
					</section>
				</div>
				<!--/ END Line Chart - Filled -->
			</div>
			<!--/ END Row -->

			<!-- START Row -->
			<div class="row-fluid">
				<!-- START Page/Section header -->
				<div class="span12">
					<div class="page-header line1">
						<h4>Calendar <small>organizing days for social, religious, commercial, or administrative purposes.</small></h4>
					</div>
				</div>
				<!--/ END Page/Section header -->
			</div>
			<!--/ END Row -->

			<!-- START Row -->
			<div class="row-fluid">
				<!-- START Default Calendar -->
				<div class="span12">
					<div id="calendar" style="margin-bottom:20px;"></div>
				</div>
				<!--/ END Default Calendar -->
			</div>
			<!--/ END Row -->
		</div>
		<!--Page Content End  -->
	</div>
	<!--/ END Content -->

</section>
