<!-- Modal -->
<div class="modal fade" id="upiRegistryDetailsModal" tabindex="-1"
     data-controls-modal="upiRegistryDetailsModal" data-backdrop="static"
     data-keyboard="false" role="dialog" aria-hidden="true"
     aria-labelledby="upiRegistryDetailsModalTitle">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable " role="document">
        <div class="modal-content">
            <div class="modal-header panel-info">
                <button type="button btn-sm" class="close p-t-12" data-dismiss="modal" aria-label="Close">
                    <i data-dismiss="modal"
                       style="padding-right: 10px"><b>X</b></i>
                </button>
                <h4 class="modal-title" id="upiRegistryDetailsModalTitle"><i>MOH Registry Patient Details </i></h4>
            </div>
            <div class="modal-body">
                <div id=upiClientInfoAvailable">
                    <div class="form-group row">
                        <label for="clientUpi" class="col-sm-4 col-form-label text-right">UPI</label>
                        <div class="col-sm-8">
                            <input type="text" readonly  class="form-control" id="clientUpi">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="clientDob" class="col-sm-4 col-form-label text-right">D.O.B</label>
                        <div class="col-sm-8">
                            <input type="text" readonly class="form-control" id="clientDob">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="firstName" class="col-sm-4 col-form-label text-right">First Name</label>
                        <div class="col-sm-8">
                            <input type="text" readonly class="form-control" id="firstName">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="middleName" class="col-sm-4 col-form-label text-right">Middle Name</label>
                        <div class="col-sm-8">
                            <input type="text" readonly class="form-control" id="middleName">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lastName" class="col-sm-4 col-form-label text-right">Last Name</label>
                        <div class="col-sm-8">
                            <input type="text" readonly class="form-control" id="lastName">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="gender" class="col-sm-4 col-form-label text-right">Gender</label>
                        <div class="col-sm-8">
                            <input type="text"readonly class="form-control" id="gender">
                        </div>
                    </div>
                </div>
                <div id="onUpiFailView" style="display: none">
                    <p class="text-danger">Could not retrieve details for UPI provided. Please confirm the format and try again</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary center" data-dismiss="modal">Close</button>
                <button type="button" onclick="useDetails()" class="btn btn-primary right">Use details</button>
            </div>
        </div>
    </div>
</div>
<script>
    /**
     * populate the name sex dob age fields on the EID and VL sample addition forms3
     * */
    function useDetails() {
        var dobVal = $("#clientDob").val();
        if (dobVal) {
            $("#name").val($("#firstName").val() + " " + $("#lastName").val());
            $("#dob").val(dobVal).trigger("change");
            var g = $("#gender").val();
            if (g) {
                if (g === 'Female') {
                    $("#sex").val(2).trigger("change");
                } else {
                    $("#sex").val(1).trigger("change")
                }

            }
            $("#upiRegistryDetailsModal").modal('hide');
        } else {
            alert("Incorrect details, close and check UPI")
        }
    }
</script>