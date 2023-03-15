<?php
	//echo '<pre> $book_curriculum_data :: '; print_r($book_curriculum_data);
?>

<html>
	<head>
		<title>Book Quiz Completion Certificate</title>

		<style>
			body {
				font-family: Roboto;
			}

			.certificate-container {
				padding: 50px;
			}
			.certificate {
				border: 20px solid #0C5280;
				padding: 25px;
				height: 600px;
				position: relative;
			}

			.certificate:after {
				content: '';
				top: 0px;
				left: 0px;
				bottom: 0px;
				right: 0px;
				position: absolute;
				background-size: 100%;
				z-index: -1;
			}

			.certificate-header > .logo {
				width: 80px;
				height: 80px;
			}

			.certificate-title {
				text-align: center;    
			}

			.certificate-body {
				text-align: center;
			}

			h1 {
				font-weight: 400;
				font-size: 48px;
				color: #0C5280;
			}

			.student-name {
				font-size: 24px;
			}

			.certificate-content {
				margin: 0 auto;
			}

			.about-certificate {
				margin: 0 auto;
			}

			.topic-description {
				text-align: center;
			}

			.certificate-footer {
				margin-top: 90px;
			}
		</style>
	</head>

	<body>
		<div class="certificate-container">
			<div class="certificate">
				<div class="water-mark-overlay"></div>

				<div class="certificate-header">
					
				</div>

				<div class="certificate-body">				   
					<p class="certificate-title"><strong><?php echo $book_title; ?></strong></p>

					<h1>Certificate of Completion</h1>

					<p class="student-name"><?php echo $user_data->first_name.' '.$user_data->last_name; ?> (<?php echo $user_data->username; ?>)</p>

					<div class="certificate-content">
						<div class="about-certificate">
							<p>
								has completed quiz on topic <?php echo $book_title; ?> online on <?php echo $book_exam_quiz_result_data->created_at; ?> by score (<?php echo $book_exam_quiz_result_data->total_correct_answers; ?>/<?php echo count(json_decode($book_exam_quiz_result_data->exam_id)); ?>)
							</p>
						</div>

						<p class="topic-title">
							The Topic includes the following:
						</p>

						<div class="text-center">
							<p class="topic-description text-muted">
								<?php $temp_val = '';
									if(!empty($book_curriculum_data)) {
										foreach($book_curriculum_data as $val) {
											$temp_val .= $val->title.', ';
										}

										echo rtrim($temp_val, ', ');
									}
								?>
							</p>
						</div>
					</div>

					<div class="certificate-footer text-muted">
						<div class="row" style="text-align: left !important;">
							<div class="col-md-6">
								<p>Admin: ______________________</p>

								<!--<img src="<?php echo FCPATH; ?>assets/uploads/images/a4cb2-php_2017022401181912571886.jpg" />-->
								<!--<img src="<?php echo URL_PUBLIC_UPLOADS; ?>images/NicePng_dan-howell-png_1667011.png" />
								<img src="https://books.itbsh.com/assets/uploads/images/download.png" />-->
								<?php //echo $image_signature; ?>
								<?php //echo $image_signature2; ?>
							</div>

							<!--<div class="col-md-6">
								<div class="row">
									<div class="col-md-6">
										<p>
											Accredited by
										</p>
									</div>

									<div class="col-md-6">
										<p>
											Endorsed by
										</p>
									</div>
								</div>
							</div>-->
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>