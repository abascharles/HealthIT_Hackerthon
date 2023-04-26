@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover data-table" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Subject</th>
                                    <th>County</th>
                                    <th>From Name</th>
                                    <th>CC List</th>
                                    <th>BCC List</th>
                                    <th>Lab Signature</th>
                                    <th>Time To Be Sent</th>
                                    <th>Sent</th>
                                    <th>Attachments</th>
                                    <th>Test Email</th>
                                    <th>Edit Email</th>
                                    <th>Delete Email</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($emails as $key => $email)
                                    <tr>
                                        <td> {{ $key+1 }} </td>
                                        <td> {{ $email->name ?? '' }} </td>
                                        <td> {{ $email->subject ?? '' }} </td>
                                        <td> {{ $email->county->name ?? '' }} </td>
                                        <td> {{ $email->from_name ?? env('MAIL_FROM_NAME') }} </td>
                                        <td> {{ $email->cc_list ?? '' }} </td>
                                        <td> {{ $email->bcc_list ?? '' }} </td>
                                        <td> {{ $email->lab_signature ?? '' }} </td>
                                        <td> {{ $email->my_date_format('time_to_be_sent', 'd/m/Y h:i:s a') }} </td>
                                        @if($email->sent)
                                            <td>True</td>
                                        @else
                                            <td>False</td>
                                        @endif
                                        <td>
                                            <a href="{{ url('/email/attachment/' . $email->id ) }} " target='_blank'>Attachments</a>
                                        </td>
                                        <td>
                                            <a href="{{ url('/email/preview/' . $email->id ) }} " target='_blank'>Preview</a>
                                        </td>
                                        <td>
                                            <a href="{{ url('/email/' . $email->id . '/edit' ) }} " target='_blank'>Edit</a>
                                        </td>
                                        <td>
                                            <form action="{{ url('email/' . $email->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the following email?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-primary">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent

@endsection