<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Make a request to the API endpoint
$host_url = home_url();
$response = wp_remote_get('https://www.juicer.io/api/hosts?hostname='. $host_url);

$feedExists = false;

// Check if the request was successful
if (!is_wp_error($response)) {
  
    // Get the response code
    $response_code = wp_remote_retrieve_response_code($response);
    
    // Check if the response code is no 404 (Not Found)
    if ($response_code !== 404) {
        
      $body = wp_remote_retrieve_body($response);
      $data = json_decode($body, true);
      
      $uniqueFeeds = [];

      // Check if "feed_id" exists in the response
      foreach ($data as $item) {
        if (isset($item['feed_id'])) {
            $feedExists = true;
            break;
        }
      }
      foreach ($data as $item) {
          $feedId = $item['feed_id'];
          $feedSlug = $item['feed_slug'];
          
          if (!array_key_exists($feedId, $uniqueFeeds)) {
              $uniqueFeeds[$feedId] = $feedSlug;
          }
      }
  }
}
?>

<div class="juicer-header">
  <img src="<?php echo plugin_dir_url( __FILE__ ) ?>/img/juicer-logo.svg" width="116" >
  <div>
    <a href="https://www.juicer.io/?utm_source=wordpress_plugin_instructions&utm_medium=referral&utm_content=cta_website" target="_blank" class="juicer-header__link">Juicer Website <img class="juicer-widget__icon-link" src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-outbound-link-icon.svg" height="16" width="16" ></a>
    <a href="https://www.juicer.io/dashboard?utm_source=wordpress_plugin_instructions&utm_medium=referral&utm_content=cta_dashboard" target="_blank" class="juicer-header__link">Your Juicer Dashboard <img class="juicer-widget__icon-link" src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-outbound-link-icon.svg" height="16" width="16" ></a>
  </div>
