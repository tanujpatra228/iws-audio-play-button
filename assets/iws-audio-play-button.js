jQuery(document).ready(function(){
    jQuery('.iws-play-btn').click(function(){
        var audio_url = jQuery(this).data('url');
        var audio_tag = `<audio controls autoplay>
            <source src="`+audio_url+`" type="audio/ogg">
                Your browser does not support the audio element.
            </audio>`;

        // jQuery(audio_tag).insertAfter(jQuery(this));
        jQuery(this).siblings('.iws-audio').html(audio_tag);
    });
});