<!DOCTYPE html>
<html lang="en">

<head>
<link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" >
  <style>
	
	.viewCus_label {
		font-size: 10px;
		color: #222222!important;
		font-weight: 500;
		text-transform: uppercase;
	}
	.viewCus_labelVal {
		font-size: 13px;
		color: #222222!important;
		font-weight: 500;
	}
		.card {
		position: relative;
		display: -ms-flexbox;
		-ms-flex-direction: column;
		flex-direction: column;
		max-width: 90%;
		/* min-width: 50%; */
		margin: 0 auto;
		margin: 15px 0 ;
		word-wrap: break-word;
		/* background: #202940; */
		background-clip: border-box;
		border: 1px solid black;
		border-radius: .25rem;
	}

	.card>hr {
		margin-right: 0;
		margin-left: 0
	}

	.card>.list-group:first-child .list-group-item:first-child {
		border-top-left-radius: .25rem;
		border-top-right-radius: .25rem
	}

	.card>.list-group:last-child .list-group-item:last-child {
		border-bottom-right-radius: .25rem;
		border-bottom-left-radius: .25rem
	}

	.no-margin{
		margin:0 !important;
	}

	.card-body {
		-ms-flex: 1 1 auto;
		flex: 1 1 auto;
		padding: 1.25rem
	}

	.card-text:last-child {
		margin-bottom: 0
	}

	.card-link:hover {
		text-decoration: none
	}

	.card-link+.card-link {
		margin-left: 1.25rem
	}

	.card-header {
		padding: 0 10px;
		margin-bottom: 0;
		/* background-color:rgb(1, 6, 44); */
		border-bottom: 1px solid black;
		font-weight: bold;
	}

	.card-header:first-child {
		border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0
	}

	.card-header+.list-group .list-group-item:first-child {
		border-top: 0
	}


	.card-group {
		display: -ms-flexbox;
		display: flex;
		-ms-flex-direction: column;
		flex-direction: column
	}

	.card-group>.card {
		margin-bottom: 15px
	}

	.card-columns .card {
		margin-bottom: .75rem
	}

	.d-inline-block{display:inline-block!important}

	.row {
		display: -ms-flexbox;
		display: flex;
		-ms-flex-wrap: wrap;
		flex-wrap: wrap;
		margin-right: -15px;
		margin-left: -15px;
		justify-content: center;
	}

	.col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, .col,
	.col-auto, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm,
	.col-sm-auto, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12, .col-md,
	.col-md-auto, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg,
	.col-lg-auto, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl,
	.col-xl-auto {
		position: relative;
		width: 50%;
		padding-right: 15px;
		padding-left: 15px;
	}
  </style>
</head>
<body>   
	<div class="card">
		<div style="text-align:center; font-size: 15px;" class="card-header  card-header-icon card-header-primary">
			<h4  class="card-title  d-inline-block">KYC Rejection</h4>
		</div>
		<div class="card-body">
			<div class="card" style="max-width: 100%">
				<div class="card-header  card-header-icon card-header-primary">
					<h4 class="card-title  d-inline-block">Lead Details</h4>
				</div>
				<div class="card-body">
					<div class="row" style="justify-content: center;" >                                            
						<div class="col-sm-4 col-md-3 ">                                       
							<p class="no-margin viewCus_label">Account Provider</p>
							<p class="no-margin viewCus_labelVal">{{$data['acc_prvdr_code']}}</p>
						</div>
						<div class="col-sm-4 col-md-3">                                       
							<p class="no-margin viewCus_label">Account Number</p>
							<p class="no-margin viewCus_labelVal">{{$data['account_num']}}</p>
						</div>
					</div>    
					<div class="row" style="justify-content: center;margin-top: 15px" >                                            

						<div class="col-sm-4 col-md-3">                                       
							<p class="no-margin viewCus_label">Biz name</p>
							<p class="no-margin viewCus_labelVal">{{$data['biz_name']}}</p>
						</div>
						<div class="col-sm-4 col-md-3">                                       
							<p class="no-margin viewCus_label">Mobile Number</p>
							<p class="no-margin viewCus_labelVal">{{$data['cust_mobile_num']}}</p>
						</div>
					</div>
				</div> 
			</div>
			<div class="card" style="max-width: 100%">
				<div style="" class="card-header  card-header-icon card-header-primary">
					<h4 class="card-title  d-inline-block">RM Details</h4>
				</div>
				<div class="card-body">

					<div class="row" >                                            
						<div class="col-md-3">                                       
							<p class="no-margin viewCus_label">RM Name</p>
							<p class="no-margin viewCus_labelVal">{{$data['rm_name']}}</p>
						</div>
						<div class="col-md-3">                                       
							<p class="no-margin viewCus_label">Contact Number</p>
							<p class="no-margin viewCus_labelVal">{{$data['rm_mbl_num']}}</p>
						</div>
			
					</div> 
				</div>
			</div>
			<div class="card" style="max-width: 100%">
				<div style="" class="card-header  card-header-icon card-header-primary">
					<h4 class="card-title  d-inline-block">Auditor Details</h4>
				</div>
				<div class="card-body">

					<div class="row" >                                            
						<div class="col-md-3">                                       
							<p class="no-margin viewCus_label">Auditor Name</p>
							<p class="no-margin viewCus_labelVal">{{$data['auditor_name']}}</p>
						</div>
					</div> 
				</div>
			</div>
			<div class="card" style="max-width: 100%">
				<div style="" class="card-header  card-header-icon card-header-primary">
					<h4 class="card-title  d-inline-block">Reason For Rejection</h4>
				</div>
				<div class="card-body">

					<div class="row" >                                            
						<div class="col-md-3">                                       
							<p class="no-margin viewCus_labelVal">{{dd_value($data['reason'])}}</p>
						</div>
					</div> 
				</div>
			</div>
		</div>
	</div>
    </div>
</body>

</html>