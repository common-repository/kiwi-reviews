<?php
wp_enqueue_script('review-script', plugins_url('/views/js/glide.js', __FILE__), ['jquery'], '', true);
wp_enqueue_style('dashicons');
wp_enqueue_style('review-style', plugins_url('/views/css/style.css', __FILE__), [], '');
wp_enqueue_style('glide-core-style', plugins_url('/views/css/glide.core.css', __FILE__), [], '');
wp_enqueue_style('glide-theme-style', plugins_url('/views/css/glide.theme.css', __FILE__), [], '');

$info = wp_remote_get('https://maps.googleapis.com/maps/api/place/details/json?key='. get_option('kiwi_reviews_api_key').'&place_id='.$place_id);
$body = wp_remote_retrieve_body($info);
$data = json_decode($body);
$reviews = $data->result->reviews;
?>

<?php

if ($reviews !== null) :
?>
<div id="wrap-sh-slider">
  <div class="kiwi-reviews-loader">
    <div class="kiwi-reviews-spinner">
      <div class="bounce1"></div>
      <div class="bounce2"></div>
      <div class="bounce3"></div>
    </div>
  </div>
    <div class="sh-slider kiwi-slider">
        <div class="glide">
            <div class="glide__track" data-glide-el="track">
                <ul class="glide__slides">
                    <?php
                    if (isset($reviews)) {
                        foreach ($reviews as $review) {  ?>
                    <li class="glide_slide">
                        <div class="review-block">
                            <div class="avatar">
                                <img src="<?php echo $review->profile_photo_url ?>" />
                            </div>
                            <div class="name"><?php echo $review->author_name ?></div>
                            <div class="star-ratings-css">
                                <div class="star-ratings-css-top" style="width: <?php
                                $val = $review->rating * 22;
                                if ($val >= 100) {
                                    echo '100';
                                } else {
                                    echo $val;
                                }
                            ?>%">
                                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                </div>
                                <div class="star-ratings-css-bottom">
                                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                </div>
                            </div>
                            <div class="review-text">
                                <p>
                                    <span class="dashicons dashicons-format-quote first-quote"></span>
                                    <?php echo $review->text ?>
                                    <span class="dashicons dashicons-format-quote last-quote"></span>
                                </p>
                            </div>
                            <div class="time-desc">
                                <p><?php echo $review->relative_time_description ?></p>
                            </div>
                        </div>
                    </li>
                    <?php }
                    }
                    ?>
                </ul>
            </div>
            <div class="glide__arrows" data-glide-el="controls">
                <button class="glide__arrow glide__arrow--left control_next" data-glide-dir="<">
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-left" role="img"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"
                        class="svg-inline--fa fa-angle-left fa-w-8 fa-5x">
                        <path fill="currentColor"
                            d="M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z"
                            class=""></path>
                    </svg>
                </button>
                <button class="glide__arrow glide__arrow--right control_prev" data-glide-dir=">">
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"
                        class="svg-inline--fa fa-angle-right fa-w-8 fa-5x">
                        <path fill="currentColor"
                            d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"
                            class=""></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
  let slides = document.getElementsByClassName('glide');
  console.log(slides);
  // var elements = document.querySelectorAll('div');
  [].forEach.call(slides, function( el ) {
    if(!el.classList.contains('glide--carousel')){
      var kiwi_slider_reviews = new Glide(el, {
        type: 'carousel',
        perView: 3,
        focusAt: 'center',
        breakpoints: {
          834: {
            perView: 1
          },
        }
      });
      kiwi_slider_reviews.destroy();

      kiwi_slider_reviews.mount();
      let loaders = document.getElementsByClassName('kiwi-reviews-loader');
      [].forEach.call(loaders, function( el ) {
        el.style.display = 'none';
      });
    }
  });

});

</script>
<?php
endif;
?>
