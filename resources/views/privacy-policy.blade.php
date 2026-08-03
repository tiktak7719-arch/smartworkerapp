@extends('layouts.app')



@section('content')  


<html lang="en">
  
<!-- Mirrored from demo.graygrids.com/themes/jobboard/privacy-policy.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 08 Jul 2026 09:23:35 GMT -->
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="author" content="Jobboard">
    
    <title>JobBoard - Bootstrap HTML5 Job Portal Template</title>    

    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/img/favicon.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">    
    <link rel="stylesheet" href="assets/css/jasny-bootstrap.min.css" type="text/css">  
    <link rel="stylesheet" href="assets/css/bootstrap-select.min.css" type="text/css">  
    <!-- Material CSS -->
    <link rel="stylesheet" href="assets/css/material-kit.css" type="text/css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css" type="text/css"> 
    <link rel="stylesheet" href="assets/fonts/themify-icons.css"> 
    <!-- Color Switcher -->
    <link rel="stylesheet" href="assets/css/color-switcher.css" type="text/css"> 
    <!-- Animate CSS -->
    <link rel="stylesheet" href="assets/extras/animate.css" type="text/css">
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="assets/extras/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="assets/extras/owl.theme.css" type="text/css">
    <!-- Rev Slider CSS -->
    <link rel="stylesheet" href="assets/extras/settings.css" type="text/css"> 
    <!-- Slicknav js -->
    <link rel="stylesheet" href="assets/css/slicknav.css" type="text/css">
    <!-- Main Styles -->
    <link rel="stylesheet" href="assets/css/main.css" type="text/css">
    <!-- Responsive CSS Styles -->
    <link rel="stylesheet" href="assets/css/responsive.css" type="text/css">

    <!-- Color CSS Styles  -->
    <link rel="stylesheet" type="text/css" href="assets/css/colors/red.css" media="screen" />
    
  </head>

  <body>  
      <!-- Header Section Start -->
      <div class="header">    
        <div class="logo-menu">
          <nav class="navbar navbar-default main-navigation" role="navigation" data-spy="affix" data-offset-top="50">
            <div class="container">
              <!-- Brand and toggle get grouped for better mobile display -->
              <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand logo" href="{{ url('/') }}"><img src="assets/img/logo.png" alt=""></a>
              </div>

              <div class="collapse navbar-collapse" id="navbar">              
                 <!-- Start Navigation List -->
                <ul class="nav navbar-nav">
                  <li><a href="{{ url('/') }}">Home</a></li>
                  <li>
                    <a class="active" href="about.html">
                    Pages <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown">
                      <li>
                        <a href="about.html">
                        About
                        </a>
                      </li>
                      <li>
                        <a href="job-page.html">
                        Job Page
                        </a>
                      </li>
                      <li>
                        <a href="job-details.html">
                        Job Details
                        </a>
                      </li>
                      <li>
                        <a href="resume.html">
                        Resume Page
                        </a>
                      </li>
                      <li>
                        <a class="active" href="privacy-policy.html">
                        Privacy Policy
                        </a>
                      </li>
                      <li>
                        <a href="faq.html">
                        FAQ
                        </a>
                      </li>
                      <li>
                        <a href="pricing.html">
                        Pricing Tables
                        </a>
                      </li>
                      <li>
                        <a href="contact.html">
                        Contact
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li>
                    <a href="#">
                    Candidates <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown">
                      <li>
                        <a href="browse-jobs.html">
                        Browse Jobs
                        </a>
                      </li>
                      <li>
                        <a href="browse-categories.html">
                        Browse Categories
                        </a>
                      </li>
                      <li>
                        <a href="add-resume.html">
                        Add Resume
                        </a>
                      </li>
                      <li>
                        <a href="manage-resumes.html">
                        Manage Resumes
                        </a>
                      </li>
                      <li>
                        <a href="job-alerts.html">
                        Job Alerts
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li>
                    <a href="#">
                    Employers <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown">
                      <li>
                        <a href="{{ url('/post-job') }}">
                        Add Job
                        </a>
                      </li>
                      <li>
                        <a href="manage-jobs.html">
                        Manage Jobs
                        </a>
                      </li>
                      <li>
                        <a href="manage-applications.html">
                        Manage Applications
                        </a>
                      </li>
                      <li>
                        <a href="browse-resumes.html">
                        Browse Resumes
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li>
                    <a href="/blog">
                    Blog <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown">
                      <li>
                        <a href="/blog">
                        Blog - Right Sidebar
                        </a>
                      </li>
                      <li>
                        <a href="blog-left-sidebar.html">
                        Blog - Left Sidebar
                        </a>
                      </li>
                      <li><a href="blog-full-width.html">Blog - Full Width</a></li>
                      <li>
                        <a href="single-post.html">
                        Blog Single Post
                        </a>
                      </li>
                    </ul>
                  </li>
                </ul>
                <ul class="nav navbar-nav navbar-right float-right">
                  <li class="left"><a href="{{ url('/post-job') }}"><i class="ti-pencil-alt"></i> Post A Job</a></li>
                  <li class="right"><a href="my-account.html"><i class="ti-lock"></i>  Log In</a></li>
                </ul>
              </div>
            </div>
            <!-- Mobile Menu Start -->
            <ul class="wpb-mobile-menu">
              <li><a href="{{ url('/') }}">Home</a></li>
              <li>
                <a class="active" href="about.html">Pages</a>
                <ul>
                  <li><a href="about.html">About</a></li>
                  <li><a href="job-page.html">Job Page</a></li>
                  <li><a href="job-details.html">Job Details</a></li>
                  <li><a href="resume.html">Resume Page</a></li>
                  <li><a class="active" href="privacy-policy.html">Privacy Policy</a></li>
                  <li><a href="faq.html">FAQ</a></li>
                  <li><a href="pricing.html">Pricing Tables</a></li>
                  <li><a href="contact.html">Contact</a></li>
                </ul>
              </li>
              <li>
                <a href="#">For Candidates</a>
                <ul>
                  <li><a href="browse-jobs.html">Browse Jobs</a></li>
                  <li><a href="browse-categories.html">Browse Categories</a></li>
                  <li><a href="add-resume.html">Add Resume</a></li>
                  <li><a href="manage-resumes.html">Manage Resumes</a></li>
                  <li><a href="job-alerts.html">Job Alerts</a></li>
                </ul>
              </li>
              <li>
                <a href="#">For Employers</a>
                <ul>
                  <li><a href="{{ url('/post-job') }}">Add Job</a></li>
                  <li><a href="manage-jobs.html">Manage Jobs</a></li>
                  <li><a href="manage-applications.html">Manage Applications</a></li>
                  <li><a href="browse-resumes.html">Browse Resumes</a></li>
                </ul>
              </li> 
              <li>
                <a href="/blog">Blog</a>
                <ul class="dropdown">
                  <li><a href="/blog">Blog - Right Sidebar</a></li>
                  <li><a href="blog-left-sidebar.html">Blog - Left Sidebar</a></li>
                  <li><a href="blog-full-width.html">Blog - Full Width</a></li>
                  <li><a href="single-post.html">Blog Single Post</a></li>
                </ul>
              </li>  
              <li class="btn-m"><a href="{{ url('/post-job') }}"><i class="ti-pencil-alt"></i> Post A Job</a></li>
              <li class="btn-m"><a href="my-account.html"><i class="ti-lock"></i>  Log In</a></li>          
            </ul>
            <!-- Mobile Menu End --> 
          </nav>

          <!-- Off Canvas Navigation -->
          <div class="navmenu navmenu-default navmenu-fixed-left offcanvas"> 
          <!--- Off Canvas Side Menu -->
            <div class="close" data-toggle="offcanvas" data-target=".navmenu">
                <i class="ti-close"></i>
            </div>
              <h3 class="title-menu">All Pages</h3>
              <ul class="nav navmenu-nav">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="about.html">About us</a></li>            
                <li><a href="job-page.html">Job Page</a></li>             
                <li><a href="job-details.html">Job Details</a></li>    
                <li><a href="resume.html">Resume Page</a></li> 
                <li><a href="privacy-policy.html">Privacy Policy</a></li>
                <li><a href="pricing.html">Pricing Tables</a></li>
                <li><a href="browse-jobs.html">Browse Jobs</a></li>
                <li><a href="browse-categories.html">Browse Categories</a></li>
                <li><a href="add-resume.html">Add Resume</a></li>
                <li><a href="manage-resumes.html">Manage Resumes</a></li> 
                <li><a href="job-alerts.html">Job Alerts</a></li> 
                <li><a href="{{ url('/post-job') }}">Add Job</a></li>  
                <li><a href="manage-jobs.html">Manage Jobs</a></li>
                <li><a href="manage-applications.html">Manage Applications</a></li>
                <li><a href="browse-resumes.html">Browse Resumes</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="faq.html">Faq</a></li>
                <li><a href="my-account.html">Login</a></li>
              </ul><!--- End Menu -->
          </div> <!--- End Off Canvas Side Menu -->
          <div class="tbtn wow pulse" id="menu" data-wow-iteration="infinite" data-wow-duration="500ms" data-toggle="offcanvas" data-target=".navmenu">
            <p><i class="ti-files"></i> All Pages</p>
          </div>
        </div>
      </div>
      <!-- Header Section End -->  

      <!-- Page Header Start -->
      <div class="page-header" style="background: url(assets/img/banner1.jpg);">
        <div class="container">
          <div class="row">         
            <div class="col-md-12">
              <div class="breadcrumb-wrapper">
                <h2 class="product-title">Privacy Policy</h2>
                <ol class="breadcrumb">
                  <li><a href="#"><i class="ti-home"></i> Home</a></li>
                  <li class="current">Privacy Policy</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Page Header End -->        

      <!-- Main container Start -->  
      <div class="policy section">
        <div class="container">
          <div class="row">          
            <div class="post-box">
              <div class="text-box">
                <h2>Privacy Policy</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Suscipit perspiciatis quam at laboriosam labore sunt voluptas deleniti similique voluptates atque commodi, veritatis aut, doloribus minima repudiandae tenetur quasi, accusamus unde!</p>
                <h4>Personal Identification Information</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Excepturi inventore, ipsa enim facilis possimus aliquid aperiam laudantium voluptatum, maxime iure, officia molestiae optio repellat eaque sunt nihil sit. Beatae, dolorum. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ducimus placeat nesciunt quidem, quod ea! Saepe, mollitia. Alias repellendus pariatur, placeat magni voluptatibus necessitatibus? Fugit, magnam, commodi voluptate deleniti est quibusdam!</p>
                <h4>Non-Personal Identification Information</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Excepturi inventore, ipsa enim facilis possimus aliquid aperiam laudantium voluptatum, maxime iure, officia molestiae optio repellat eaque sunt nihil sit. Beatae, dolorum. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ducimus placeat nesciunt quidem, quod ea! Saepe, mollitia. Alias repellendus pariatur, placeat magni voluptatibus necessitatibus? Fugit, magnam, commodi voluptate deleniti est quibusdam!</p>
                <h4>Web Browser Cookies</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Excepturi inventore, ipsa enim facilis possimus aliquid aperiam laudantium voluptatum, maxime iure, officia molestiae optio repellat eaque sunt nihil sit. Beatae, dolorum. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ducimus placeat nesciunt quidem, quod ea! Saepe, mollitia. Alias repellendus pariatur, placeat magni voluptatibus necessitatibus? Fugit, magnam, commodi voluptate deleniti est quibusdam!</p>
                <h4>How we use collected information</h4>
                <span>Jobify may collect and use Users personal information for the following purposes:</span>
                <ul>
                <li>To improve customer service</li>
                <li>Information you provide helps us respond to your customer service requests and support needs more efficiently.</li>
                <li>To personalize user experience</li>
                <li>We may use information in the aggregate to understand how our Users as a group use the services and resources provided on our Site.</li>
                <li>To process payments</li>
                <li>We may use the information Users provide about themselves when placing an order only to provide service to that order. We do not share this information with outside parties except to the extent necessary to provide the service.</li>
                <li>To run a promotion, contest, survey or other Site feature</li>
                <li>To send Users information they agreed to receive about topics we think will be of interest to them.</li>
                <li>To send periodic emails</li>
                <li>We may use the email address to send them information and updates pertaining to their order. If User decides to opt-in to our mailing list, they will receive emails that may include company news, updates, related product or service information, etc. If at any time the User would like to unsubscribe from receiving future emails, we include detailed unsubscribe instructions at the bottom of each email.</li>
                </ul>
                <h4>How we protect your information</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus in nobis, atque cum incidunt accusamus. Itaque aperiam, modi voluptates ipsam ratione minus, illo hic facere atque pariatur debitis consectetur quis.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Minus ipsum minima suscipit ea nam, accusantium dignissimos sapiente quas rem, iusto, voluptate mollitia cupiditate quo laudantium quibusdam! Expedita distinctio, provident. Sapiente. accusantium dignissimos sapiente quas rem, iusto, voluptate mollitia cupiditate quo laudantium quibusdam!</p>
                <h4>Sharing your personal information</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officiis consectetur nihil natus, rem modi cum. Recusandae eligendi hic repudiandae voluptate ipsum, non repellat quasi. Delectus, nam, quam. Officia, at explicabo?</p>
                <h4>Third Party Websites</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reprehenderit molestias officia placeat repellendus, nisi accusantium voluptates distinctio ea qui voluptatum quos porro suscipit amet, unde, maiores saepe, vitae similique earum. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cum pariatur magni ipsam illo amet, temporibus, molestias. Asperiores voluptatum tempora expedita hic rem, eveniet facere cumque reiciendis vel, laboriosam, placeat tenetur. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eveniet quasi molestias, ducimus neque placeat facilis! Maiores, dicta mollitia consequuntur neque recusandae culpa aperiam asperiores voluptatibus laborum unde reprehenderit dolore perferendis.</p>
                <h4>Google Adsense</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Corporis ut ea laboriosam, consectetur earum aperiam, voluptatibus officiis veritatis modi enim est reprehenderit nihil, omnis magnam. Blanditiis repellat necessitatibus, amet mollitia! privacy policy at http://www.google.com/privacy_ads.html</p>
                <h4>Changes to this privacy policy</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium quod porro provident cupiditate eligendi neque, dolores nostrum optio eius. Repellendus aliquid nemo consequatur maxime facere possimus animi fugit ad laudantium.</p>
                <h4>Your acceptance of these terms</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sit cupiditate, tenetur quaerat natus voluptatum nobis incidunt, architecto officiis omnis reiciendis eveniet, impedit fugiat. Ullam, possimus, eum. Rem enim vero quo.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Main container End -->  

      <!-- Footer Section Start -->
      <footer>
        <!-- Footer Area Start -->
        <section class="footer-Content">
          <div class="container">
            <div class="row">
              <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="widget">
                  <h3 class="block-title"><img src="assets/img/logo.png" class="img-responsive" alt="Footer Logo"></h3>
                  <div class="textwidget">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque lobortis tincidunt est, et euismod purus suscipit quis. Etiam euismod ornare elementum. Sed ex est, consectetur eget facilisis sed, auctor ut purus.</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="widget">
                  <h3 class="block-title">Quick Links</h3>
                  <ul class="menu">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Support</a></li>
                    <li><a href="#">License</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                    <li><a href="#">Contact</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="widget">
                  <h3 class="block-title">Trending Jobs</h3>
                  <ul class="menu">
                    <li><a href="#">Android Developer</a></li>
                    <li><a href="#">Senior Teamleader</a></li>
                    <li><a href="#">iOS Developer</a></li>
                    <li><a href="#">Junior Tester</a></li>
                    <li><a href="#">Full Stack Developer</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="widget">
                  <h3 class="block-title">Follow Us</h3>
                  <div class="bottom-social-icons social-icon">  
                    <a class="twitter" href="https://twitter.com/GrayGrids"><i class="ti-twitter-alt"></i></a>
                    <a class="facebook" href="https://web.facebook.com/GrayGrids"><i class="ti-facebook"></i></a>                   
                    <a class="youtube" href="https://youtube.com/"><i class="ti-youtube"></i></a>
                    <a class="dribble" href="https://dribbble.com/"><i class="ti-dribbble"></i></a>
                    <a class="linkedin" href="https://www.linkedin.com/"><i class="ti-linkedin"></i></a>
                  </div>  
                  <p>Join our mailing list to stay up to date and get notices about our new releases!</p>
                  <form class="subscribe-box">
                    <input type="text" placeholder="Your email">
                    <input type="submit" class="btn-system" value="Send">
                  </form> 
                </div>
              </div>
            </div>
          </div>
        </section>
        <!-- Footer area End -->
        
        <!-- Copyright Start  -->
        <div id="copyright">
          <div class="container">
            <div class="row">
              <div class="col-md-12">
                <p>All Rights reserved &copy; 2017 - Designed & Developed by <a rel="nofollow" href="http://graygrids.com/">GrayGrids</a></p>   
              </div>
            </div>
          </div>
        </div>
        <!-- Copyright End -->

      </footer>
      <!-- Footer Section End -->  
      
      <!-- Go To Top Link -->
      <a href="#" class="back-to-top">
        <i class="ti-arrow-up"></i>
      </a>
        
      <div id="loading">
        <div id="loading-center">
          <div id="loading-center-absolute">
            <div class="object" id="object_one"></div>
            <div class="object" id="object_two"></div>
            <div class="object" id="object_three"></div>
            <div class="object" id="object_four"></div>
            <div class="object" id="object_five"></div>
            <div class="object" id="object_six"></div>
            <div class="object" id="object_seven"></div>
            <div class="object" id="object_eight"></div>
          </div>
        </div>
      </div>
        
    <!-- Main JS  -->
    <script type="text/javascript" src="assets/js/jquery-min.js"></script>      
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>    
    <script type="text/javascript" src="assets/js/material.min.js"></script>
    <script type="text/javascript" src="assets/js/material-kit.js"></script>
    <script type="text/javascript" src="assets/js/color-switcher.js"></script>
    <script type="text/javascript" src="assets/js/jquery.parallax.js"></script>
    <script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.slicknav.js"></script>
    <script type="text/javascript" src="assets/js/main.js"></script>
    <script type="text/javascript" src="assets/js/jquery.counterup.min.js"></script>
    <script type="text/javascript" src="assets/js/waypoints.min.js"></script>
    <script type="text/javascript" src="assets/js/jasny-bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="assets/js/form-validator.min.js"></script>
    <script type="text/javascript" src="assets/js/contact-form-script.js"></script>    
    <script type="text/javascript" src="assets/js/jquery.themepunch.revolution.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.themepunch.tools.min.js"></script>
      
  </body>

<!-- Mirrored from demo.graygrids.com/themes/jobboard/privacy-policy.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 08 Jul 2026 09:23:35 GMT -->
</html>
 @endsection