</div>
<div class="juicer-container">
  <div class="juicer-column-2-3">
    
    <?php if (is_wp_error($response)) : ?>
    <div class="juicer-widget blue">
      <div class="juicer-info-block">
        <img src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-info-blue.svg" height="24" width="24" >
        <span>We can't confirm if your feed is setup correctly due to an API error</span>
      </div>
    </div>
    <?php elseif ($feedExists) : ?>
      <div class="juicer-widget green">
        <div class="juicer-info-block">
          <img src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-checkbox-green.svg" height="24" width="24" >
          <span>Good Job! The feed on this website is set up correctly.</span>
          <div class="juicer-info-block__feed-list">
            <?php
                $count = count($uniqueFeeds);
                
                if ($count > 1) {
                    echo "Your active feeds: ";
                } else {
                    echo "Your active feed: ";
                }
                
                $index = 0;
                
                foreach ($uniqueFeeds as $feedSlug) {
                    echo "<strong>{$feedSlug}</strong>";
                    $index++;
                
                    if ($index !== $count) {
                        echo ", ";
                    }
                }
              ?>
          </div>
        </div>
      </div>
    <?php elseif (isset($_COOKIE['juicer_welcome'])) : ?>
        <div class="juicer-widget blue">
          <div class="juicer-info-block">
            <img src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wave-emoji.svg" height="32" width="32" >
            <span>Welcome to Juicer! Follow the instructions below to set up your first feed.</span>
            <script>document.cookie = 'juicer_welcome=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';</script>
          </div>
        </div>
    <?php else: ?>
      <div class="juicer-widget red">
        <div class="juicer-info-block">
          <img src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-info-red.svg" height="24" width="24" >
          <span>The feed may not be set up on your website, or we can't verify its correct display.</span>
        </div>
      </div>
    <?php endif; ?>
  
    <div class="juicer-widget">
      <h2>How to set up a Juicer feed on your website?</h2>
      
      <div class="juicer-img-holder">
        <img src="<?php echo plugin_dir_url( __FILE__ ) ?>img/feed-img.png">
      </div>
      
      <div class="juicer-social-networks-holder">
        <img src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-social-networks.svg">
        <span>15+ Social networks supported</span>
      </div>
      
      <ol class="juicer-steps-list">
        <li>Sign up at <a href="https://www.juicer.io/?utm_source=wordpress_plugin_instructions&utm_medium=referral&utm_content=cta_bullet" target="_blank">Juicer.io</a> and create a free feed. It only takes 1 minute.</li>
        <li>Navigate to your Juicer dashboard, where you can see your latest feeds.</li>
        <li>Copy your feed name from the URL in the address bar of your browser.</li>
        <li>Embed your social media feed by pasting shortcode with your feed name.</li>
      </ol>
      <div class="juicer-code-wrapper" style="margin-left: 25px;">
        <pre>[juicer name='FEED_NAME']</pre>
        <button class="juicer-code-copy"><img src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-copy-icon.svg"></button>
        <span class="code-copy-tooltip">Copied</span>
      </div>
    </div>
    
    <div class="juicer-widget">
      <p>If you prefer, you can also use the following PHP snippet to add the feed to your template.</p>
      <div class="juicer-code-wrapper">
        <pre>&lt;?php juicer_feed('name=juicer'); ?&gt;</pre>
        <button class="juicer-code-copy"><img src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-copy-icon.svg"></button>
        <span class="code-copy-tooltip">Copied</span>
      </div>
    </div>
  </div>
  
  <div class="juicer-column-1-3">
    <a href="#" class="juicer-widget link juicer-review-link">
      <div class="juicer-widget__title">
        <img class="juicer-widget__icon" src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-rating-icon.svg" height="24" width="24" >
        <h2>Show Your Love</h2>
        <img class="juicer-widget__icon-link" src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-outbound-link-icon.svg" height="16" width="16" >
      </div>
      <p>Take 2 minutes to review the plugin. Spread the love to encourage us to keep getting better.</p>
    </a>
    
    <a href="https://help.juicer.io/hc/en-us/sections/360008042291-Getting-Started-with-Juicer?utm_source=wordpress_plugin_instructions&utm_medium=referral&utm_content=cta_documentation" target="_blank" class="juicer-widget link">
      <div class="juicer-widget__title">
        <img class="juicer-widget__icon" src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-docs-icon.svg" height="24" width="24" >
        <h2>View Documentation</h2>
        <img class="juicer-widget__icon-link" src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-outbound-link-icon.svg" height="16" width="16" >
      </div>
      <p>For advanced usage and customization options, refer to the documentation for the Juicer WordPress Plugin.</p>
    </a>
  
    <a href="https://www.juicer.io/contact?utm_source=wordpress_plugin_instructions&utm_medium=referral&utm_content=cta_help" target="_blank" class="juicer-widget link">
      <div class="juicer-widget__title">
        <img class="juicer-widget__icon" src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-support-icon.svg" height="24" width="24" >
        <h2>Need Help?</h2>
        <img class="juicer-widget__icon-link" src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-outbound-link-icon.svg" height="16" width="16" >
      </div>
      <p>Stuck with something?<br />Get help by submitting a support ticket.</p>
    </a>
  </div>
</div>

<!-- Modal -->
<div id="juicer-modal" class="juicer-modal">
  <div class="juicer-modal-content">
    <span class="juicer-close">&times;</span>
    <p>We would love to hear your feedback. It won't take more than a minute.</p>
    <h2>Enjoying the Juicer plugin?</h2>
    <div class="juicer-modal-link-wrapper">
      <a href="https://wordpress.org/plugins/juicer/#reviews" target="_blank" class="juicer-modal-link">
        <img src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-heart-icon.svg" width="40" >
        <strong>Yes</strong>
        <p>(leave a review)</p>
      </a>
      <a href="https://help.juicer.io/hc/en-us/requests/new" target="_blank" class="juicer-modal-link">
        <img src="<?php echo plugin_dir_url( __FILE__ ) ?>img/wp-feedback-icon.svg" width="40" >
        <strong>No</strong>
        <p>(share your feedback)</p>
      </a>
    </div>
  </div>
</div>