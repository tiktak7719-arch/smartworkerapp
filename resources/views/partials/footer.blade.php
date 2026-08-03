<!-- Footer Section Start -->
    <footer>
    	<!-- Footer Area Start -->
    	<section class="footer-Content">
    		<div class="container">
    			<div class="row">
    				<div class="col-md-3 col-sm-6 col-xs-12">
              <div class="widget">
                <h3 class="block-title"><img src="{{ asset('assets/img/logo.png') }}" class="img-responsive" alt="SmartWorker"></h3>
                <div class="textwidget">
                  <p>Find the right opportunity or connect with qualified candidates on SmartWorker.</p>
                </div>
              </div>
            </div>
    				<div class="col-md-3 col-sm-6 col-xs-12">
    					<div class="widget">
                
    						<h3 class="block-title">Quick Links</h3>
  							<ul class="menu">
                  <li><a href="{{ url('/about') }}">About Us</a></li>
                  <li><a href="{{ url('/faq') }}">Support</a></li>
                  <li><a href="{{ url('/pricing') }}">Pricing</a></li>
                  <li><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                  <li><a href="{{ route('terms') }}">Terms of Use</a></li>
                  <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
    					</div>
    				</div>
    				<div class="col-md-3 col-sm-6 col-xs-12">
    					<div class="widget">
                <h3 class="block-title">Trending Jobs</h3>
                <ul class="menu">
                  <li><a href="{{ route('jobs.index') }}">Browse All Jobs</a></li>
                  <li><a href="{{ route('jobs.index', ['job_type' => 'remote']) }}">Remote Jobs</a></li>
                  <li><a href="{{ route('jobs.categories') }}">Browse Categories</a></li>
                  @if (auth()->user()?->isCandidate())
                    <li><a href="{{ route('job-alerts') }}">Job Alerts</a></li>
                  @endif
                  @if (auth()->user()?->isEmployer())
                    <li><a href="{{ route('post-job') }}">Post a Job</a></li>
                  @elseif (auth()->guest())
                    <li><a href="{{ route('my-account', ['form' => 'register']) }}">Create an Account</a></li>
                  @endif
                </ul>
              </div>
    				</div>
    				<div class="col-md-3 col-sm-6 col-xs-12">
    					<div class="widget">
                <h3 class="block-title">Follow Us</h3>
                <div class="bottom-social-icons social-icon">  
                  <a class="twitter" href="#project-welcome" data-project-social aria-label="Twitter account coming soon"><i class="ti-twitter-alt"></i></a>
                  <a class="facebook" href="https://www.facebook.com/profile.php?id=100016148715801" target="_blank" rel="noopener noreferrer" aria-label="Shaheen Shah on Facebook"><i class="ti-facebook"></i></a>
                  <a class="youtube" href="#project-welcome" data-project-social aria-label="YouTube account coming soon"><i class="ti-youtube"></i></a>
                  <a class="dribble" href="#project-welcome" data-project-social aria-label="Dribbble account coming soon"><i class="ti-dribbble"></i></a>
                  <a class="linkedin" href="#project-welcome" data-project-social aria-label="LinkedIn account coming soon"><i class="ti-linkedin"></i></a>
                </div>	
                <p>Need help with your account or a job listing?</p>
                <a href="{{ route('contact') }}" class="btn btn-common btn-sm">Contact Support</a>
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
              <div class="site-info text-center">
                <p>&copy; {{ date('Y') }} SmartWorker. All rights reserved.</p>
              </div>   
    				</div>
    			</div>
    		</div>
    	</div>
    	<!-- Copyright End -->

    </footer>
    <!-- Footer Section End -->  

@push('scripts')
<script>
  document.querySelectorAll('[data-project-social]').forEach(function (link) {
    link.addEventListener('click', function (event) {
      event.preventDefault();
      window.alert('Welcome to the project. I am Shaheen Shah. I will add this account link later.');
    });
  });
</script>
@endpush
