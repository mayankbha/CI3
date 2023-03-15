   <!-- >> Blog-->
   <?php $this->session->unset_userdata('req_from'); ?>
   <section class="blog-content">
       <div class="container">
           <div class="row row-margin">
               <?php echo $this->session->flashdata('message'); ?>
               
               <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                   <?php 

                   // echo "<pre>";print_r($uid);
                   // echo "<pre>";print_r($record->seller_id);
                   // die();

                   //if ((!empty($record->image) && file_exists(URL_PUBLIC_UPLOADS . 'book_curriculum_files/' . $record->image)) || (!empty($record->preview_file) && file_exists(URL_PUBLIC_UPLOADS . 'book_curriculum_files/' . $record->preview_file))) { ?>
                    <?php if(!empty($record->image) || !empty($record->preview_file)) { ?>
                                 <div class="play-video">
                        <?php if(!empty($record->image)) { ?>
                          <img src="data:image/jpg;base64, <?php echo $record->book_image_arr['image']; ?>" class="img-responsive" alt="<?php echo $record->image; ?>" style="height:400px;">
                        <?php } ?>

                        <?php if(!empty($record->preview_file)) {
                          $ext_explode = explode('/', $record->book_image_arr['preview_file_mimetype']);
                          $ext = $ext_explode[1];

                                          //$ext = pathinfo($record->preview_file, PATHINFO_EXTENSION);

                          if (in_array($ext, unserialize(VIDEO_FORMATS)))
                                              $icls = 'fa fa-play-circle-o';
                                          else if (in_array($ext, unserialize(AUDIO_FORMATS)))
                                              $icls = 'fa fa-file-audio-o';
                                          else if (in_array($ext, unserialize(IMAGE_FORMATS)))
                                              $icls = 'fa fa-image';
                                          else
                                              $icls = 'fa fa-file-text-o';

                                          $file_src = $record->book_image_arr['preview_file'];
                                          //$file_src = URL_PUBLIC_UPLOADS2 . 'book_curriculum_files/' . $record->preview_file;
                                      ?>
                                         <a href="javascript: void(0);" onclick="openIframe('<?php echo $record->book_image_arr['preview_file_mimetype']; ?>', '<?php echo $record->book_image_arr['preview_file']; ?>');">
                                             <i title="<?php echo get_languageword('Click_to_view'); ?>" class="pop-original <?php echo $icls; ?>"></i>
                                         </a>
                                     <?php } ?>
                                 </div>
                             <?php } ?>
                             <h3><?php if (!empty($record->book_title)) echo $record->book_title; ?> <div class="sharethis-inline-share-buttons" data-url="<?= URL_HOME_BUY_BOOK . '/' . $record->slug . "/" . urlencode(base64_encode($this->session->userdata['user_id'] . "_" . $record->sc_id)); ?>" style="display:inline-block;"></div>
                             </h3>
                             <div id="selling_book_comment-section" class="selling_book_comment-section">
                                 <div class="bg-white">

                                     <!-- <div class="d-flex flex-row fs-12"> -->
                                     <?php //var_dump($record); die;  
                                      ?>

                                     <!-- <div class="like p-2 cursor review_textarea_focus"><i class="fa fa-commenting-o"></i><span class="ml-1">Comment</span></div> -->
                                     <?php /* <div class="like p-2 cursor"><a href="https://www.facebook.com/sharer/sharer.php?u=<?= URL_HOME_BUY_BOOK . '/' . $record->slug; ?>"><i class="fa fa-share"></i><span class="ml-1">Share</span></a></div> */ ?>
                                     <!-- <div class=" p-2 ">
                                               <a href="#" data-toggle="modal" data-target="#myModal"><i class="fa fa-envelope  "></i><span class="ml-1"><?= " Refer to friend" ?></span></a>
                                         </div> -->
                                     <!-- </div> -->
                                     <div class="d-flex flex-row fs-12 ">
                                         <?php if ($this->ion_auth->logged_in()  && !($this->ion_auth->is_admin())) { ?>

                                             <div class="like p-2 cursor <?= ($likecommentsystem->userliked == "yes" ? 'like_color_blue' : '') ?>" onclick="likecallback('<?= $this->session->userdata['user_id'] ?>','<?= $record->sc_id ?>')"><i class="fa fa-thumbs-o-up"></i><span class="ml-1 likeml_<?= $record->sc_id ?>"><?= ($likecommentsystem->userliked == "yes" ? "UnLike" : "Like") ?></span></div>
                                         <?php } ?>
                                         <div class="p-2 thumbs-up-list-page <?= ($likecommentsystem->userliked ? 'like_color_blue' : '') ?>">
                                             <div class=" pt-2 "><i class="fa fa-thumbs-up"></i> <span class="ml-1">Likes (<?= ($likecommentsystem->likescount ? $likecommentsystem->likescount : 0) ?>)</span></div>
                                         </div>
                                         <div class="p-2 average_rating">
                                             <a href="<?= URL_HOME_BUY_BOOK . '/' . $record->slug; ?>#selling_book_comment-section">
                                                 <span class="ml-1">
                                                     <?php

                                                      $totalratingscount = 0;
                                                      if ($likecommentsystem->totalratingscount > 0) {
                                                          $totalratingscount = $likecommentsystem->totalratingscount / $likecommentsystem->ratingscount;
                                                      }
                                                      $totalratingscount = number_format((float)$totalratingscount, 1, '.', '');

                                                      echo $totalratingscount;

                                                      ?>

                                                 </span>

                                                 <?php

                                                  //  var_dump($totalratingscount); die; 
                                                  for ($iv = 1; $iv <= 5; $iv++) : ?>

                                                     <label for="star-<?= $iv ?>">
                                                         <svg width="12" height="12" viewBox="0 0 51 48">
                                                             <?php
                                                              $fillstoke = '';
                                                              if ($iv <= $totalratingscount) {
                                                                  echo "working here" . $iv;
                                                                  $fillstoke = 'fill="#FFBB00" stroke="#cc9600"';
                                                              }
                                                              ?>
                                                             <path <?= $fillstoke ?> d="m25,1 6,17h18l-14,11 5,17-15-10-15,10 5-17-14-11h18z" />
                                                         </svg>
                                                     </label>
                                                 <?php endfor; ?>

                                                 <span class="ml-1">(<?= $likecommentsystem->ratingscount ?>) </span>

                                             </a>
                                         </div>

                                         <div class=" p-2 ">
                                             <div class=" pt-2 total_comments">
                                                 <a href="<?= URL_HOME_BUY_BOOK . '/' . $record->slug; ?>#selling_book_comment-section"><i class="fa fa-commenting-o  "></i><span class="ml-1"><?= " Comments " . "(" . $likecommentsystem->ratingscount . ")" ?></span></a>
                                             </div>
                                         </div>
                                         <?php /* <div class=" p-2 ">
                                             <div class=" pt-2 ">
                                                 <a href="#" data-toggle="modal" data-target="#myModal"><i class="fa fa-envelope  "></i><span class="ml-1"><?= " Refer to friend" ?></span></a>
                                             </div>
                                         </div> */ ?>
                                     </div>
                                 </div>
                             </div>
                             <ul class="related-videos">
                                 <li>by <?php if (!empty($record->username)) echo $record->username; ?></li>
                                 <li><?php if (!empty($record->updated_at)) echo date('M jS, Y', strtotime($record->updated_at)); ?></li>
                                 <br>
                                 <li> <a href="<?php echo URL_HOME_BUY_BOOKS; ?>">
                                  <?php
                                  $cat = $this->db->select('*')->where('id', $record->category_id)->get('pre_categories')->row();
                                  echo $cat->name; ?></a></li>
                                 <li> <a href="<?php echo URL_HOME_BUY_BOOKS; ?>"><?php if (!empty($record->book_name)) echo $record->book_name; ?></a></li>
                             </ul>
                             <!-- Video Description-->
                             <?php if (!empty($record->description)) echo $record->description; ?>
                             <!-- /Video Description-->
                             <!-- Content -->
                             <?php if (!empty($record->sellingbook_curriculum)) { ?>
                                 <h2 class="heading-line mtop4"><?php echo get_languageword('content'); ?></h2>
                                 <ul class="list-group">
                                     <?php foreach ($record->sellingbook_curriculum as $key => $value) {
                                          $onClickEvent = '$(this).next(\'ul\').find(\'a\').click();';

                                          if (!empty($value->file_ext)) {
                                              $ext = $value->file_ext;

                                              if (in_array($ext, unserialize(VIDEO_FORMATS))) {
                                                  $iclass = 'fa fa-play-circle';
                                              } else if (in_array($ext, unserialize(AUDIO_FORMATS))) {
                                                  $iclass = 'fa fa-file-audio';
                                              } else if (in_array($ext, unserialize(IMAGE_FORMATS))) {
                                                  $iclass = 'fa fa-image';
                                              } else {
                                                  $iclass = 'fa fa-file-text';
                                              }

                                              $videoPopClass = ($value->is_free == '1') ? 'videopopUp' : '';
                                              $file_src = ($value->is_free == '1') ? URL_PUBLIC_UPLOADS2 . 'book_curriculum_files/' . $value->file_name : 'javascript:void(0)';

                            if($ext == 'pdf') {
                              $freeTxtTitle = ($value->is_free == '1') ? '<a target= "_blank" href="'.$value->presignedUrl.'">(Free Preview)</a>' : '';

                              $preview_txt = '<a style="position:relative; left:0;" href="javascript: void(0);">
                                        <li>
                                          <font color="#e27d7f">
                                            <i class="' . $iclass . '"></i>
                                          </font>
                                        </li>
                                      </a>';
                            } else {
                              $freeTxtTitle = ($value->is_free == '1') ? '<a href="javascript: void(0);" onclick="openIframe(\''.$value->mimetype.'\', \''.$value->s3_file.'\');">(Free Preview)</a>' : '';

                              $preview_txt = '<a style="position:relative; left:0;" href="javascript: void(0);">
                                        <li>
                                          <font color="#e27d7f">
                                            <i class="' . $iclass . '"></i>
                                          </font>
                                        </li>
                                      </a>';
                            }
                                          } else {
                                              $iclass = 'fa fa-link';

                                              $file_src = $value->file_name;

                                              //$file_src = ($value->is_free == '1') ? $value->file_name : 'javascript:void(0)';
                                              $onClickEvent = 'javascript:window.open(\'' . $value->file_name . '\', \'_blank\');';

                            $freeTxtTitle = ($value->is_free == '1') ? '<a target="_blank" href="' . $file_src . '">(Free Preview)</a>' : '';

                                              $preview_txt = '<a href="javascript: void(0);">
                                                  <li>
                                                      <font color="#e27d7f">
                                                          <i class="' . $iclass . '"></i>
                                                      </font>
                                                  </li>
                                              </a>';
                                          }
                                      ?>
                                         <li class="list-group-item">
                                             <?php echo $value->title . ' ' . $freeTxtTitle; ?>
                                             <ul class="lessions-list">
                                                 <?php echo $preview_txt; ?>
                                             </ul>
                                         </li>
                                     <?php } ?>
                                 </ul>
                             <?php } ?>
                             <!-- /Curriculam -->
                             <?php if ($this->config->item('site_settings')->like_comment_setting == "Yes") : ?>
                                 <div id="selling_book_comment-section" class="selling_book_comment-section">

                                     <div class="bg-light p-2">

                                         <div class="feedback">
                                             <h2 class="heading-line mtop4">Comment and rating <span class="total_rating_count">(0)</span></h2>

                                             <div class="rating-wraper-parent">

                                             </div>
                                             <?php if ($this->ion_auth->logged_in()  && !($this->ion_auth->is_admin())) : ?>
                                                 <form id="review_form_view" action="">
                                                     <h3>Submit your review</h3>
                                                     <div class="rating">
                                                         <?php for ($i = 5; $i >= 1; $i--) : ?>
                                                             <input type="radio" name="rating" value="<?= $i ?>" id="rating-<?= $i ?>" <?= ($i == $likecommentsystem->rating_number) ? 'Checked="Checked"' : "" ?>>
                                                             <label for="rating-<?= $i ?>"></label>
                                                         <?php endfor; ?>
                                                         <div class="emoji-wrapper">
                                                             <div class="emoji">
                                                                 <svg class="rating-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                     <circle cx="256" cy="256" r="256" fill="#ffd93b" />
                                                                     <path d="M512 256c0 141.44-114.64 256-256 256-80.48 0-152.32-37.12-199.28-95.28 43.92 35.52 99.84 56.72 160.72 56.72 141.36 0 256-114.56 256-256 0-60.88-21.2-116.8-56.72-160.72C474.8 103.68 512 175.52 512 256z" fill="#f4c534" />
                                                                     <ellipse transform="scale(-1) rotate(31.21 715.433 -595.455)" cx="166.318" cy="199.829" rx="56.146" ry="56.13" fill="#fff" />
                                                                     <ellipse transform="rotate(-148.804 180.87 175.82)" cx="180.871" cy="175.822" rx="28.048" ry="28.08" fill="#3e4347" />
                                                                     <ellipse transform="rotate(-113.778 194.434 165.995)" cx="194.433" cy="165.993" rx="8.016" ry="5.296" fill="#5a5f63" />
                                                                     <ellipse transform="scale(-1) rotate(31.21 715.397 -1237.664)" cx="345.695" cy="199.819" rx="56.146" ry="56.13" fill="#fff" />
                                                                     <ellipse transform="rotate(-148.804 360.25 175.837)" cx="360.252" cy="175.84" rx="28.048" ry="28.08" fill="#3e4347" />
                                                                     <ellipse transform="scale(-1) rotate(66.227 254.508 -573.138)" cx="373.794" cy="165.987" rx="8.016" ry="5.296" fill="#5a5f63" />
                                                                     <path d="M370.56 344.4c0 7.696-6.224 13.92-13.92 13.92H155.36c-7.616 0-13.92-6.224-13.92-13.92s6.304-13.92 13.92-13.92h201.296c7.696.016 13.904 6.224 13.904 13.92z" fill="#3e4347" />
                                                                 </svg>
                                                                 <svg class="rating-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                     <circle cx="256" cy="256" r="256" fill="#ffd93b" />
                                                                     <path d="M512 256A256 256 0 0 1 56.7 416.7a256 256 0 0 0 360-360c58.1 47 95.3 118.8 95.3 199.3z" fill="#f4c534" />
                                                                     <path d="M328.4 428a92.8 92.8 0 0 0-145-.1 6.8 6.8 0 0 1-12-5.8 86.6 86.6 0 0 1 84.5-69 86.6 86.6 0 0 1 84.7 69.8c1.3 6.9-7.7 10.6-12.2 5.1z" fill="#3e4347" />
                                                                     <path d="M269.2 222.3c5.3 62.8 52 113.9 104.8 113.9 52.3 0 90.8-51.1 85.6-113.9-2-25-10.8-47.9-23.7-66.7-4.1-6.1-12.2-8-18.5-4.2a111.8 111.8 0 0 1-60.1 16.2c-22.8 0-42.1-5.6-57.8-14.8-6.8-4-15.4-1.5-18.9 5.4-9 18.2-13.2 40.3-11.4 64.1z" fill="#f4c534" />
                                                                     <path d="M357 189.5c25.8 0 47-7.1 63.7-18.7 10 14.6 17 32.1 18.7 51.6 4 49.6-26.1 89.7-67.5 89.7-41.6 0-78.4-40.1-82.5-89.7A95 95 0 0 1 298 174c16 9.7 35.6 15.5 59 15.5z" fill="#fff" />
                                                                     <path d="M396.2 246.1a38.5 38.5 0 0 1-38.7 38.6 38.5 38.5 0 0 1-38.6-38.6 38.6 38.6 0 1 1 77.3 0z" fill="#3e4347" />
                                                                     <path d="M380.4 241.1c-3.2 3.2-9.9 1.7-14.9-3.2-4.8-4.8-6.2-11.5-3-14.7 3.3-3.4 10-2 14.9 2.9 4.9 5 6.4 11.7 3 15z" fill="#fff" />
                                                                     <path d="M242.8 222.3c-5.3 62.8-52 113.9-104.8 113.9-52.3 0-90.8-51.1-85.6-113.9 2-25 10.8-47.9 23.7-66.7 4.1-6.1 12.2-8 18.5-4.2 16.2 10.1 36.2 16.2 60.1 16.2 22.8 0 42.1-5.6 57.8-14.8 6.8-4 15.4-1.5 18.9 5.4 9 18.2 13.2 40.3 11.4 64.1z" fill="#f4c534" />
                                                                     <path d="M155 189.5c-25.8 0-47-7.1-63.7-18.7-10 14.6-17 32.1-18.7 51.6-4 49.6 26.1 89.7 67.5 89.7 41.6 0 78.4-40.1 82.5-89.7A95 95 0 0 0 214 174c-16 9.7-35.6 15.5-59 15.5z" fill="#fff" />
                                                                     <path d="M115.8 246.1a38.5 38.5 0 0 0 38.7 38.6 38.5 38.5 0 0 0 38.6-38.6 38.6 38.6 0 1 0-77.3 0z" fill="#3e4347" />
                                                                     <path d="M131.6 241.1c3.2 3.2 9.9 1.7 14.9-3.2 4.8-4.8 6.2-11.5 3-14.7-3.3-3.4-10-2-14.9 2.9-4.9 5-6.4 11.7-3 15z" fill="#fff" />
                                                                 </svg>
                                                                 <svg class="rating-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                     <circle cx="256" cy="256" r="256" fill="#ffd93b" />
                                                                     <path d="M512 256A256 256 0 0 1 56.7 416.7a256 256 0 0 0 360-360c58.1 47 95.3 118.8 95.3 199.3z" fill="#f4c534" />
                                                                     <path d="M336.6 403.2c-6.5 8-16 10-25.5 5.2a117.6 117.6 0 0 0-110.2 0c-9.4 4.9-19 3.3-25.6-4.6-6.5-7.7-4.7-21.1 8.4-28 45.1-24 99.5-24 144.6 0 13 7 14.8 19.7 8.3 27.4z" fill="#3e4347" />
                                                                     <path d="M276.6 244.3a79.3 79.3 0 1 1 158.8 0 79.5 79.5 0 1 1-158.8 0z" fill="#fff" />
                                                                     <circle cx="340" cy="260.4" r="36.2" fill="#3e4347" />
                                                                     <g fill="#fff">
                                                                         <ellipse transform="rotate(-135 326.4 246.6)" cx="326.4" cy="246.6" rx="6.5" ry="10" />
                                                                         <path d="M231.9 244.3a79.3 79.3 0 1 0-158.8 0 79.5 79.5 0 1 0 158.8 0z" />
                                                                     </g>
                                                                     <circle cx="168.5" cy="260.4" r="36.2" fill="#3e4347" />
                                                                     <ellipse transform="rotate(-135 182.1 246.7)" cx="182.1" cy="246.7" rx="10" ry="6.5" fill="#fff" />
                                                                 </svg>
                                                                 <svg class="rating-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                     <circle cx="256" cy="256" r="256" fill="#ffd93b" />
                                                                     <path d="M407.7 352.8a163.9 163.9 0 0 1-303.5 0c-2.3-5.5 1.5-12 7.5-13.2a780.8 780.8 0 0 1 288.4 0c6 1.2 9.9 7.7 7.6 13.2z" fill="#3e4347" />
                                                                     <path d="M512 256A256 256 0 0 1 56.7 416.7a256 256 0 0 0 360-360c58.1 47 95.3 118.8 95.3 199.3z" fill="#f4c534" />
                                                                     <g fill="#fff">
                                                                         <path d="M115.3 339c18.2 29.6 75.1 32.8 143.1 32.8 67.1 0 124.2-3.2 143.2-31.6l-1.5-.6a780.6 780.6 0 0 0-284.8-.6z" />
                                                                         <ellipse cx="356.4" cy="205.3" rx="81.1" ry="81" />
                                                                     </g>
                                                                     <ellipse cx="356.4" cy="205.3" rx="44.2" ry="44.2" fill="#3e4347" />
                                                                     <g fill="#fff">
                                                                         <ellipse transform="scale(-1) rotate(45 454 -906)" cx="375.3" cy="188.1" rx="12" ry="8.1" />
                                                                         <ellipse cx="155.6" cy="205.3" rx="81.1" ry="81" />
                                                                     </g>
                                                                     <ellipse cx="155.6" cy="205.3" rx="44.2" ry="44.2" fill="#3e4347" />
                                                                     <ellipse transform="scale(-1) rotate(45 454 -421.3)" cx="174.5" cy="188" rx="12" ry="8.1" fill="#fff" />
                                                                 </svg>
                                                                 <svg class="rating-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                     <circle cx="256" cy="256" r="256" fill="#ffd93b" />
                                                                     <path d="M512 256A256 256 0 0 1 56.7 416.7a256 256 0 0 0 360-360c58.1 47 95.3 118.8 95.3 199.3z" fill="#f4c534" />
                                                                     <path d="M232.3 201.3c0 49.2-74.3 94.2-74.3 94.2s-74.4-45-74.4-94.2a38 38 0 0 1 74.4-11.1 38 38 0 0 1 74.3 11.1z" fill="#e24b4b" />
                                                                     <path d="M96.1 173.3a37.7 37.7 0 0 0-12.4 28c0 49.2 74.3 94.2 74.3 94.2C80.2 229.8 95.6 175.2 96 173.3z" fill="#d03f3f" />
                                                                     <path d="M215.2 200c-3.6 3-9.8 1-13.8-4.1-4.2-5.2-4.6-11.5-1.2-14.1 3.6-2.8 9.7-.7 13.9 4.4 4 5.2 4.6 11.4 1.1 13.8z" fill="#fff" />
                                                                     <path d="M428.4 201.3c0 49.2-74.4 94.2-74.4 94.2s-74.3-45-74.3-94.2a38 38 0 0 1 74.4-11.1 38 38 0 0 1 74.3 11.1z" fill="#e24b4b" />
                                                                     <path d="M292.2 173.3a37.7 37.7 0 0 0-12.4 28c0 49.2 74.3 94.2 74.3 94.2-77.8-65.7-62.4-120.3-61.9-122.2z" fill="#d03f3f" />
                                                                     <path d="M411.3 200c-3.6 3-9.8 1-13.8-4.1-4.2-5.2-4.6-11.5-1.2-14.1 3.6-2.8 9.7-.7 13.9 4.4 4 5.2 4.6 11.4 1.1 13.8z" fill="#fff" />
                                                                     <path d="M381.7 374.1c-30.2 35.9-75.3 64.4-125.7 64.4s-95.4-28.5-125.8-64.2a17.6 17.6 0 0 1 16.5-28.7 627.7 627.7 0 0 0 218.7-.1c16.2-2.7 27 16.1 16.3 28.6z" fill="#3e4347" />
                                                                     <path d="M256 438.5c25.7 0 50-7.5 71.7-19.5-9-33.7-40.7-43.3-62.6-31.7-29.7 15.8-62.8-4.7-75.6 34.3 20.3 10.4 42.8 17 66.5 17z" fill="#e24b4b" />
                                                                 </svg>
                                                                 <svg class="rating-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                     <g fill="#ffd93b">
                                                                         <circle cx="256" cy="256" r="256" />
                                                                         <path d="M512 256A256 256 0 0 1 56.8 416.7a256 256 0 0 0 360-360c58 47 95.2 118.8 95.2 199.3z" />
                                                                     </g>
                                                                     <path d="M512 99.4v165.1c0 11-8.9 19.9-19.7 19.9h-187c-13 0-23.5-10.5-23.5-23.5v-21.3c0-12.9-8.9-24.8-21.6-26.7-16.2-2.5-30 10-30 25.5V261c0 13-10.5 23.5-23.5 23.5h-187A19.7 19.7 0 0 1 0 264.7V99.4c0-10.9 8.8-19.7 19.7-19.7h472.6c10.8 0 19.7 8.7 19.7 19.7z" fill="#e9eff4" />
                                                                     <path d="M204.6 138v88.2a23 23 0 0 1-23 23H58.2a23 23 0 0 1-23-23v-88.3a23 23 0 0 1 23-23h123.4a23 23 0 0 1 23 23z" fill="#45cbea" />
                                                                     <path d="M476.9 138v88.2a23 23 0 0 1-23 23H330.3a23 23 0 0 1-23-23v-88.3a23 23 0 0 1 23-23h123.4a23 23 0 0 1 23 23z" fill="#e84d88" />
                                                                     <g fill="#38c0dc">
                                                                         <path d="M95.2 114.9l-60 60v15.2l75.2-75.2zM123.3 114.9L35.1 203v23.2c0 1.8.3 3.7.7 5.4l116.8-116.7h-29.3z" />
                                                                     </g>
                                                                     <g fill="#d23f77">
                                                                         <path d="M373.3 114.9l-66 66V196l81.3-81.2zM401.5 114.9l-94.1 94v17.3c0 3.5.8 6.8 2.2 9.8l121.1-121.1h-29.2z" />
                                                                     </g>
                                                                     <path d="M329.5 395.2c0 44.7-33 81-73.4 81-40.7 0-73.5-36.3-73.5-81s32.8-81 73.5-81c40.5 0 73.4 36.3 73.4 81z" fill="#3e4347" />
                                                                     <path d="M256 476.2a70 70 0 0 0 53.3-25.5 34.6 34.6 0 0 0-58-25 34.4 34.4 0 0 0-47.8 26 69.9 69.9 0 0 0 52.6 24.5z" fill="#e24b4b" />
                                                                     <path d="M290.3 434.8c-1 3.4-5.8 5.2-11 3.9s-8.4-5.1-7.4-8.7c.8-3.3 5.7-5 10.7-3.8 5.1 1.4 8.5 5.3 7.7 8.6z" fill="#fff" opacity=".2" />
                                                                 </svg>
                                                             </div>
                                                         </div>
                                                     </div>
                                                     <input type="hidden" name="user_id" value="<?= $this->session->userdata['user_id'] ?>">
                                                     <input type="hidden" name="item_id" value="<?= $record->sc_id ?>">
                                                     <div class="d-flex flex-row align-items-start"><label class="display-4">Comment<textarea id="review_textarea" placeholder="Type here" class="form-control ml-1 shadow-none textarea" name="review"> <?= (($likecommentsystem->userliked == 'yes') ? $likecommentsystem->review : '') ?></textarea></label></div>
                                                     <div class="mt-2 "><button class="btn btn-primary btn-sm shadow-none submit-review" type="button">Post review</button></div>

                                                 </form>
                                             <?php endif; ?>
                                         </div>
                                     </div>
                                 </div>

                             <?php endif; ?>
                             <!-- Related Items-->
                             <?php if (!empty($more_selling_books) && count($more_selling_books) > 1) { ?>
                                 <h2 class="heading-line mtop4">More from This Seller Name</h2>
                                 <div class="row">
                                     <?php foreach ($more_selling_books as $foreachrecord) {
                                          if ($record->sc_id != $record->sc_id) {
                                      ?>
                                             <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                                 <!-- Sigle blog post -->
                                                 <div class="blog-card">
                                                     <div class="blog-card-img related-itm-img">
                                                         <a href="<?php echo URL_HOME_BUY_BOOK . '/' . $foreachrecord->slug; ?>">
                                                             <figure class="imghvr-zoom-in "><img src="<?php echo get_selling_book_img($foreachrecord->image); ?>" alt="" class="img-responsive">
                                                                 <figcaption>
                                                                     <span class="btn btn-read"><?php echo get_languageword('Get_This_Book'); ?></span>
                                                                 </figcaption>
                                                             </figure>
                                                         </a>
                                                         <div class="blog-card-ribbon"><?php echo $foreachrecord->book_name; ?></div>
                                                     </div>
                                                     <p class="related-link"><a href="<?php echo URL_HOME_BUY_BOOK . '/' . $foreachrecord->slug; ?>"> <?php echo $foreachrecord->book_title; ?> </a></p>
                                                     <ul class="related-videos">
                                                         <?php
                                                          if ($foreachrecord->book_price > 0) {
                                                              $more_discounted_price = $foreachrecord->book_price;
                                                              if ($this->ion_auth->logged_in() && $this->ion_auth->is_buyer()) {
                                                                  $userDetails = getUserRec();
                                                                  $more_discounted_price = getBuyerDiscountedPrice($foreachrecord->sc_id, $userDetails->id);
                                                              }
                                                          ?>
                                                             <li>
                                                                 <?php echo $this->config->item('site_settings')->currency_symbol . ' ' . $more_discounted_price; ?>
                                                             </li>
                                                         <?php } else { ?>
                                                             <li>
                                                                 Free
                                                             </li>
                                                         <?php } ?>
                                                         <li>
                                                             <?php if (!empty($foreachrecord->updated_at)) echo date('M jS, Y', strtotime($foreachrecord->updated_at)); ?>
                                                         </li>
                                                     </ul>
                                                 </div>
                                                 <!-- Sigle blog post Ends -->
                                             </div>
                                     <?php }
                                      } ?>
                                 </div>
                             <?php } ?>
                   <!-- /Related Items-->
               </div>
               <!-- Sidebar/Widgets bar -->
               <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                   <!-- Price Widget -->
                   <div class="get-video-book">
                       <h4 class="sell-price">
                           <?php
                            $actual_price = $record->actual_price;
                            $discount_price = $record->book_price;
                            $actual_price = $record->actual_price;
                            $discount_price = $record->book_price;
                            if ($this->ion_auth->logged_in() && $this->ion_auth->is_buyer() && $actual_price > 0) {
                                $userDetails = getUserRec();
                                $discount_price = getBuyerDiscountedPrice($record->sc_id, $userDetails->id);
                            }
                            $code = $this->config->item('site_settings')->currency_symbol;
                            $discount = (($actual_price - $discount_price) / $actual_price) * 100;
                            $discount = round($discount);
                            //40 to 30 is: (40-30)/40 * 100 = 25%.
                            if ($discount_price < $record->actual_price) {
                                echo
                                '<span style="color:red;"><del>'
                                    . $code
                                    . ' '
                                    . $record->actual_price . '</del><span>'
                                    . '<span style="color:white;"> | </span>'
                                    . '<span style="color:#2cdd9b;">'
                                    . $code
                                    . ' '
                                    . $discount_price
                                    . '</span>';
                            } elseif ($discount_price > 0) {
                                echo
                                '<span style="color:#2cdd9b;">'
                                    . $code
                                    . ' '
                                    . $discount_price
                                    . '</span>';
                            } else {
                                echo
                                '<span style="color:#2cdd9b;"> FREE </span>';
                            }
                            ?>
                           <br>
                           <?php
                            if ($discount_price < $record->actual_price) {
                                echo
                                '<span style="color:#2cdd9b;">'
                                    . $discount
                                    . '% Discount </span>';
                            } else {
                            }
                            ?>
                       </h4>
                       <div class="mobile-effect">
                           <?php if ($this->ion_auth->is_buyer()) { ?>
                               <?php if ($discount_price > 0 && empty($is_purchased) && $is_purchased->max_downloads < 1) { ?>
                                   <a href="<?php echo URL_HOME_CHECKOUT . '/' . $record->slug; ?>" class="btn-accept"><?php echo get_languageword('Buy_This_Book'); ?>
                                   </a>
                               <?php } elseif (!empty($is_purchased) && $is_purchased->max_downloads > 0) { ?>
                                   <a href="<?php echo URL_BUYER_BOOK_PURCHASES; ?>" class="btn-accept"><?php echo get_languageword('Already_Purchased'); ?>
                                   </a>
                               <?php } else { ?>
                                   <a href="<?php echo URL_HOME_GET_FREE . '/' . $record->slug; ?>" class="btn-accept"><?php echo get_languageword('Get_This_Book'); ?>
                                   </a>
                               <?php } ?>
                           <?php } elseif ($this->ion_auth->is_seller()) { ?>
                               <a href="#" class="btn-accept"><?php echo get_languageword('Sellers_Cannot_Buy_Or_Get'); ?>
                               </a>
                           <?php } elseif ($this->ion_auth->is_admin()) { ?>
                               <a href="#" class="btn-accept"><?php echo get_languageword('Admins_Cannot_Buy_Or_Get'); ?>
                               </a>
                           <?php } else { ?>
                               <?php if ($discount_price > 0) { ?>
                                   <a href="https://eyeniversum.com/home/auth/login" class="btn-accept"><?php echo get_languageword('Login_As_Student_to_Buy'); ?>
                                   </a>
                               <?php } else { ?>
                                   <a href="https://eyeniversum.com/home/auth/login" class="btn-accept"><?php echo get_languageword('Login_As_Student_to_Get'); ?>
                                   </a>
                               <?php } ?>
                           <?php } ?>
                       </div>
                       <ul class="list">
                           <?php if (!empty($record->sellingbook_curriculum)) { ?>
                               <li class="list-item">
                                   <span class="list-left">File(s)</span>
                                   <span class="list-right"><?php echo count($record->sellingbook_curriculum); ?></span>
                               </li>
                           <?php } ?>
                           <?php if (!empty($record->skill_level)) { ?>
                               <li class="list-item">
                                   <span class="list-left"><?php echo get_languageword('Skill_Level'); ?></span>
                                   <span class="list-right" title="<?php echo $record->skill_level; ?>"><?php echo $record->skill_level; ?></span>
                               </li>
                           <?php } ?>
                           <?php if (!empty($record->languages)) { ?>
                               <li class="list-item">
                                   <span class="list-left"><?php echo get_languageword('languages'); ?></span>
                                   <span class="list-right" title="<?php echo $record->languages; ?>">
                                       <?php echo $record->languages; ?>
                                   </span>
                               </li>
                           <?php } ?>
                           <?php if (!empty($record->max_downloads)) { ?>
                               <li class="list-item">
                                   <span class="list-left" title="<?php echo get_languageword('Maximum_Downloads'); ?>"><?php echo get_languageword('Max_Downloads'); ?></span>
                                   <span class="list-right"> <?php echo $record->max_downloads; ?> </span>
                               </li>
                           <?php } ?>
                       </ul>
                   </div>
                   <!-- /Price Widget -->
                   <!-- Seller Widget -->
                   <div class="get-video-book mtop4 text-center hidden-sm hidden-xs">
                       <?php if (!empty($record->photo) || !empty($record->username)) { ?>
                           <div class="profile-img"><img class="img-responsive img-circle center-block " src="<?php echo get_seller_img($record->photo, $record->gender); ?>" alt="<?php echo $record->username; ?>"></div>
                           <h4><?php if (!empty($record->username)) echo $record->username; ?></h4>
                       <?php } ?>
                       <?php if (!empty($record->profile)) { ?>
                           <p class="blog-info-text"><?php echo $record->profile; ?></p>
                       <?php } ?>
                   </div>
                   <!-- /Seller Widget-->
                   <!-- Attachment Widget-->
                   <?php if (!empty($is_purchased) && $is_purchased->max_downloads > 0) { ?>
                       <div class="get-video-attachment mtop4">
                           <h4><?php echo get_languageword('attachments'); ?></h4>
                           <ul>
                               <li><a href="<?php echo URL_BUYER_BOOK_PURCHASES; ?>"><i class="fa fa fa-download"></i> <?php echo get_languageword('Download'); ?></a></li>
                           </ul>
                       </div>
                   <?php } ?>
                   <!-- /Attachment Widget -->
               </div>
           </div>
       </div>
   </section>

   <!-- The Modal -->
   <div class="modal" id="myModal">
       <div class="modal-dialog">
           <div class="modal-content" style="width: fit-content;">
               <!-- Modal Header -->
               <div class="modal-header">
                   <h4 class="modal-title">Reffer and earn</h4>
                   <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>
               <!-- Modal body -->
               <div class="modal-body">
                   <div class="form-group">
                       <label for="exampleContact1">Full name</label>
                       <input type="text" class="form-control" id="exampleInputFullname1" placeholder="Fullname">
                   </div>
                   <div class="form-group">
                       <label for="exampleInputEmail1">Email address</label>
                       <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                       <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                   </div>
                   <div class="form-group">
                       <label for="exampleContact1">Contact</label>
                       <input type="text" class="form-control" id="exampleInputContact1" placeholder="Contact">
                   </div>
               </div>
               <!-- Modal footer -->
               <div class="modal-footer">
                   <button class="btn ">Refer Now</button>
                   <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
               </div>
           </div>
       </div>
   </div>

	<!-- The Iframe Modal -->
   <div class="modal bd-example-modal-lg" id="iframeModal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-lg modal-dialog-centered">
           <div class="modal-content">
				<!-- Modal Header -->
				<div class="modal-header">
					<?php if (!empty($is_purchased) && $is_purchased->max_downloads > 0) { ?>
					   <span><a href="<?php echo URL_BUYER_DOWNLOAD_BOOK.'/'.$is_purchased->purchase_id; ?>"><i class="fa fa fa-download"></i> <?php echo get_languageword('Download'); ?></a></span>
					<?php } ?>

					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>

				<!-- Modal body -->
				<div class="modal-body">
					<!--<iframe  style="zoom:0.60" frameborder="0" id="iframe_src"></iframe>-->

					<img id="image_file" style="display: none;" />

					<embed id="pdf_file" style="display: none;" width='100%' height='100%' src='data:application/"+file_ext+";base64, " + encodeURI(file)+"#toolbar=0&navpanes=0&scrollbar=0'></embed>

					<!--<audio id='audio_file' style="display: none;" controls='controls' autobuffer='autobuffer' autoplay='autoplay'><source src='data:audio/"+file_ext+";base64, "+file+"' /></audio>

					<video id='video_file' style="display: none;" controls='controls' autobuffer='autobuffer' autoplay='autoplay' src='data:video/"+file_ext+";base64, "+file+"'>-->

					<div id="jp_container_1" class="jp-video" role="application" aria-label="media player" style="width: 100%; height: auto;">
						<div class="jp-type-playlist">
							<div id="jquery_jplayer_1" class="jp-jplayer"></div>
							<div class="jp-gui">
								<div class="jp-video-play">
									<button class="jp-video-play-icon" role="button" tabindex="0">play</button>
								</div>
								<div class="jp-interface">
									<div class="jp-progress">
										<div class="jp-seek-bar">
											<div class="jp-play-bar"></div>
										</div>
									</div>
									<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
									<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
									<div class="jp-details">
										<div class="jp-title" aria-label="title">&nbsp;</div>
									</div>
									<div class="jp-controls-holder">
										<div class="jp-volume-controls">
											<button class="jp-mute" role="button" tabindex="0">mute</button>
											<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
											<div class="jp-volume-bar">
												<div class="jp-volume-bar-value"></div>
											</div>
										</div>
										<div class="jp-controls">
											<button class="jp-previous" role="button" tabindex="0">previous</button>
											<button class="jp-play" role="button" tabindex="0">play</button>
											<button class="jp-stop" role="button" tabindex="0">stop</button>
											<button class="jp-next" role="button" tabindex="0">next</button>
										</div>
										<div class="jp-toggles">
											<button class="jp-repeat" role="button" tabindex="0">repeat</button>
											<button class="jp-shuffle" role="button" tabindex="0">shuffle</button>
											<button class="jp-full-screen" role="button" tabindex="0">full screen</button>
										</div>
									</div>
								</div>
							</div>
							<div class="jp-playlist">
								<ul>
									<!-- The method Playlist.displayPlaylist() uses this unordered list -->
									<li></li>
								</ul>
							</div>
							<div class="jp-no-solution">
								<span>Update Required</span>
								To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
							</div>
						</div>
					</div>
				</div>

				<!-- Modal footer -->
				<!--<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>-->
			</div>
		</div>
	</div>

	<script type="text/javascript">
		function openIframe(mimetype, s3_file) {
			var explode_mimetype = mimetype.split('/');
			var file_format = explode_mimetype [0];

			$('#image_file').hide();
			$('#pdf_file').hide();

			//$('#audio_file').hide();
			//$('#video_file').hide();

			if(file_format == 'image') {
				$('#jp_container_1').hide();
				$('#image_file').show();
				$('#image_file').attr('src', 'data:'+mimetype+';base64,' + s3_file);
			} else if(file_format == 'application') {
				$('#jp_container_1').hide();
				let pdfWindow = window.open("");
				pdfWindow.document.write("<html<head><title>"+s3_file+"</title><style>body{margin: 0px;}iframe{border-width: 0px;}</style></head>");
				pdfWindow.document.write("<body><embed width='100%' height='100%' src='data:"+mimetype+";base64, " + encodeURI(s3_file)+"#toolbar=0&navpanes=0&scrollbar=0'></embed></body></html>");

				//$('#pdf_file').show();
				//$('#pdf_file').attr('src', 'data:'+mimetype+';base64,' + s3_file);
			} else if(file_format == 'audio') {
				$('#jp_container_1').show();
				$("#jquery_jplayer_1").jPlayer("play");

				//$('#audio_file').show();
				//$('#audio_file').attr('src', 'data:'+mimetype+';base64,' + s3_file);
			} else if(file_format == 'video') {
				$('#jp_container_1').show();
				$("#jquery_jplayer_1").jPlayer("play");

				//$('#video_file').show();
				//$('#video_file').attr('src', 'data:'+mimetype+';base64,' + s3_file);
			}

			$('#iframeModal').modal('show');

			//$('#iframe_src').attr("src", "https://wwf.org");
			//$('#iframe_src').attr("src", "data:"+mimetype+";base64, "+s3_file);
		}

       function callback_rating_list() {
           setTimeout(() => {
               $.ajax({
                   type: 'POST',
                   url: '<?php echo base_url("home/view_list_reviewrating"); ?>',
                   data: {
                       'book_id': <?= $record->sc_id ?>,
                       'average_rating': 1,
                   },
                   success: function(data) {
                       if (data.response == 'success') {

                           $(".rating-wraper-parent").html(data.response_list);
                           if (data.average_rating_html) {
                               $(".average_rating").html(data.average_rating_html)
                           }

                           if (data.total_comments) {
                               $(".total_comments .ml-1").html("Comments (" + data.total_comments + ")");
                               $(".total_rating_count").text("(" + data.total_comments + ")");
                           }

                       }
                       if (data.response == 'error') {
                           alert("There is some problem please contact admin.");
                       }
                   }
               });
           }, 1000);
       }
       document.addEventListener("DOMContentLoaded", function(event) {
			//alert('Hi');
			$("#iframeModal").on('hidden.bs.modal', function () {
				//alert('The modal is completely hidden now!');
				$("#jquery_jplayer_1").jPlayer("stop");
			});

			$('#iframeModal').on('show.bs.modal', function () {
				//alert('modal shows');

				//$(this).find('.modal-content').css('width', 'fit-content');

				$(this).find('.modal-header').css('margin-top', '-14px');
				$(this).find('.modal-header').css('padding', '18px 4px 18px 16px');
			});

		   new jPlayerPlaylist({
				jPlayer: "#jquery_jplayer_1",
				cssSelectorAncestor: "#jp_container_1",
			},
			<?php echo json_encode($curriculum_file_arr); ?>
			/*[
				{
					title:"Cro Magnon Man",
					artist:"The Stark Palace",
					mp3:"http://www.jplayer.org/audio/mp3/TSP-01-Cro_magnon_man.mp3",
					oga:"http://www.jplayer.org/audio/ogg/TSP-01-Cro_magnon_man.ogg",
					poster: "http://www.jplayer.org/audio/poster/The_Stark_Palace_640x360.png"
				},
				{
					title:"Your Face",
					artist:"The Stark Palace",
					mp3:"http://www.jplayer.org/audio/mp3/TSP-05-Your_face.mp3",
					oga:"http://www.jplayer.org/audio/ogg/TSP-05-Your_face.ogg",
					poster: "http://www.jplayer.org/audio/poster/The_Stark_Palace_640x360.png"
				},
				{
					title:"Hidden",
					artist:"Miaow",
					mp3:"http://www.jplayer.org/audio/mp3/Miaow-02-Hidden.mp3",
					oga:"http://www.jplayer.org/audio/ogg/Miaow-02-Hidden.ogg",
					poster: "http://www.jplayer.org/audio/poster/Miaow_640x360.png"
				},
				{
					title:"Big Buck Bunny Trailer",
					artist:"Blender Foundation",
					m4v:"http://www.jplayer.org/video/m4v/Big_Buck_Bunny_Trailer.m4v",
					ogv:"http://www.jplayer.org/video/ogv/Big_Buck_Bunny_Trailer.ogv",
					webmv: "http://www.jplayer.org/video/webm/Big_Buck_Bunny_Trailer.webm",
					poster:"http://www.jplayer.org/video/poster/Big_Buck_Bunny_Trailer_480x270.png"
				},
				{
					title:"Finding Nemo Teaser",
					artist:"Pixar",
					m4v: "http://www.jplayer.org/video/m4v/Finding_Nemo_Teaser.m4v",
					ogv: "http://www.jplayer.org/video/ogv/Finding_Nemo_Teaser.ogv",
					webmv: "http://www.jplayer.org/video/webm/Finding_Nemo_Teaser.webm",
					poster: "http://www.jplayer.org/video/poster/Finding_Nemo_Teaser_640x352.png"
				},
				{
					title:"Cyber Sonnet",
					artist:"The Stark Palace",
					mp3:"http://www.jplayer.org/audio/mp3/TSP-07-Cybersonnet.mp3",
					oga:"http://www.jplayer.org/audio/ogg/TSP-07-Cybersonnet.ogg",
					poster: "http://www.jplayer.org/audio/poster/The_Stark_Palace_640x360.png"
				},
				{
					title:"Incredibles Teaser",
					artist:"Pixar",
					m4v: "http://www.jplayer.org/video/m4v/Incredibles_Teaser.m4v",
					ogv: "http://www.jplayer.org/video/ogv/Incredibles_Teaser.ogv",
					webmv: "http://www.jplayer.org/video/webm/Incredibles_Teaser.webm",
					poster: "http://www.jplayer.org/video/poster/Incredibles_Teaser_640x272.png"
				},
				{
					title:"Tempered Song",
					artist:"Miaow",
					mp3:"http://www.jplayer.org/audio/mp3/Miaow-01-Tempered-song.mp3",
					oga:"http://www.jplayer.org/audio/ogg/Miaow-01-Tempered-song.ogg",
					poster: "http://www.jplayer.org/audio/poster/Miaow_640x360.png"
				},
				{
					title:"Lentement",
					artist:"Miaow",
					mp3:"http://www.jplayer.org/audio/mp3/Miaow-03-Lentement.mp3",
					oga:"http://www.jplayer.org/audio/ogg/Miaow-03-Lentement.ogg",
					poster: "http://www.jplayer.org/audio/poster/Miaow_640x360.png"
				}
			]*/
			, {
				swfPath: "dist/jplayer",
				supplied: "webmv, ogv, m4v, oga, mp3",
				useStateClassSkin: true,
				autoBlur: false,
				smoothPlayBar: true,
				keyEnabled: true,
				audioFullScreen: true,
				size: {
					width: "100%",
					height: "487px"
				},
				sizeFull: {
					width: "100%",
					height: "487px"
				},
				option: {
					"fullscreen": true
				}
			});

           setTimeout(() => {
               $(".rating-wraper-parent").html(' <div id="preloader" style="position: relative;display: block;"><div id="status" style="width: 30px;height: 30px;display: block;"></div></div>');
           }, 200);
           callback_rating_list();
           // focus on textarea   
           $('.review_textarea_focus').click(function() {
               $('#review_textarea').focus();
           });

           // on submit review 
           $('.submit-review').on("click", function() {

               callback_rating_list();

               $.ajax({
                   type: 'POST',
                   url: '<?php echo base_url("home/do_review"); ?>',
                   data: $('#review_form_view').serialize(),
                   success: function(data) {

                       if (data.response == 'success') {
                           alert(data.message);
                           callback_rating_list();
                       }
                       if (data.response == 'error') {
                           alert(data.message);
                       }
                       if (data.response == 'redirect') {
                           alert(data.message);
                           location.href = '/auth/login';
                       }
                   }
               });
           });
       });

       function likecallback(user_id, book_id) {
           if (user_id && book_id) {
               $.ajax({
                   type: 'POST',
                   url: '<?php echo base_url("home/do_likecomment"); ?>',
                   data: {
                       'user_id': user_id,
                       'book_id': book_id,
                   },
                   success: function(data) {
                       if (data.response == 'success') {
                           if (data.action == 'add') {
                               $(".likeml_" + book_id).closest(".like.p-2.cursor").addClass("like_color_blue");
                               $(".likeml_" + book_id).text("Unlike");
                           } else {
                               $(".likeml_" + book_id).closest(".like.p-2.cursor").removeClass("like_color_blue");
                               $(".likeml_" + book_id).text("Like");
                           }
                           $(".thumbs-up-list-page .ml-1").text(data.totalcount + " Likes");
                           alert(data.message);
                       }
                       if (data.response == 'error') {
                           alert("There is some problem please contact admin.");
                       }
                   }
               });
           } else {
               location.href = '/auth/login';
           }
       }
   </script>

   <?php if ($this->session->userdata['user_id']) : ?>
       <script>
           document.addEventListener("DOMContentLoaded", function(event) {

               $('body').on('click', '.st-btn', function() {



                   var datanetwork = $(this).attr("data-network");

                   var sharethisurlcustom = $(this).closest('.sharethis-inline-share-buttons').attr('data-url');

                   alert("You can earn point onetime by sharing each network!");


                   $.ajax({
                       url: "https://count-server.sharethis.com/v2.0/get_counts?url=" + sharethisurlcustom,
                       type: 'GET',
                       dataType: 'json', // added data type
                       success: function(shares) {
                           var totalsharercount = shares.total;
                           setInterval(
                               function() {
                                   $.ajax({
                                       url: "https://count-server.sharethis.com/v2.0/get_counts?url=" + sharethisurlcustom,
                                       type: 'GET',
                                       dataType: 'json', // added data type
                                       success: function(shares) {
                                           if (shares.total > totalsharercount) {
                                               $.ajax({
                                                   type: 'POST',
                                                   url: '<?php echo base_url("home/do_sharerpointcredit"); ?>',
                                                   data: {
                                                       'user_id': "<?= $this->session->userdata['user_id'] ?>",
                                                       'book_id': "<?= $record->sc_id ?>",
                                                       'datanetwork': datanetwork,
                                                   },
                                                   success: function(data) {

                                                       if (data.success == 1) {
                                                           clearInterval();
                                                       }

                                                   }
                                               });
                                           }
                                           totalsharercount = shares.total;
                                       }
                                   });
                               }, 10000);
                       }
                   });

               });

           });
       </script>
   <?php else : ?>
       <script type="text/javascript">
           document.addEventListener("DOMContentLoaded", function(event) {

               $('body').on('click', '.st-btn', function() {

                   alert("You can earn point onetime by sharing each network but you must be first logged in!");

               });
           });
       </script>
   <?php endif; ?>