import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


            
jQuery(window).on('load', function() {
    NProgress.start();
    NProgress.done(true);
});


jQuery(window).on('beforeunload', function() {    
    NProgress.start();
    NProgress.done();
});