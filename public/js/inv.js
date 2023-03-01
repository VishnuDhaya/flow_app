$(document).ready(function () {
    if (!sessionStorage.alreadyClicked) {
        $('#loader').removeClass('d-none');
        setTimeout(function () {
            $("#loader").css("display", "none");
            $(".main-div").addClass("d-block");
            sessionStorage.alreadyClicked = 1;
        }, 2000);
    }
    else{
        $('.main-div').removeClass('animate-bottom')
        $(".main-div").addClass("d-block");
    }
});

if($(".net-rtrn .text-right").html()<0) {
    $(".net-rtrn").css("color", 'red')
}

if($("#carousel-div").length>0){
    document.getElementsByClassName("carousel-item")[0].classList.add("active");
}

$(function () {
    $('[data-toggle="popover"]').popover({
        container:'div',
    })
})

function  tabswitch(e){
    if(e.id=="financial"){
        $("#financial").css({"background-color":"#DCF0FF","font-weight":"bold"});
        $("#social").css({"background-color":"#fff","font-weight":"normal"});
    }
    else{
        $("#financial").css({"background-color":"#fff","font-weight":"normal"});
        $("#social").css({"background-color":"#DCF0FF","font-weight":"bold"});
    }
}

$('#carouselControlsHome').on('slid.bs.carousel', function () {

    if(($(".carousel-item.all-tab").hasClass("active"))){
        $(".social_rtn_tab_switch").find(".active").removeClass("active");
        $("#all-btn").addClass("active");
    }
    else if(($(".carousel-item.men-tab").hasClass("active"))){
        $(".social_rtn_tab_switch").find(".active").removeClass("active");
        $("#men-btn").addClass("active");
    }
    else{
        $(".social_rtn_tab_switch").find(".active").removeClass("active");
        $("#women-btn").addClass("active");
    }
})
// $( document ).ready(function() {
//     let c =new CountUp("crc",0,12020);
//     c.start();
// });

$(document).ready(function() {
    $('[data-toggle="info-popover"]').popover({
        container:'.main-div',
    });
});
$(document).ready(function() {
    $('[data-toggle="smry-popover"]').popover({
        container:'.bond-smry',
    });
});

$(document).ready(function() {
    $('[data-toggle="txn-popover"]').popover({
        container:'#txns',
    });
});

$(document).ready(function() {
    $('[data-toggle="scl-popover"]').popover({
        container:'.social',
    });
});
$(document).ready(function(){
    $('[data-toggle="acnt-popover"]').popover({
        container:'.white-box',
    })
});

data=""+$("#return-content").html();
$(document).ready(function() {
    $('[data-toggle="return-popover"]').popover({
        container:'.bond-detail-row',
        html: true,
        content: data,
    });
});

function bondSwitch(e) {
    if(e.id=="allBonds"){
        $("#myBond").removeClass("active-bond")
        $("#allBonds").addClass("active-bond")
        $(".single").addClass("d-none")
        $(".bondswt").removeClass("fw-bold")
        $(".multi").removeClass("d-none")
        $(".table").addClass("d-none")
        $(".business-val h1").html($(".all-cust").html())
        $(".tot-val h1").html($(".all-disb").html())
        $(".bond-type-head").html('All Bonds')
        $("#fund-code").html("Flow Ecosystem");
        $(".crc-val p").html($(".all-crc").html())
        $(".rtf-val p").html($(".all-rtf").html())
        $(".pb-val p").html($(".all-pb").html())
        console.log(e.id)
    }
    else{
        $("#myBond").addClass("active-bond")
        $("#allBonds").removeClass("active-bond")
        $(".single").removeClass("d-none")
        $(".bondswt").addClass("fw-bold")
        $(".multi").addClass("d-none")
        $(".table").removeClass("d-none")
        $(".business-val h1").html($(".cust").html())
        $(".tot-val h1").html($(".disb").html())
        $(".bond-type-head").html($('.bond-head').html())
        $("#fund-code").html($('.fund_code').html());
        $(".crc-val p").html($(".crc").html())
        $(".rtf-val p").html($(".rtf").html())
        $(".pb-val p").html($(".pb").html())
    }

}
