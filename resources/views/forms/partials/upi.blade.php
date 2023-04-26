<div class="form-group">
    <label class="col-sm-4 control-label">Patient UPI No.
        <strong><div style='color: #37ff00; display: none;'>*</div></strong>
    </label>
    <div class="col-sm-3">
        <input class="form-control" id="upi_no" name="upi_no" maxlength="13" type="text">
    </div>
    <a onclick="get_upi_verification()" class="btn btn-success">VERIFY PATIENT &nbsp;
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search"
             viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
        </svg>
    </a>
</div>

<script>
    function get_upi_verification() {

        $("#upiRegistryDetailsModal")
            .find("input,textarea,select")
            .val('')
            .end();
        $("#onUpiFailView").hide()
        var upi_no = $("#upi_no").val();
        if (upi_no) {
            $.ajax({
                type: "GET",
                url: "{{ url('patient_cr') }}" + "/" + upi_no,
                success: function (data) {
                    if (data.clientNumber) {
                        $("#clientUpi").val(data['clientNumber']);
                        $("#clientDob").val($.datepicker.formatDate('yy-mm-dd', new Date(data['dateOfBirth'])));
                        $("#firstName").val(data['firstName']);
                        $("#middleName").val(data['middleName']);
                        $("#lastName").val(data['lastName']);
                        $("#maritalStatus").val(data['maritalStatus']);
                        $("#gender").val(data['gender']);
                        $("#occupation").val(data['occupation']);
                        $("#religion").val(data['religion']);
                        $("#educationLevel").val(data['educationLevel']);

                        $("#upiRegistryDetailsModal").modal('show');

                    } else {
                        $("#onUpiFailView").show();
                        $("#upiClientInfoAvailable").hidden;

                        $("#upiRegistryDetailsModal").modal('show');
                    }
                }
            });
        } else {
            alert("Please provide a upi");
            return false;

        }
    }
</script>