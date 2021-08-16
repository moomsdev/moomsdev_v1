// eslint-disable-next-line no-unused-vars
import config from "@config";
import "./vendor/*.js";
import "./pages/*.js";
import "@styles/theme";
import "@images/favicon.ico";
import "airbnb-browser-shims";

// Your code goes here ...
// import "mmenu-js/src/mmenu";
// import "mmenu-js/dist/mmenu.polyfills";
import "swiper/swiper-bundle.min";
import Swiper from "swiper/swiper-bundle.min";
// import "aos/dist/aos";
// import AOS from "aos";

// import "@fancyapps/fancybox/dist/jquery.fancybox.min";
$().ready(function () {
    jQuery(document).ready(function() {
        $("#main_menu").hover(
            function(){
                window.setTimeout(function(){
                    $("ul").addClass("show");
                }, 400);
            },
            function(){
                $("ul").removeClass("show");
            });
    });
  // $('[data-fancybox="images"]').fancybox({
  //   thumbs: {
  //     autoStart: true,
  //     axis: "x",
  //   },
  // });

  //--------animation
  // AOS.init({
  //   once: true,
  // });

  //---------mmenu
  // let fb = $("#mobile_menu").data("fb");
  // let ytb = $("#mobile_menu").data("ytb");
  // let itg = $("#mobile_menu").data("itg");
  // new Mmenu("#mobile_menu", {
  //   extensions: ["position-bottom", "fullscreen", "theme-black", "border-full"],
  //   searchfield: false,
  //   counters: false,
  //
  //   navbar: {
  //     title: "MENU",
  //   },
  //   iconbar: {
  //     use: true,
  //     top: ["<a href='/'><i class='fa fa-home'></i></a>"],
  //     bottom: [
  //       "<a target='_blank' href='" +
  //         fb +
  //         "'><i class='fab fa-facebook-f'></i></a>",
  //       "<a target='_blank' href='" +
  //         ytb +
  //         "'><i class='fab fa-youtube'></i></a>",
  //       "<a target='_blank' href='" +
  //         itg +
  //         "'><i class='fab fa-instagram'></i></a>",
  //     ],
  //   },
  // });

  //----------slider
  const swiper_slider = new Swiper(".__text_slider", {
      slidersPerView: 1,
      centeredSlides: true,
      effect: "fade",
      fadeEffect: { crossFade: true },
      virtualTranslate: true,
      speed: 1200,
      autoplay: {
          delay: 5000,
          disableOnInteraction: false,
      },

  });

});

