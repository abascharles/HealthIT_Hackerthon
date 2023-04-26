@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-4 pull-right">
            <div class="hpanel hgreen">
                <div class="panel-body">
                    <h3>{{ $user->full_name }}</h3>
                    <p><strong>Last Login:</strong>&nbsp;{{ $user->last_login }}</p>
                </div>
            </div>
        </div>
    </div>
    @foreach($user->dailyLogs() as $log)
        @include('users.logs',['log' => $log]);
    @endforeach
</div>

{{-- Old Section to this page keeping it coz might want this fancy display --}}
{{-- 
<div class="content">
<div class="row">
    <div class="col-lg-3">
        <div class="hpanel hgreen">
            <div class="panel-heading">Heading</div>
            <div class="panel-body">
                <!-- <img alt="logo" class="img-circle m-b m-t-md" src="images/profile.jpg"> -->
                <h3><a href="#">Max Simson</a></h3>
                <div class="text-muted font-bold m-b-xs">California, LA</div>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt est vitae ultrices accumsan.
                </p>
                <div class="progress m-t-xs full progress-small">
                    <div style="width: 65%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="65" role="progressbar" class=" progress-bar progress-bar-success">
                        <span class="sr-only">35% Complete (success)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="hpanel">
            <div class="panel-heading">
                <div class="panel-tools">
                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                    <a class="closebox"><i class="fa fa-times"></i></a>
                </div>
                Samples
            </div>
            <div class="panel-body list">

                <div class="pull-right">
                    <a href="#" class="btn btn-xs btn-default">Today</a>
                    <a href="#" class="btn btn-xs btn-default">Month</a>
                </div>
                <div class="list-item-container">
                    <div class="list-item">
                        <h3 class="no-margins font-extra-bold text-success">2,773</h3>
                        <small>Tota Messages Sent</small>
                        <div class="pull-right font-bold">98% <i class="fa fa-level-up text-success"></i></div>
                    </div>
                    <div class="list-item">
                        <h3 class="no-margins font-extra-bold text-color3">4,422</h3>
                        <small>Last activity</small>
                        <div class="pull-right font-bold">13% <i class="fa fa-level-down text-color3"></i></div>
                    </div>
                    <div class="list-item">
                        <h3 class="no-margins font-extra-bold text-color3">9,180</h3>
                        <small>Monthly income</small>
                        <div class="pull-right font-bold">22% <i class="fa fa-bolt text-color3"></i></div>
                    </div>
                    <div class="list-item">
                        <h3 class="no-margins font-extra-bold text-success">1,450</h3>
                        <small>Tota Messages Sent</small>
                        <div class="pull-right font-bold">44% <i class="fa fa-level-up text-success"></i></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="hpanel">
            <div class="panel-heading">
                <div class="panel-tools">
                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                    <a class="closebox"><i class="fa fa-times"></i></a>
                </div>
                Batches
            </div>
            <div class="panel-body list">

                <div class="pull-right">
                    <a href="#" class="btn btn-xs btn-default">Today</a>
                    <a href="#" class="btn btn-xs btn-default">Month</a>
                </div>
                <div class="list-item-container">
                    <div class="list-item">
                        <h3 class="no-margins font-extra-bold text-success">2,773</h3>
                        <small>Tota Messages Sent</small>
                        <div class="pull-right font-bold">98% <i class="fa fa-level-up text-success"></i></div>
                    </div>
                    <div class="list-item">
                        <h3 class="no-margins font-extra-bold text-color3">4,422</h3>
                        <small>Last activity</small>
                        <div class="pull-right font-bold">13% <i class="fa fa-level-down text-color3"></i></div>
                    </div>
                    <div class="list-item">
                        <h3 class="no-margins font-extra-bold text-color3">9,180</h3>
                        <small>Monthly income</small>
                        <div class="pull-right font-bold">22% <i class="fa fa-bolt text-color3"></i></div>
                    </div>
                    <div class="list-item">
                        <h3 class="no-margins font-extra-bold text-success">1,450</h3>
                        <small>Tota Messages Sent</small>
                        <div class="pull-right font-bold">44% <i class="fa fa-level-up text-success"></i></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="hpanel">
            <div class="panel-heading">
                <div class="panel-tools">
                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                    <a class="closebox"><i class="fa fa-times"></i></a>
                </div>
                Worksheets
            </div>
            <div class="panel-body list">

                <div class="pull-right">
                    <a href="#" class="btn btn-xs btn-default">Today</a>
                    <a href="#" class="btn btn-xs btn-default">Month</a>
                </div>
                <div class="list-item-container">
                    <div class="list-item">
                        <h3 class="no-margins font-extra-bold text-success">2,773</h3>
                        <small>Tota Messages Sent</small>
                        <div class="pull-right font-bold">98% <i class="fa fa-level-up text-success"></i></div>
                    </div>
                    <div class="list-item">
                        <h3 class="no-margins font-extra-bold text-color3">4,422</h3>
                        <small>Last activity</small>
                        <div class="pull-right font-bold">13% <i class="fa fa-level-down text-color3"></i></div>
                    </div>
                    <div class="list-item">
                        <h3 class="no-margins font-extra-bold text-color3">9,180</h3>
                        <small>Monthly income</small>
                        <div class="pull-right font-bold">22% <i class="fa fa-bolt text-color3"></i></div>
                    </div>
                    <div class="list-item">
                        <h3 class="no-margins font-extra-bold text-success">1,450</h3>
                        <small>Tota Messages Sent</small>
                        <div class="pull-right font-bold">44% <i class="fa fa-level-up text-success"></i></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
 --}}
@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent

@endsection