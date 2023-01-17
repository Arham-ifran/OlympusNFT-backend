$(document).ajaxStart(function () {
    showLoader();
});
$(document).ajaxStop(function () {
    hideLoader();
});

function showLoader() {
    if (typeof NProgress !== undefined && typeof NProgress !== 'undefined') {
        NProgress.start();
    }
}

function hideLoader() {
    if (typeof NProgress !== undefined && typeof NProgress !== 'undefined') {
        NProgress.done();
    }
}
$(function () {

    jQuery.validator.addMethod("validBTCAddress", function (value, element) {
        return this.optional(element) || WAValidator.validate(value, 'BTC');
    }, "Please provide a valid Bitcoin wallet address");
    jQuery.validator.addMethod("validXMRAddress", function (value, element) {
        return this.optional(element) || WAValidator.validate(value, 'XMR');
    }, "Please provide a valid Monero wallet address");


    $('.delete-form-btn').on('click', function () {
        var submitBtn = $(this).next('.deleteSubmit');
        Swal.fire({
            title: "Are you sure?",
            text: "You will not be able to recover this record!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false,
            closeOnCancel: true
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                submitBtn.click();
            } else if (result.isDenied) {
                // Swal.fire('Changes are not saved', '', 'info')
            }
        });

    });
    // $.validator.setDefaults({
    //     submitHandler: function () {
    //         alert("Form successful submitted!");
    //     },
    // });
    $("#settingsForm, #profile-form, #password-form").validate({
        // rules: {
        //     email: {
        //         required: true,
        //         email: true,
        //     },
        //     password: {
        //         required: true,
        //         minlength: 5,
        //     },
        //     terms: {
        //         required: true,
        //     },
        // },
        // messages: {
        //     email: {
        //         required: "Please enter a email address",
        //         email: "Please enter a vaild email address",
        //     },
        //     password: {
        //         required: "Please provide a password",
        //         minlength: "Your password must be at least 5 characters long",
        //     },
        //     terms: "Please accept our terms",
        // },
        errorElement: "span",
        errorPlacement: function (error, element) {
            error.addClass("invalid-feedback");
            element.closest(".form-group").append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass("is-invalid");
        },
    });
});

//show image onchange input file function
function change_image(input, img_id, show_div, width, height) {

    var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
    var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor);
    if (isSafari) {
        var nAgt = navigator.userAgent;
        var fullVersion = '' + parseFloat(navigator.appVersion);
        var verOffset;
        if ((verOffset = nAgt.indexOf("Safari")) != -1) {
            fullVersion = parseFloat(nAgt.substring(verOffset + 7));
            if ((verOffset = nAgt.indexOf("Version")) != -1)
                fullVersion = parseFloat(nAgt.substring(verOffset + 8));
        }
        if (fullVersion >= 7) {
            readURL1(this);
        } else {
            $('#' + show_div).html(input);
        }

    } else {
        readURL1(input, img_id, show_div, width, height);
    }
}

// Modifide readURL function for generic images
function readURL1(input, img_id, show_div, width, height) {


    if (input.files && input.files[0]) {
        var reader = new FileReader();

        var file = input.files[0];

        var image = new Image();
        reader.readAsDataURL(file);
        reader.onload = function (_file) {
            image.src = _file.target.result;
            image.onload = function () {
                var w = this.width,
                    h = this.height,
                    t = file.type,
                    n = file.name,
                    s = ~~(file.size / 1024) + 'KB';

                if (typeof width != 'undefined' && typeof height != 'undefined') {
                    if (w >= width && h >= height) {
                        $('#' + img_id).attr('src', image.src).parent().show();
                    } else {
                        $('#' + img_id).val('');
                        alert('Invalid image size (' + w + 'x' + h + '). Image must be large then ' + width + ' x ' + height);
                        return false;
                    }
                } else {
                    $('#' + img_id).attr('src', image.src).parent().show();
                    /* $('#' + show_div).html('image size: ' + w + ' X ' + h);*/

                }
            };

        };

    }
}