<!-- Jquery Validate -->
<script src="{{ asset('js/validate/jquery.validate.min.js') }}"></script>

{{ $js_scripts ?? '' }}

<script type="text/javascript">

    $.fn.serializeObject = function() {
        var o = {};
        //var a = this.serializeArray();
        $(this).find('input[type="hidden"], input[type="text"], input[type="password"], input[type="checkbox"]:checked, input[type="radio"]:checked, select').each(function() {
            if ($(this).attr('type') == 'hidden') { //If checkbox is checked do not take the hidden field
                var $parent = $(this).parent();
                var $chb = $parent.find('input[type="checkbox"][name="' + this.name.replace(/\[/g, '\[').replace(/\]/g, '\]') + '"]');
                if ($chb != null) {
                    if ($chb.prop('checked')) return;
                }
            }
            if (this.name === null || this.name === undefined || this.name === '' || this.name == '_token' || this.name == '_method')
                return;
            var elemValue = null;
            if ($(this).is('select'))
                elemValue = $(this).find('option:selected').val();
            else
                elemValue = this.value;
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(elemValue || '');
            }
            else {
                o[this.name] = elemValue || '';
            }
            if(o[this.name] == '') o[this.name] = null;
        });
        return o;
    };

    $(document).ready(function(){
    	$(".form-control").attr('autocomplete', 'off');

        // $(".form-horizontal select").select2({ width: 'resolve' }); 
        $(".form-horizontal select").select2({
            placeholder: "Select One",
            allowClear: true
        }); 

        var msg;
        var dynamicErrorMsg = function () { return msg; }


        jQuery.validator.addMethod("GreaterThanSpecific", function(value, element, param) {

            var start = value;
            var finish = param[0];

            var start_date = new Date(start);
            var finish_date = new Date(finish);

            msg =  param[1] + " cannot be set to a date less than " + param[0];

            return this.optional(element) || (start_date >= finish_date);

        }, dynamicErrorMsg);


        jQuery.validator.addMethod("lessThan", function(value, element, param) {

            var start = value;
            var finish = $( param[0] ).val();

            var s = start.split("-");
            var f = finish.split("-");

            // console.log("length is " + f.length);

            if(f.length < 3 || s.length < 3){
                return true;
            }
            else{
                // var start_date = new Date(s[0], s[1], s[2]);
                // var finish_date = new Date(f[0], f[1], f[2]);

                // console.log("Start date is " + start_date);
                // console.log("End date is " + finish_date);

                var start_date = new Date(start);
                var finish_date = new Date(finish);


                msg =  param[1] + " cannot be set to a date less than " + param[2];
                // msg =  param[1] + " " + start_date + " cannot be set to a date greater than " + finish_date + " " + param[2];

                return this.optional(element) || (start_date <= finish_date);

            }

            /*if (!/Invalid|NaN/.test(new Date(value))) {
                return new Date(value) > new Date($(params).val());
            }

            return isNaN(value) && isNaN($(params).val())
                || (Number(value) > Number($(params).val()));*/

        }, dynamicErrorMsg);

        jQuery.validator.addMethod("greaterThan", function(value, element, param) {

            var finish = value;
            var start = $( param[0] ).val();

            var s = start.split("-");
            var f = finish.split("-");

            if(f.length < 3){
                return true;
            }
            else{
                var start_date = new Date(start);
                var finish_date = new Date(finish);

                msg =  param[1] + " cannot be set to a date less than " + param[2];

                return this.optional(element) || (start_date <= finish_date);

            }
        }, dynamicErrorMsg);

        jQuery.validator.addMethod("lessThanTwo", function(value, element, param) {

            var start = value;
            var finish = $( param[0] ).val();

            var s = start.split("-");
            var f = finish.split("-");

            // console.log("length is " + f.length);

            if(f.length < 3){
                return true;
            }
            else{
                var start_date = new Date(start);
                var finish_date = new Date(finish);

                msg =  param[1] + " cannot be set to a date greater than " + param[2];

                return this.optional(element) || (start_date <= finish_date);

            }

            /*if (!/Invalid|NaN/.test(new Date(value))) {
                return new Date(value) > new Date($(params).val());
            }

            return isNaN(value) && isNaN($(params).val())
                || (Number(value) > Number($(params).val()));*/

        }, dynamicErrorMsg);

        jQuery.validator.addMethod("regex", function(value, element, param) {

            var re = new RegExp(param);
            return this.optional(element) || re.test(value);

        }, "Please check your input.");

        $(".form-horizontal").validate({
            errorPlacement: function (error, element)
            {
                element.before(error);
            }
            {{ $val_rules ?? '' }}
        });
        
        {{ $slot }}

    });

    function diff_in_years(dt2, dt1)
    {
        var diff =(dt2.getTime() - dt1.getTime()) / 1000;
        diff /= (60 * 60 * 24);
        return Math.abs(Math.round(diff/365.25));
    }


$.fn.serializeObject = function() {
    var o = {};
    //var a = this.serializeArray();
    $(this).find('input[type="hidden"], input[type="text"], input[type="password"], input[type="checkbox"]:checked, input[type="radio"]:checked, select').each(function() {
        if ($(this).attr('type') == 'hidden') { //If checkbox is checked do not take the hidden field
            var $parent = $(this).parent();
            var $chb = $parent.find('input[type="checkbox"][name="' + this.name.replace(/\[/g, '\[').replace(/\]/g, '\]') + '"]');
            if ($chb != null) {
                if ($chb.prop('checked')) return;
            }
        }
        if (this.name === null || this.name === undefined || this.name === '')
            return;
        var elemValue = null;
        if ($(this).is('select'))
            elemValue = $(this).find('option:selected').val();
        else
            elemValue = this.value;
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(elemValue || '');
        }
        else {
            o[this.name] = elemValue || '';
        }
        if(o[this.name] == '') o[this.name] = null;
    });
    return o;
};

</script>
