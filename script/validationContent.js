$(function(){
    content = $("#noteContent");
    errContent = $("#errContent");

    function updateInputStyle(isValid, element) {
        if (!isValid) {
            element.css({
                'border': '2px solid red',
                'outline': 'none'
            });
        }
        else {
            element.css({
                'border': '2px solid white',
                'outline': 'none'
            });     
          }
    }
    function enableSubmitButton(enable) {
        $("button[type=submit]").prop('disabled', !enable);
    }

    async function validateContent() {
        let ok = true;
        contentMaxLength = $("#contentMaxLength").val();
        errContent.html("");
        content.removeClass('is-invalid is-valid');
        console.log(contentMaxLength);
        console.log(content.val());

        if(content.val().trim().length > contentMaxLength){
            errContent.append("<p>Content length must be less than 2000.</p>");
            ok = false;
        }

        updateInputStyle(ok, content);
        enableSubmitButton(ok);
        return ok;
    }

    content.on("input", validateContent);
    //$("input:text:first").focus();
});
