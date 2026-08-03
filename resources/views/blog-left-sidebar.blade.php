@extends('layouts.app')



@section('content')  


<html lang="en">
  
<!-- Mirrored from demo.graygrids.com/themes/jobboard/blog-left-sidebar.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 08 Jul 2026 09:23:44 GMT -->
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
                    <a href="about.html">
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
                        <a href="privacy-policy.html">
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
                    <a class="active" href="/blog">
                    Blog <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown">
                      <li>
                        <a href="/blog">
                        Blog - Right Sidebar
                        </a>
                      </li>
                      <li>
                        <a class="active" href="blog-left-sidebar.html">
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
                <a href="about.html">Pages</a>
                <ul>
                  <li><a href="about.html">About</a></li>
                  <li><a href="job-page.html">Job Page</a></li>
                  <li><a href="job-details.html">Job Details</a></li>
                  <li><a href="resume.html">Resume Page</a></li>
                  <li><a href="privacy-policy.html">Privacy Policy</a></li>
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
                <a class="active" href="/blog">Blog</a>
                <ul class="dropdown">
                  <li><a href="/blog">Blog - Right Sidebar</a></li>
                  <li><a class="active" href="blog-left-sidebar.html">Blog - Left Sidebar</a></li>
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
      <!-- Header Section End -->  

      <!-- Page Header Start -->
      <div class="page-header" style="background: url(assets/img/banner1.jpg);">
        <div class="container">
          <div class="row">         
            <div class="col-md-12">
              <div class="breadcrumb-wrapper">
                <h2 class="product-title">Blog</h2>
                <ol class="breadcrumb">
                  <li><a href="#"><i class="ti-home"></i> Home</a></li>
                  <li class="current">Blog</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Page Header End -->    

       <!-- Start Content -->
      <div id="content">
        <div class="container">
          <div class="row">          

            <!--Sidebar-->
            <aside id="sidebar" class="col-md-3 right-sidebar">
              <!-- Search Widget -->
              <div class="widget widget-search">
                <h5 class="widget-title">Search This Site</h5>
                <form action="#">
                  <input class="form-control search" type="search" placeholder="Enter your keyword" />
                  <button class="search-btn" type="submit"><i class="fa fa-search"></i></button>
                </form>
              </div>

              <!-- Categories Widget -->
              <div class="widget widget-categories">
                <h5 class="widget-title">Categories</h5>
                <ul class="cat-list">
                  <li>
                    <a href="#">Announcement <span class="num-posts">(4)</span></a>                    
                  </li>
                  <li>
                    <a href="#">Indeed Events <span class="num-posts">(2)</span></a>                    
                  </li>
                  <li>
                    <a href="#">Tips & Tricks <span class="num-posts">(3)</span></a>                    
                  </li>
                  <li>
                    <a href="#">Experiences <span class="num-posts">(5)</span></a>                    
                  </li>
                  <li>
                    <a href="#">Case Studies <span class="num-posts">(6)</span></a>                    
                  </li>
                  <li>
                    <a href="#">Labor Market News <span class="num-posts">(9)</span></a>                    
                  </li>
                  <li>
                    <a href="#">HR Best Practices <span class="num-posts">(17)</span></a>                   
                  </li>
                </ul>
              </div>

              <!-- Popular Posts widget -->
              <div class="widget widget-popular-posts">
              <h5 class="widget-title">Recent Post</h5>
                <ul class="posts-list">
                  <li>
                    <div class="widget-content">
                      <a href="#">Tips to write an impressive resume online for beginner</a>
                      <span><i class="icon-calendar"></i> 25 March, 2018</span>
                    </div>
                    <div class="clearfix"></div>
                  </li>
                  <li>
                    <div class="widget-content">
                      <a href="#">The sceret to pitching for new business</a>
                      <span><i class="icon-calendar"></i> 25 March, 2018</span>
                    </div>
                    <div class="clearfix"></div>
                  </li>
                  <li>
                    <div class="widget-content">
                      <a href="#">7 things you should never say to your boss</a>
                      <span><i class="icon-calendar"></i> 25 March, 2018</span>
                    </div>
                    <div class="clearfix"></div>
                  </li>
                </ul>
              </div>

              <!-- Tag Media -->
              <div class="widget tag">
                <h5 class="widget-title">Tags</h5>
                <a href="#"> Jobpress</a>
                <a href="#"> Recruiter</a>
                <a href="#"> Interview</a> 
                <a href="#"> Employee</a>                                 
                <a href="#"> Labor</a>
                <a href="#"> HR</a>
                <a href="#"> Salary</a>
                <a href="#"> Employer</a>
              </div> 

              <!-- Archives Widget -->
              <div class="widget widget-archives">
                <h5 class="widget-title">Archives</h5>
                <ul class="cat-list">
                  <li>
                    <a href="#">October 2016 <span class="num-posts">(29)</span></a>                    
                  </li>
                  <li>
                    <a href="#">September 2016 <span class="num-posts">(34)</span></a>                    
                  </li>
                  <li>
                    <a href="#">August 2016 <span class="num-posts">(23)</span></a>                    
                  </li>
                  <li>
                    <a href="#">July 2016 <span class="num-posts">(38)</span></a>                    
                  </li>
                  <li>
                    <a href="#">June 2016 <span class="num-posts">(16)</span></a>                    
                  </li>
                  <li>
                    <a href="#">May 2016 <span class="num-posts">(14)</span></a>                    
                  </li>
                  <li>
                    <a href="#">April 2016 <span class="num-posts">(17)</span></a>                   
                  </li>
                </ul>
              </div>

            </aside>
            <!--End sidebar-->

            <!-- Start Blog Posts -->
            <div class="col-md-9">             

              <!-- Start Post -->
              <div class="blog-post">
                <!-- Post thumb -->
                <div class="post-thumb">
                  <a href="#"><img src="assets/img/blog/blog1.jpg" alt=""></a>
                  <div class="hover-wrap">
                  </div>
                </div>
                <!-- End Post post-thumb -->
                
                <!-- Post author -->
                <div class="blog-author">
                  <img src="assets/img/blog/author.jpg" alt="">
                </div>
                <!-- Post End -->

                <!-- Post Content -->
                <div class="post-content">                     
                  <h3 class="post-title"><a href="#">Let's explore 5 cool new features in JobBoard theme</a></h3>
                  <div class="meta">                    
                    <span class="meta-part"><a href="#"><i class="ti-user"></i> By Admin</a></span>
                    <span class="meta-part"><i class="ti-calendar"></i><a href="#"> 20.02.2018</a></span>
                    <span class="meta-part"><a href="#"><i class="ti-comment-alt"></i> 5Comments</a></span>                    
                  </div>
                  <p>Fusce fermentum ipsum mauris, rutrum ultrices ligula sodales et. Maecenas pellentesque aliquet arcu, vel elementum magna facilisis vitae. Nam in cursus lorem. Donec ac tellus nisl. Sed volutpat quis orci nec placerat. Fusec ex magna, congue sed vulpu-tate rhoncus, laoreet nec sapien.</p>
                  <a href="#" class="btn btn-common">Read More</a>
                </div>
                <!-- Post Content -->

              </div>
              <!-- End Post -->

              <!-- Start Post -->
              <div class="blog-post">
                <!-- Post thumb -->
                <div class="post-thumb">
                  <a href="#"><img src="assets/img/blog/blog2.jpg" alt=""></a>
                  <div class="hover-wrap">
                  </div>
                </div>
                <!-- End Post post-thumb -->
                
                <!-- Post author -->
                <div class="blog-author">
                  <img src="assets/img/blog/author.jpg" alt="">
                </div>
                <!-- Post End -->

                <!-- Post Content -->
                <div class="post-content">                     
                  <h3 class="post-title"><a href="#">Employer branding: Get behind the wheel!</a></h3>
                  <div class="meta">                    
                    <span class="meta-part"><a href="#"><i class="ti-user"></i> By Admin</a></span>
                    <span class="meta-part"><i class="ti-calendar"></i><a href="#"> 20.02.2018</a></span>
                    <span class="meta-part"><a href="#"><i class="ti-comment-alt"></i> 5Comments</a></span>                    
                  </div>
                  <p>Fusce fermentum ipsum mauris, rutrum ultrices ligula sodales et. Maecenas pellentesque aliquet arcu, vel elementum magna facilisis vitae. Nam in cursus lorem. Donec ac tellus nisl. Sed volutpat quis orci nec placerat. Fusec ex magna, congue sed vulpu-tate rhoncus, laoreet nec sapien.</p>
                  <a href="#" class="btn btn-common">Read More</a>
                </div>
                <!-- Post Content -->

              </div>
              <!-- End Post -->
             
              <!-- Start Post -->
              <div class="blog-post">
                <!-- Post thumb -->
                <div class="post-thumb">
                  <a href="#"><img src="assets/img/blog/blog3.jpg" alt=""></a>
                  <div class="hover-wrap">
                  </div>
                </div>
                <!-- End Post post-thumb -->
                
                <!-- Post author -->
                <div class="blog-author">
                  <img src="assets/img/blog/author.jpg" alt="">
                </div>
                <!-- Post End -->

                <!-- Post Content -->
                <div class="post-content">                     
                  <h3 class="post-title"><a href="#">How to convince recruiters and get your dream job</a></h3>
                  <div class="meta">                    
                    <span class="meta-part"><a href="#"><i class="ti-user"></i> By Admin</a></span>
                    <span class="meta-part"><i class="ti-calendar"></i><a href="#"> 20.02.2018</a></span>
                    <span class="meta-part"><a href="#"><i class="ti-comment-alt"></i> 5Comments</a></span>                    
                  </div>
                  <p>Fusce fermentum ipsum mauris, rutrum ultrices ligula sodales et. Maecenas pellentesque aliquet arcu, vel elementum magna facilisis vitae. Nam in cursus lorem. Donec ac tellus nisl. Sed volutpat quis orci nec placerat. Fusec ex magna, congue sed vulpu-tate rhoncus, laoreet nec sapien.</p>
                  <a href="#" class="btn btn-common">Read More</a>
                </div>
                <!-- Post Content -->

              </div>
              <!-- End Post -->              

              <!-- Start Pagination -->
              <ul class="pagination">              
                <li class="active"><a href="#" class="btn btn-common" ><i class="ti-angle-left"></i> prev</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li class="active"><a href="#" class="btn btn-common">Next <i class="ti-angle-right"></i></a></li>
              </ul>
              <!-- End Pagination -->
            </div>
            <!-- End Blog Posts -->

          </div>
        </div>
      </div>
      <!-- End Content -->   


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

<!-- Mirrored from demo.graygrids.com/themes/jobboard/blog-left-sidebar.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 08 Jul 2026 09:23:44 GMT -->
</html>

@endsection