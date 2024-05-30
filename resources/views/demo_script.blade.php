<?php if (!env('BC_DEMO_SCRIPT')) return; ?>
<!-- Google Tag Manager -->
<script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-W7B5HJJ');
</script>
<!-- End Google Tag Manager -->

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W7B5HJJ" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            xfbml: true,
            version: 'v3.3'
        });
    };
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>

<script>
    // Your web app's Firebase configuration
    const firebaseConfig = {
        apiKey: "AIzaSyDi1_zTLm85BgzpzpWc6vCz-VYPHWnjza8",
        authDomain: "kytrademarks-8bd71.firebaseapp.com",
        projectId: "kytrademarks-8bd71",
        storageBucket: "kytrademarks-8bd71.appspot.com",
        messagingSenderId: "124458226407",
        appId: "1:124458226407:web:f64378cd891714d4054842",
        measurementId: "G-X13N9JRZZD"
    };
    // Initialize Firebase
    // firebase.initializeApp(firebaseConfig);
    const app = initializeApp(firebaseConfig);
    const analytics = getAnalytics(app);

    const messaging = firebase.messaging();

    function initFirebaseMessagingRegistration() {
        messaging.requestPermission().then(function() {
            return messaging.getToken()
        }).then(function(token) {

            axios.post("{{ route('fcmToken') }}", {
                _method: "PATCH",
                token
            }).then(({
                data
            }) => {
                console.log(data)
            }).catch(({
                response: {
                    data
                }
            }) => {
                console.error(data)
            })

        }).catch(function(err) {
            console.log(`Token Error :: ${err}`);
        });
    }

    initFirebaseMessagingRegistration();

    messaging.onMessage(function({
        data: {
            body,
            title
        }
    }) {
        new Notification(title, {
            body
        });
    });
</script>
<!-- Load Facebook SDK for JavaScript -->

<!-- Your customer chat code -->
<div class="fb-customerchat" attribution=setup_tool page_id="2280007165584589">
</div>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-115740936-4"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-115740936-4');
</script>
<style>
    .bravo-buy-button {
        text-align: center;
        position: fixed;
        bottom: 20px;
        left: 20px;
        background-color: #0072bc;
        color: #fff;
        text-transform: uppercase;
        font-size: 12px;
        padding: 10px 20px 8px;
        border-radius: 5px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        font-weight: 600;
        z-index: 99;
    }

    .bravo-buy-button:hover {
        text-decoration: none;
        color: #fff;
    }

    .bravo-buy-button .tf-text {
        background-image: url('{{url(' landing/img/logoen.png')}}');
        width: 66px;
        height: 12px;
        display: inline-block;
        background-repeat: no-repeat;

    }
</style>
<a class="sticky-button bravo-buy-button" target="_blank" href="https://codecanyon.net/item/mytravel-ultimate-laravel-booking-system/32680572">
    <span>Buy on <span class="tf-text"></span> for $59</span>
</a>