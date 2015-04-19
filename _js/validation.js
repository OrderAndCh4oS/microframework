(function($,W,D)
{
    var JQUERY4U = {};

    JQUERY4U.UTIL =
    {
        setupFormValidation: function()
        {
            //form validation rules
            $("#contact_form").validate({
                rules: {
                    clientname: {
                        required: true
                    },
                    clientemail: {
                        required: true,
                        email: true
                    },
                    enquiry: {
                        required: true
                    }
                },
                messages: {
                    clientname: "Please enter your name",
                    clientemail: "Please enter a valid email address",
                    enquiry: "Don't forget to write your message"
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        }
    }

    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        JQUERY4U.UTIL.setupFormValidation();
    });

})(jQuery, window, document);