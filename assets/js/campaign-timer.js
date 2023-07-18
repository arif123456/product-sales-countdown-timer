; (function ($) {

    /*=================================
    Campaign countdown timer
    =================================*/

        const timers = document.querySelectorAll('.wpfound_shop_page_timer');

        // Update timers
        timers.forEach(timer => {
            const date_range = $(timer).data('date-range');

                var countDownDate = new Date(date_range).getTime();
                var wpfound_timer = setInterval(function() {
                var now = new Date().getTime();
                var wpfound_distance = countDownDate - now;
                var wpfound_days = Math.floor(wpfound_distance / (1000 * 60 * 60 * 24));
                var wpfound_hours = Math.floor((wpfound_distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var wpfound_minutes = Math.floor((wpfound_distance % (1000 * 60 * 60)) / (1000 * 60));
                var wpfound_seconds = Math.floor((wpfound_distance % (1000 * 60)) / 1000);

                timer.innerHTML = 
                    `<div class="wpfound_countdown_wrap">
                        <div class="wpfound_countdown_timer">
                            <span class="wpfound_countdown-single-item day">
                                <span class="date">${wpfound_days}</span>Days
                            </span>
                            <span class="wpfound_countdown-single-item hours">
                                <span class="date">${wpfound_hours}</span>Hours
                            </span>
                            <span class="wpfound_countdown-single-item mins">
                                <span class="date">${wpfound_minutes}</span>Mins
                            </span>
                            <span class="wpfound_countdown-single-item secs">
                                <span class="date">${wpfound_seconds}</span>Secs
                            </span>
                        </div>
                    </div>`;
                
    
                // If the count down is finished, write some text
                if (wpfound_distance < 0) {
                    clearInterval(wpfound_timer);
                    timer.innerHTML = "<span class='wpfound_expire_texxt'>DATE EXPIRED</span>";
                }
            }, 1000);
        });

})( jQuery );