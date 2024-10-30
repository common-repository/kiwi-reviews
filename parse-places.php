<div class='kiwi-map-container'>
    <?php
    if (isset($_POST['google-key'])) {
        if (get_option('kiwi_reviews_api_key')) {
            update_option('kiwi_reviews_api_key', sanitize_text_field($_POST['google-key']));
        } else {
            add_option('kiwi_reviews_api_key', sanitize_text_field($_POST['google-key']), '', 'yes');
        }
    }

    if (! empty($_POST['place_id'])) {
        echo '<h2 style="text-align:center">Your Shortcode : <input class="kiwi-reviews-input" type="text" value="[kiwi-review place-id='.esc_html($_POST['place_id']).']" /><button class="kiwi-reviews-btn" onclick="copy()" data-clipboard-text="[kiwi-review place-id='.$_POST['place_id'].']"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="copy" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-copy fa-w-14 fa-2x"><path fill="currentColor" d="M320 448v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24V120c0-13.255 10.745-24 24-24h72v296c0 30.879 25.121 56 56 56h168zm0-344V0H152c-13.255 0-24 10.745-24 24v368c0 13.255 10.745 24 24 24h272c13.255 0 24-10.745 24-24V128H344c-13.2 0-24-10.8-24-24zm120.971-31.029L375.029 7.029A24 24 0 0 0 358.059 0H352v96h96v-6.059a24 24 0 0 0-7.029-16.97z" class=""></path></svg></button></h2><hr />';
    }

    wp_enqueue_script('places-script', plugins_url('/views/js/query.js', __FILE__), ['jquery'], '', true);
    wp_enqueue_script('google-maps-places-script', 'https://maps.googleapis.com/maps/api/js?key='. get_option('kiwi_reviews_api_key') .'&sensor=false&libraries=places&callback=initialize', ['jquery'], '', true);
    wp_enqueue_script('clipboard-js', plugins_url('/views/js/clipboard.js', __FILE__), '', true);
    wp_enqueue_style('review-style', plugins_url('/views/css/style.css', __FILE__), [], '');

    if (empty(get_option('kiwi_reviews_api_key')) || isset($_GET['kiwi-reviews-change-key'])):
        ?>
        <?php
    if (!isset($_GET['kiwi-reviews-change-key'])):
        ?>
        <div style="text-align:center">
            <h1>Welcome!</h1>
            <h2>Please insert your Google Maps Api Key to proceed</h2>
            <h3><a href="https://developers.google.com/maps/documentation/javascript/get-api-key">Here's a detailed guide on how to get an API key</a></h3>
        </div>
    <hr />
    <?php
    else:
        ?>
        <h1 style="text-align:center">Change API key</h1>
        <h2 style="text-align:center">Please insert your Google Maps Api Key to change</h2>
    <hr />
    <?php
    endif;
    ?>
        <form autocomplete="off" action="" method="POST">
            <?php
            if (isset($_GET['kiwi-reviews-change-key'])):
                ?>
                <h4>Your current API Key : <?php echo get_option('kiwi_reviews_api_key') ?></h4>
                <a href="?page=kiwi-reviews">Go back</a>
            <?php
            endif;
            ?>
            <input style="margin:10px auto; display:block; width:60%;" type="text" name="google-key" onkeyup="validateApiKey(this)">
            <button id="submit-btn" class="kiwi-confirm-btn" disabled>Submit</button>
        </form>
        <script>
            function validateApiKey(e){
                if(e.value.replace(/ /g,'').length > 0){
                    let valid = true;
                    document.getElementById('submit-btn').removeAttribute("disabled");
                }else{
                    let valid = false
                    document.getElementById('submit-btn').setAttribute("disabled", "true");
                }
            }
        </script>
    <?php
    endif;

    if (!isset($_GET['kiwi-reviews-change-key'])):
    if (!empty(get_option('kiwi_reviews_api_key'))):
    if (!empty($_POST['place_id'])) {
        $query = sanitize_text_field($_POST['place_id']);
        $info = wp_remote_get('https://maps.googleapis.com/maps/api/place/details/json?key='. get_option('kiwi_reviews_api_key').'&fields=reviews&place_id='.$query);
        add_shortcode('kiwi-review', 'kiwi_reviews', sanitize_text_field($_POST['place_id']));
        do_shortcode('[kiwi-review place-id='.sanitize_text_field($_POST['place_id']).']');
    } else {
        $query = '';
    }
    ?>

    <form autocomplete="off" action="" method="POST">
        <a href="?page=kiwi-reviews&kiwi-reviews-change-key=true">Change API key</a>
        <input id="searchTextField" class="kiwi-map-search" type="text" name="query">
        <input id="kiwi-place-id" name="place_id" type="hidden">
        <div id="map"></div>
        <button id="submit-btn" class="kiwi-confirm-btn" disabled>Submit</button>
    </form>
</div>

<script>
    var map;
    var markers = [];

    function copy(){
        var clipboard = new ClipboardJS('.kiwi-reviews-btn');

        clipboard.on('success', function(e) {
            console.info('Action:', e.action);
            console.info('Text:', e.text);
            console.info('Trigger:', e.trigger);

            e.clearSelection();
        });

        clipboard.on('error', function(e) {
            console.error('Action:', e.action);
            console.error('Trigger:', e.trigger);
        });
    }

    function initialize() {

        var input = document.getElementById('searchTextField');
        autocomplete = new google.maps.places.Autocomplete(input);

        map = new google.maps.Map(document.getElementById('map'), {
            center: {
                lat: -34.397,
                lng: 150.644
            },
            zoom: 8
        });


        var service = new google.maps.places.PlacesService(map);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {

            var request = {
                location: map.getCenter(),
                radius: '500',
                query: document.getElementById('searchTextField').value
            };
            service.textSearch(request, callback);
        });
    }

    function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(map);
        }
    }


    function clearMarkers() {
        setMapOnAll(null);
    }

    function deleteMarkers() {
        clearMarkers();
        markers = [];
    }

    function callback(results, status) {
        deleteMarkers();
        if (status == google.maps.places.PlacesServiceStatus.OK) {
            var valid = true;
            var marker = new google.maps.Marker({
                map: map,
                place: {
                    placeId: results[0].place_id,
                    location: results[0].geometry.location
                }
            });
            markers.push(marker);
            map.setCenter(results[0].geometry.location);
            document.getElementById('kiwi-place-id').value = results[0].place_id;
        } else {
            var valid = false;
        }
        if (!valid) {
            document.getElementById('submit-btn').setAttribute("disabled", "true");
        } else {
            document.getElementById('submit-btn').removeAttribute("disabled");
            document.getElementById('submit-btn').focus();
        }
    }


</script>

<?php
endif;
endif;
?>
