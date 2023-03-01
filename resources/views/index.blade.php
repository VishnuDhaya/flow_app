<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no"><meta name="theme-color" content="#000000"><link rel="manifest" href="/manifest.json"><link rel="shortcut icon" href="/icon.png"><link rel="stylesheet" href="/styles/bootstrap/dist/css/bootstrap.min.css"><link rel="stylesheet" href="/styles/font/monserrat.css"><link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons"/><link rel="stylesheet" href="/theme/assets/css/material-dashboard.css"><link rel="stylesheet" href="/theme/assets/css/custom.css"><link rel="stylesheet" href="/styles/css/designSystem.css"><title>FLOW - The App</title><link href="/static/css/2.2607bd96.chunk.css" rel="stylesheet"><link href="/static/css/main.b1f2cd96.chunk.css" rel="stylesheet"></head><body class="dark-edition" id="body"><noscript>You need to enable JavaScript to run this app.</noscript><div id="root"></div><script>!function(l){function e(e){for(var r,t,n=e[0],o=e[1],u=e[2],f=0,i=[];f<n.length;f++)t=n[f],p[t]&&i.push(p[t][0]),p[t]=0;for(r in o)Object.prototype.hasOwnProperty.call(o,r)&&(l[r]=o[r]);for(s&&s(e);i.length;)i.shift()();return c.push.apply(c,u||[]),a()}function a(){for(var e,r=0;r<c.length;r++){for(var t=c[r],n=!0,o=1;o<t.length;o++){var u=t[o];0!==p[u]&&(n=!1)}n&&(c.splice(r--,1),e=f(f.s=t[0]))}return e}var t={},p={1:0},c=[];function f(e){if(t[e])return t[e].exports;var r=t[e]={i:e,l:!1,exports:{}};return l[e].call(r.exports,r,r.exports,f),r.l=!0,r.exports}f.m=l,f.c=t,f.d=function(e,r,t){f.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:t})},f.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},f.t=function(r,e){if(1&e&&(r=f(r)),8&e)return r;if(4&e&&"object"==typeof r&&r&&r.__esModule)return r;var t=Object.create(null);if(f.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:r}),2&e&&"string"!=typeof r)for(var n in r)f.d(t,n,function(e){return r[e]}.bind(null,n));return t},f.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return f.d(r,"a",r),r},f.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},f.p="/";var r=window.webpackJsonp=window.webpackJsonp||[],n=r.push.bind(r);r.push=e,r=r.slice();for(var o=0;o<r.length;o++)e(r[o]);var s=n;a()}([])</script><script src="/static/js/2.269579de.chunk.js"></script><script src="/static/js/main.bf2df5ee.chunk.js"></script></body><script src="/styles/jquery/dist/jquery.min.js"></script><script src="/styles/bootstrap/dist/js/bootstrap.bundle.min.js"></script><script src="/theme/assets/js/core/jquery.min.js"></script><script src="/theme/assets/js/core/popper.min.js"></script><script src="/theme/assets/js/core/bootstrap-material-design.min.js"></script><script src="/theme/assets/js/plugins/moment.min.js"></script><script src="/theme/assets/js/plugins/sweetalert2.js"></script><script src="/theme/assets/js/plugins/jquery.validate.min.js"></script><script src="/theme/assets/js/plugins/jquery.bootstrap-wizard.js"></script><script src="/theme/assets/js/plugins/bootstrap-selectpicker.js"></script><script src="/theme/assets/js/plugins/bootstrap-datetimepicker.min.js"></script><script src="/theme/assets/js/plugins/jquery.dataTables.min.js"></script><script src="/theme/assets/js/plugins/bootstrap-tagsinput.js"></script><script src="/theme/assets/js/plugins/jasny-bootstrap.min.js"></script><script src="/theme/assets/js/plugins/fullcalendar/fullcalendar.min.js"></script><script src="/theme/assets/js/plugins/fullcalendar/daygrid.min.js"></script><script src="/theme/assets/js/plugins/fullcalendar/timegrid.min.js"></script><script src="/theme/assets/js/plugins/fullcalendar/list.min.js"></script><script src="/theme/assets/js/plugins/fullcalendar/interaction.min.js"></script><script src="/theme/assets/js/plugins/jquery-jvectormap.js"></script><script src="/theme/assets/js/plugins/nouislider.min.js"></script><script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script><script src="/theme/assets/js/plugins/arrive.min.js"></script><script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script><script src="/theme/assets/js/plugins/chartist.min.js"></script><script src="/theme/assets/js/plugins/bootstrap-notify.js"></script><script src="/theme/assets/js/material-dashboard.js?v=1.0.1" type="text/javascript"></script><script src="https://kit.fontawesome.com/4b47f93a51.js" crossorigin="anonymous"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script><script>$(document).ready(function(){$().ready(function(){$sidebar=$(".sidebar"),$sidebar_img_container=$sidebar.find(".sidebar-background"),$full_page=$(".full-page"),$sidebar_responsive=$("body > .navbar-collapse"),window_width=$(window).width(),992<=window_width&&$("#body").addClass("sidebar-mini"),fixed_plugin_open=$(".sidebar .sidebar-wrapper .nav li.active a p").html(),767<window_width&&"Dashboard"==fixed_plugin_open&&$(".fixed-plugin .dropdown").hasClass("show-dropdown")&&$(".fixed-plugin .dropdown").addClass("open"),$(".fixed-plugin a").click(function(a){$(this).hasClass("switch-trigger")&&(a.stopPropagation?a.stopPropagation():window.event&&(window.event.cancelBubble=!0))}),$(".fixed-plugin .active-color span").click(function(){$full_page_background=$(".full-page-background"),$(this).siblings().removeClass("active"),$(this).addClass("active");var a=$(this).data("color");0!=$sidebar.length&&$sidebar.attr("data-color",a),0!=$full_page.length&&$full_page.attr("filter-color",a),0!=$sidebar_responsive.length&&$sidebar_responsive.attr("data-color",a)}),$(".fixed-plugin .background-color .badge").click(function(){$(this).siblings().removeClass("active"),$(this).addClass("active");var a=$(this).data("background-color");0!=$sidebar.length&&$sidebar.attr("data-background-color",a)}),$(".fixed-plugin .img-holder").click(function(){$full_page_background=$(".full-page-background"),$(this).parent("li").siblings().removeClass("active"),$(this).parent("li").addClass("active");var a=$(this).find("img").attr("src");if(0!=$sidebar_img_container.length&&0!=$(".switch-sidebar-image input:checked").length&&$sidebar_img_container.fadeOut("fast",function(){$sidebar_img_container.css("background-image",'url("'+a+'")'),$sidebar_img_container.fadeIn("fast")}),0!=$full_page_background.length&&0!=$(".switch-sidebar-image input:checked").length){var i=$(".fixed-plugin li.active .img-holder").find("img").data("src");$full_page_background.fadeOut("fast",function(){$full_page_background.css("background-image",'url("'+i+'")'),$full_page_background.fadeIn("fast")})}if(0==$(".switch-sidebar-image input:checked").length){a=$(".fixed-plugin li.active .img-holder").find("img").attr("src"),i=$(".fixed-plugin li.active .img-holder").find("img").data("src");$sidebar_img_container.css("background-image",'url("'+a+'")'),$full_page_background.css("background-image",'url("'+i+'")')}0!=$sidebar_responsive.length&&$sidebar_responsive.css("background-image",'url("'+a+'")')}),$(".switch-sidebar-image input").change(function(){$full_page_background=$(".full-page-background"),$input=$(this),$input.is(":checked")?(0!=$sidebar_img_container.length&&($sidebar_img_container.fadeIn("fast"),$sidebar.attr("data-image","#")),0!=$full_page_background.length&&($full_page_background.fadeIn("fast"),$full_page.attr("data-image","#")),background_image=!0):(0!=$sidebar_img_container.length&&($sidebar.removeAttr("data-image"),$sidebar_img_container.fadeOut("fast")),0!=$full_page_background.length&&($full_page.removeAttr("data-image","#"),$full_page_background.fadeOut("fast")),background_image=!1)}),$(".switch-sidebar-mini input").change(function(){if($body=$("body"),$input=$(this),1==md.misc.sidebar_mini_active){if($("body").removeClass("sidebar-mini"),md.misc.sidebar_mini_active=!1,0!=$(".sidebar").length)new PerfectScrollbar(".sidebar");if(0!=$(".sidebar-wrapper").length)new PerfectScrollbar(".sidebar-wrapper");if(0!=$(".main-panel").length)new PerfectScrollbar(".main-panel");if(0!=$(".main").length)new PerfectScrollbar("main");$("html").addClass("perfect-scrollbar-on")}else $("html").addClass("perfect-scrollbar-off"),setTimeout(function(){$("body").addClass("sidebar-mini"),md.misc.sidebar_mini_active=!0},300);var a=setInterval(function(){window.dispatchEvent(new Event("resize"))},180);setTimeout(function(){clearInterval(a)},1e3)})})})</script></html>