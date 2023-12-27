

    <!-- Start Section Join Us -->
    <section class="Join-Us">
      <div class="container">
        <div class="enwan-Join-Us">
          <h2><?php echo $this->translate("Candid career advice delivered by experienced professionals");?></h2>
          <div class="khat-Join-Us"></div>
        </div>
        <div class="box-Join-Us">
             <div class="box">
            <img src="application/modules/Customtheme/externals/images/Join-Us-4.png" alt="" />
            <p style="font-size: 23px; font-weight: 300;"><?php echo $this->translate("Hourly Consultation");?></p>
            <div class="ul-Join">
              <ul>
                    <li><?php echo $this->translate("Work one-on-one with us to establish your professional brand, gain career confidence and successfully reach your goals.");?></li> 
           
              </ul>
            </div>
            <?php if(!$this->viewer()->getIdentity()):?>
                <button onclick="window.location.href='/bookings/services/home'"><?php echo $this->translate("Browse Consultant");?></button>
            <?php else:?>
                <button onclick="window.location.href='/bookings/services/home'"><?php echo $this->translate("Browse Consultant");?></button>
            <?php endif;?>
          </div>
          <div class="box">
            <img src="application/modules/Customtheme/externals/images/Join-Us-4.png" alt="" />
            <p style="font-size: 23px; font-weight: 300;"><?php echo $this->translate("Professional Guidance");?></p>
            <div class="ul-Join">
              <ul>
                <li><?php echo $this->translate("Invest in yourself and get real world guidance from professionals established in the areas you need the most help with.");?></li>
               
                
              </ul>
            </div>
            <!--<button onclick="window.location.href='https://zoom.us/j/7568151820?pwd=MzZsWjJPSVVtSFRTLzdhNEs2WDNOQT09'"><?php echo $this->translate("More Details");?></button> -->
          </div>
          <div class="box">
            <img src="application/modules/Customtheme/externals/images/Join-Us-3.png" alt="" />
            <p style="font-size: 23px; font-weight: 300;"><?php echo $this->translate("On-Job Mentor Program");?></p>
            <div class="ul-Join">
              <ul>
                <li><?php echo $this->translate("Take control of your career. stop waiting for the moment to happen. make your moment happen.");?></li>
              </ul>
            </div>
            <button onclick="window.location.href='/pages/mentor-service'"><?php echo $this->translate("Browse Mentors");?></button>
          </div>
         
        </div>
      </div>
    </section>
    <!-- End Section Join Us -->

    
