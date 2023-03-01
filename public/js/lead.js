/***** navbar dropdown *****/

$(document).ready(function () {
    var countVal = 1;
    $(".dropdown").on('click',function(){
        var countValadded = countVal++;
        if( countValadded %2 != 0) {
            $(".navDropdownIndicatordown").css({'transform':'rotate(180deg)','transition':'.15s linear'});
        }
        else if( countValadded %2 == 0) {
            $(".navDropdownIndicatordown").css({'transform':'rotate(0deg)','transition':'.15s linear'});
        }
    })
    $(".dropdown-box-item").mouseover(function(){
        $(this).css("transition", ".15s linear");
    })
    $(".dropdown-box-item").mouseout(function(){
        $(this).css("transition", ".15s linear");
    })

    $(".dis-input input").prop('disabled',true)
})
MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

var observer = new MutationObserver(function(mutations, observer) {
    $(document).ready(function () {
        setTimeout(function() {
            $('#alert').fadeOut('fast');
        }, 3000);
    })
});
observer.observe(document, {
    subtree: true,
    attributes: true
});