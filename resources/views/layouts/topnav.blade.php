<style type="text/css">
    .navbar.navbar-static-top a, .nav.navbar-nav li a {
        color: black;
    }
    @media (min-width: 768px)
    .navbar-nav {
        float: left;
        margin: 0;
        margin-right: 2em;
    }
    .navbar.navbar-static-top a, .nav.navbar-nav li a {
        color: black;
        padding-left: 10px;
        /* padding-right: 10px; */
    }
</style>

<div id="header">
    <div class="">
    </div>
    <a href="{{ url('/home') }} ">
        <div id="logo" class="light-version" style="padding-left: 2px; padding-top: 6px; width: 250px;">
            <span>
                <img src="{{ asset('img/logo.jpg') }}">
            </span>
        </div>
    </a>
    <nav role="navigation">
        <div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
        <div class="small-logo">
            <span class="text-primary">@if(Auth::user()->user_type_id != 7) @if(Session('testingSystem') == 'CD4') CD4 @else EID - VL @endif @endif</span>
        </div>
        @if(Session('testingSystem') != 'CD4')
            @if(Session('pendingTasks') && !(env('APP_LAB') == 2 || env('APP_LAB') == 5))
                <form role="search" class="navbar-form-custom" style="width: 400px;">
                    <div class="form-group">
                        <h4 style="margin-top:1em;">{{ $pageTitle ?? '' }}</h4>
                    </div>
                </form>
            @endif
        @endif
        <!-- Mobile View -->
        <!-- Anything edited here should also be edited below on the PC view section -->
        <div class="mobile-menu">
            <button type="button" class="navbar-toggle mobile-menu-toggle" data-toggle="collapse" data-target="#mobile-collapse">
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="collapse mobile-navbar" id="mobile-collapse">
                <ul class="nav navbar-nav">
                    @if(in_array(Auth::user()->user_type_id, [0,1,4]))
                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Help Desk <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/help_desk">SOP</a></li>
                            <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/help_desk_video">SOP Video</a></li>
                            <li class=""><a class="dropdown-item" target="_blank" href="https://dataposit.mojohelpdesk.com">Submit Query</a></li>
                        </ul>
                    </li>
                    @endif
                @if(!in_array(Auth::user()->user_type_id, [7,8,10,11]))
                    @if(Session('testingSystem') == 'CD4')
                        <li class="">
                            <a href="{{ url('home') }}">Home</a>
                        </li>
                        <li class="">
                            <a href="{{ url('cd4/sample') }}">Samples</a>
                        </li>
                        <li class="">
                            <a href="{{ url('cd4/worksheet') }}">Worksheets</a>
                        </li>
                        <li class="">
                            <a href="{{ url('cd4/sample/dispatch/2') }}">Results List</a>
                        </li>
                        <li class="">
                            <a href="{{ url('cd4/reports') }}">Reports</a>
                        </li>
                        <li class="">
                            <a href="{{ url('home') }}">Dashboard</a>
                        </li>
                    @elseif(Session('testingSystem') == 'Covid') 
                        <li class="">
                            <a href="{{ url('covid_sample') }}">Samples</a>
                        </li>
                        <li class="">
                            <a href="{{ url('covid_sample/cif') }}">CIF Samples</a>
                        </li>
                        <li class="">
                            <a href="{{ url('covid_sample/jitenge') }}">Jitenge Samples</a>
                        </li>
                        <li class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Traveller's Corner <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class=""><a class="dropdown-item" href="{{ url('traveller') }}">Samples</a></li>
                                <li class=""><a class="dropdown-item" href="{{ url('traveller/create') }}">Upload</a></li>
                            </ul>
                        </li>
                        <li class="">
                            <a href="{{ url('covid_worksheet') }}">Worksheets</a>
                        </li>
                        <li class="">
                            <a href="{{ url('covid_pool') }}">Pools</a>
                        </li>
                        <li class="">
                            <a href="{{ url('covid_sample/index/2') }}">Results List</a>
                        </li>
                        <li class="">
                            <a href="{{ url('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="">
                            <a href="{{ url('covidreports') }}">Reports</a>
                        </li>
                        <li class="">
                            <a href="{{ url('covidkits/reports') }}">Kits</a>
                        </li>
                    @elseif(session('testingSystem') == 'DR')
                        <li class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Dashboard <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- <li class=""><a class="dropdown-item" href="{{ url('dr_dashboard') }}">Dashboard</a></li> -->
                                <!-- <li class=""><a class="dropdown-item" href="{{ url('dr_testing') }}">Testing Dashboard</a></li> -->
                                <li class=""><a class="dropdown-item" href="{{ url('dr_waterfall') }}">Main Dashboard</a></li>
                                <li class=""><a class="dropdown-item" href="{{ url('dr_formulation') }}">Formulation Resistance</a></li>
                                <li class=""><a class="dropdown-item" href="{{ url('dr_mutation') }}">Mutation Frequency</a></li>
                            </ul>
                        </li>
                        @if(!in_array(auth()->user()->user_type_id, [20]))
                        @if(env('APP_LAB') == 1)
                            <li class="">
                                <a href="{{ url('viralsample/potential_dr') }}">Potential Samples</a>
                            </li>
                        @endif
                        <li class="">
                            <a href="{{ url('dr_extraction_worksheet') }}">Extraction Worksheets</a>
                        </li>
                        <li class="">
                            <a href="{{ url('dr_worksheet') }}">Sequencing Worksheets</a>
                        </li>
                        <li class="">
                            <a href="{{ url('dr_sample/index/1') }}">Completed Results</a>
                        </li>    
                        @if(auth()->user()->is_admin)                            <li>
                                <a href="{{ url('user') }}">Users</a>
                            </li>
                            <li>
                                <a href="{{ url('facility') }}">Facilities</a>
                            </li>
                        @endif   
                        <li class="">
                            <a href="{{ url('reports') }}">Report</a>
                        </li>
                        @endif
                    @elseif(session('testingSystem') == 'HPV')
                        <li class="">
                            <a href="{{ url('home') }}">Home</a>
                        </li>
                        <li class="">
                            <a href="{{ url('cancersample') }}">Samples</a>
                        </li>
                        <li class="">
                            <a href="{{ url('cancerworksheet') }}">Worksheets</a>
                        </li>
                        <li class="">
                            <a href="{{ url('cancersample/index/true') }}">Dispatched Results</a>
                        </li>
                        <li class="">
                            <a href="{{ url('reports') }}">Reports</a>
                        </li>
                        <li class="">
                            <a href="{{ url('dashboard') }}">Dashboard</a>
                        </li>
                    @else
                        @if(!Session('pendingTasks') || env('APP_LAB') == 2)
                            @if (Auth::user()->user_type_id == 5)
                                @if(in_array('APP_LAB', [1, 3]))
                                    <li class=""><a href="{{ url('covid_sample') }}">Covid Samples</a></li>
                                    <li class=""><a href="{{ url('covid_sample/index/2') }}">Covid Results</a></li>
                                @endif

                                <li class="dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Samples <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class=""><a class="dropdown-item" href="{{ url('batch') }}">EID</a></li>
                                        <li class=""><a class="dropdown-item" href="{{ url('viralbatch') }}">VL</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Results <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class=""><a class="dropdown-item" href="{{ url('batch/index/1') }}">EID</a></li>
                                        <li class=""><a class="dropdown-item" href="{{ url('viralbatch/index/1') }}">VL</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        POC Samples <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class=""><a class="dropdown-item" href="{{ url('sample/list_poc') }}">EID</a></li>
                                        <li class=""><a class="dropdown-item" href="{{ url('viralsample/list_poc') }}">VL</a></li>
                                    </ul>
                                </li>

                                <li class="">
                                    <a href="{{ url('cancersample') }}">HPV Samples</a>
                                </li>
                                <li class="">
                                    <a href="{{ url('worklist') }}">POC Worklists</a>
                                </li>
                                <li class="dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        SMS Log <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class=""><a class="dropdown-item" href="{{ url('sample/sms_log') }}">EID SMS Log</a></li>
                                        <li class=""><a class="dropdown-item" href="{{ url('viralsample/sms_log') }}">VL SMS Log</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Reports <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class=""><a class="dropdown-item" href="{{ url('facility/reports/EID') }}">EID Reports</a></li>
                                        <li class=""><a class="dropdown-item" href="{{ url('facility/reports/VL') }}">VL Reports</a></li>
                                        <li class=""><a class="dropdown-item" href="{{ url('facility/reports/HPV') }}">HPV Reports</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Forms <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/covid">Covid-19 Form</a></li>
                                        <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/poc">POC User Guide</a></li>
                                        <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/eid_req">EID Form</a></li>

                                        <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/vl_req">VL Form</a></li>
                                        <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/remotelogin">Remote Login SOP</a></li>
                                    </ul>
                                </li>
                                <!-- <li class="">
                                    <a href="#">EID Reports</a>
                                </li>
                                <li class="">
                                    <a href="#">VL Reports</a>
                                </li> -->
                            @elseif(Auth::user()->user_type_id == 2)
                                <li>
                                    <a href="{{ url('home') }}">Home</a>
                                </li>
                                <li>
                                    <a href="{{ url('user') }}">Users</a>
                                </li>
                                <li>
                                    <a href="{{ url('facility') }}">Facilities</a>
                                </li>
                                <li>
                                    <a href="{{ url('facility/lab') }}">Lab Facilities</a>
                                </li>
                                <li class=""><a class="dropdown-item" href="{{ url('facility/noCode') }}">Facilities without Code</a></li>
                            @elseif(in_array(Auth::user()->user_type_id, [12, 15]))
                                <li class="">
                                    <a href="{{ url('covid_sample/cif') }}">CIF Samples</a>
                                </li>
                                <li class="">
                                    <a href="{{ url('covid_sample/jitenge') }}">Jitenge Samples</a>
                                </li>
                            @else
                                @if(!Session('pendingTasks') || env('APP_LAB') == 2)

                                    <li class=""> <a href="{{ url($widgets['prefix'] . 'batch') }}">Samples</a> </li>
                                    <li class=""> <a href="{{ url($widgets['prefix'] . 'worksheet') }}">Worksheets</a> </li>
                                    <li class=""> <a href="{{ url($widgets['prefix'] . 'batch/index/1') }}">Dispatched Results</a> </li>

                                    <li class="dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Facilities <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li class=""><a class="dropdown-item" href="{{ url('facility') }}">Facilities</a></li>
                                            <li class=""><a class="dropdown-item" href="{{ url('facility/lab') }}">Lab Facilities</a></li>
                                            <li class=""><a class="dropdown-item" href="{{ url('facility/noCode') }}">Facilities without Code</a></li>
                                            @if(env('APP_LAB') == 5)
                                            <li class=""><a class="dropdown-item" href="{{ url('district') }}">Subcounties</a></li>
                                            @endif
                                        </ul>
                                    </li>

                                    <li class="">
                                        <a href="{{ route('reports') }}">Reports</a>
                                    </li>
                                    <li class="">
                                        <a href="{{ route('dashboard') }}">Dashboard</a>
                                    </li>

                                    <li class="">
                                        @if(env('APP_LAB') != 7 && in_array(session('testingSystem'), ['EID', 'Viralload']))
                                            <a href="{{ url('reports/kits') }}">Kits
                                            <span class="label label-{{ $widgets['get_badge']($widgets['rejectedAllocations']) }}">
                                            {{ $widgets['rejectedAllocations'] }}
                                            </span>
                                            </a>
                                        @endif
                                    </li>

                                    @if(Auth::user()->user_type_id == 0)
                                        <li>
                                            <a href="{{ url('user') }}">Users</a>
                                        </li>
                                    @endif
                                @endif
                            @endif
                        @else
                            <li class="">
                                <a href="{{ url('pending') }}">Pending Tasks</a>
                            </li>
                        @endif
                    @endif
                @endif
                    <li>
                        <a class="" href="{{ url('/logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Mobile View -->

        <!-- Full PC View -->
        <div class="navbar-right">
            <ul class="nav navbar-nav no-borders">
                @if(in_array(Auth::user()->user_type_id, [0,1,4]))
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Help Desk <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/help_desk">SOP</a></li>
                        <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/help_desk_video">SOP Video</a></li>
                        <li class=""><a class="dropdown-item" target="_blank" href="https://dataposit.mojohelpdesk.com">Submit Query</a></li>
                        <li class=""><a class="dropdown-item" href="http://127.0.0.1:5500/HealthITChuka/Form.html">Service desk </a></li>
                       </ul>
                </li>
                @endif
            @if(!in_array(Auth::user()->user_type_id, [7,8,10,11]))
                @if(Session('testingSystem') == 'CD4')
                    <li class="">
                        <a class="label-menu-corner" href="{{ url('home') }}">
                        <i class="pe-7s-home" style="font-size: 25px;"></i>
                            <span class="label label-danger"></span>
                        </a>
                    </li>
                    <li class="">
                        <a href="{{ url('cd4/sample') }}">Samples</a>
                    </li>
                    <li class="">
                        <a href="{{ url('cd4/worksheet') }}">Worksheets</a>
                    </li>
                    <li class="">
                        <a href="{{ url('cd4/sample/dispatch/2') }}">Results List</a>
                    </li>
                    <li class="">
                        <a href="{{ url('cd4/reports') }}">Reports</a>
                    </li>
                    <li class="">
                        <a href="{{ url('home') }}">Dashboard</a>
                    </li>
                @elseif(Session('testingSystem') == 'Covid')
                    <li class="">
                        <a href="{{ url('covid_sample') }}">Samples</a>
                    </li>
                    <li class="">
                        <a href="{{ url('covid_sample/cif') }}">CIF Samples</a>
                    </li>
                    <li class="">
                        <a href="{{ url('covid_sample/jitenge') }}">Jitenge Samples</a>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Traveller's Corner <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class=""><a class="dropdown-item" href="{{ url('traveller') }}">Samples</a></li>
                            <li class=""><a class="dropdown-item" href="{{ url('traveller/create') }}">Upload</a></li>
                        </ul>
                    </li>
                    <li class="">
                        <a href="{{ url('covid_worksheet') }}">Worksheets</a>
                    </li>
                    <li class="">
                        <a href="{{ url('covid_pool') }}">Pools</a>
                    </li>
                    <li class="">
                        <a href="{{ url('covid_sample/index/2') }}">Results List</a>
                    </li>
                    <li class="">
                        <a href="{{ url('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="">
                        <a href="{{ url('covidreports') }}">Reports</a>
                    </li>
                    <li class="">
                        <a href="{{ url('covidkits/reports') }}">Kits</a>
                    </li>
                @elseif(session('testingSystem') == 'DR')
                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Dashboard <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- <li class=""><a class="dropdown-item" href="{{ url('dr_dashboard') }}">Dashboard</a></li> -->
                            <!-- <li class=""><a class="dropdown-item" href="{{ url('dr_testing') }}">Testing Dashboard</a></li> -->
                            <li class=""><a class="dropdown-item" href="{{ url('dr_waterfall') }}">Main Dashboard</a></li>
                            <li class=""><a class="dropdown-item" href="{{ url('dr_formulation') }}">Formulation Resistance</a></li>
                            <li class=""><a class="dropdown-item" href="{{ url('dr_mutation') }}">Mutation Frequency</a></li>
                        </ul>
                    </li>
                    @if(!in_array(auth()->user()->user_type_id, [20]))
                    @if(env('APP_LAB') == 1)
                        <li class="">
                            <a href="{{ url('viralsample/potential_dr') }}">Potential Samples</a>
                        </li>
                    @endif
                    <li class="">
                        <a href="{{ url('dr_extraction_worksheet') }}">Extraction Worksheets</a>
                    </li>
                    <li class="">
                        <a href="{{ url('dr_worksheet') }}">Sequencing Worksheets</a>
                    </li>
                    <li class="">
                        <a href="{{ url('dr_sample/index/1') }}">Completed Results</a>
                    </li>
                    @if(auth()->user()->is_admin)
                        <li>
                            <a href="{{ url('user') }}">Users</a>
                        </li>
                        <li>
                            <a href="{{ url('facility') }}">Facilities</a>
                        </li>
                    @endif
                    <li class="">
                        <a href="{{ url('reports') }}">Report</a>
                    </li>     
                    @endif                  
                @elseif(session('testingSystem') == 'HPV')
                    <li class="">
                        <a class="label-menu-corner" href="{{ url('home') }}">
                        <i class="pe-7s-home" style="font-size: 25px;"></i>
                            <span class="label label-danger"></span>
                        </a>
                    </li>                    
                    <li class="">
                        <a href="{{ url('cancersample') }}">Samples</a>
                    </li>
                    <li class="">
                        <a href="{{ url('cancerworksheet') }}">Worksheets</a>
                    </li>
                    <li class="">
                        <a href="{{ url('cancersample/index/true') }}">Dispatched Results</a>
                    </li>
                    <li class="">
                        <a href="{{ url('reports') }}">Reports</a>
                    </li>
                    <li class="">
                        <a href="{{ url('dashboard') }}">Dashboard</a>
                    </li>
                @else
                    @if(!Session('pendingTasks') || env('APP_LAB') == 2)
                        @if (Auth::user()->user_type_id == 5)
                            @if(in_array('APP_LAB', [1, 3]))
                                <li class="">
                                    <a href="{{ url('covid_sample') }}">Covid Samples</a>
                                </li>
                                <li class="">
                                    <a href="{{ url('covid_sample/index/2') }}">Covid Results</a>
                                </li>
                            @endif

                            <li class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Samples <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class=""><a class="dropdown-item" href="{{ url('batch') }}">EID</a></li>
                                    <li class=""><a class="dropdown-item" href="{{ url('viralbatch') }}">VL</a></li>
                                </ul>
                            </li>

                            <li class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Results <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class=""><a class="dropdown-item" href="{{ url('batch/index/1') }}">EID</a></li>
                                    <li class=""><a class="dropdown-item" href="{{ url('viralbatch/index/1') }}">VL</a></li>
                                </ul>
                            </li>

                            <li class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    POC Samples <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class=""><a class="dropdown-item" href="{{ url('sample/list_poc') }}">EID</a></li>
                                    <li class=""><a class="dropdown-item" href="{{ url('viralsample/list_poc') }}">VL</a></li>
                                </ul>
                            </li>


                            <li class="">
                                <a href="{{ url('cancersample') }}">HPV Samples</a>
                            </li>
                            <li class="">
                                <a href="{{ url('worklist') }}">POC Worklists</a>
                            </li>
                            <li class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    SMS Log <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class=""><a class="dropdown-item" href="{{ url('sample/sms_log') }}">EID SMS Log</a></li>
                                    <li class=""><a class="dropdown-item" href="{{ url('viralsample/sms_log') }}">VL SMS Log</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Reports <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class=""><a class="dropdown-item" href="{{ url('facility/reports/EID') }}">EID Reports</a></li>
                                    <li class=""><a class="dropdown-item" href="{{ url('facility/reports/VL') }}">VL Reports</a></li>
                                    <li class=""><a class="dropdown-item" href="{{ url('facility/reports/HPV') }}">HPV Reports</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Forms <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/covid">Covid-19 Form</a></li>
                                    <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/poc">POC User Guide</a></li>
                                    <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/eid_req">EID Form</a></li>
                                    
                                    <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/vl_req">VL Form</a></li>
                                    <li class=""><a class="dropdown-item" href="https://eiddash.nascop.org/download/remotelogin">Remote Login SOP</a></li>
                                </ul>
                            </li>
                            <!-- <li class="">
                                <a href="#">EID Reports</a>
                            </li>
                            <li class="">
                                <a href="#">VL Reports</a>
                            </li> -->
                        @elseif(Auth::user()->user_type_id == 2)
                                <li>
                                    <a href="{{ url('home') }}">Home</a>
                                </li>
                                <li>
                                    <a href="{{ url('user') }}">Users</a>
                                </li>
                                @if(env('APP_LAB') == 1)
                                    <li>
                                        <a href="{{ url('covid_sample') }}">Covid</a>
                                    </li>
                                @endif
                                <li>
                                    <a href="{{ url('facility') }}">Facilities</a>
                                </li>
                                <li>
                                    <a href="{{ url('facility/lab') }}">Lab Facilities</a>
                                </li>
                                <li>
                                    <a href="{{ url('sample/transfer_samples') }}">Transfer EID Samples</a>
                                </li>
                                <li>
                                    <a href="{{ url('viralsample/transfer_samples') }}">Transfer VL Samples</a>
                                </li>
                                <li>
                                    <a href="{{ url('covid_sample/transfer_samples') }}">Transfer Covid Samples</a>
                                </li>
                        @elseif(in_array(Auth::user()->user_type_id, [12, 15]))
                            <li class="">
                                <a href="{{ url('covid_sample/cif') }}">CIF Samples</a>
                            </li>
                            <li class="">
                                <a href="{{ url('covid_sample/jitenge') }}">Jitenge Samples</a>
                            </li>


                        @else
                            <li class="">
                                <a class="label-menu-corner" href="{{ url('home') }}">
                                <i class="pe-7s-home" style="font-size: 25px;"></i>
                                    <span class="label label-danger">
                                    @isset($widgets['pendingSamples'])
                                        @if(session('testingSystem') == 'Viralload')
                                            {{ $widgets['pendingSamples']['all']+$widgets['batchesForApproval']+$widgets['batchesNotReceived']+$widgets['batchesForDispatch']+$widgets['samplesForRepeat']+$widgets['rejectedForDispatch']+$widgets['emrForApproval'] }}
                                        @else
                                            {{ $widgets['pendingSamples']+$widgets['batchesForApproval']+$widgets['batchesForDispatch']+$widgets['samplesForRepeat']+$widgets['rejectedForDispatch']+$widgets['emrForApproval'] }}
                                        @endif
                                    @endisset
                                    </span>
                                </a>
                            </li>

                            <li class=""> <a href="{{ url($widgets['prefix'] . 'batch') }}">Samples</a> </li>
                            <li class=""> <a href="{{ url($widgets['prefix'] . 'worksheet') }}">Worksheets</a> </li>
                            <li class=""> <a href="{{ url($widgets['prefix'] . 'batch/index/1') }}">Dispatched Results</a> </li>

                            <li class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Facilities <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class=""><a class="dropdown-item" href="{{ url('facility') }}">Facilities</a></li>
                                    <li class=""><a class="dropdown-item" href="{{ url('facility/lab') }}">Lab Facilities</a></li>
                                    <li class=""><a class="dropdown-item" href="{{ url('facility/noCode') }}">Facilities without Code</a></li>
                                    @if(env('APP_LAB') == 5)
                                    <li class=""><a class="dropdown-item" href="{{ url('district') }}">Subcounties</a></li>
                                    @endif
                                </ul>
                            </li>

                            <li class="">
                                <a href="{{ route('reports') }}">Reports</a>
                            </li>

                            <li class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Dashboard <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class=""><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                                    <li class=""><a class="dropdown-item" href="
                                    @if(session('testingSystem') == 'Viralload')
                                        {{ 'https://viralload.nascop.org/lab' }}
                                    @else
                                        {{ 'https://eid.nascop.org/labPerformance' }}
                                    @endif">Nascop Dashboard</a></li>
                                </ul>
                            </li>

                            @if(env('APP_LAB') != 7 && in_array(session('testingSystem'), ['EID', 'Viralload']))
                            <li class="">                   <a href="{{ url('reports/kits') }}">Kits
                                <span class="label label-{{ $widgets['get_badge']($widgets['rejectedAllocations']) }}">
                                {{ $widgets['rejectedAllocations'] }}
                                </span></a>
                            </li>
                            @endif
                            @if(Auth::user()->user_type_id == 0)
                                <li>
                                    <a href="{{ url($widgets['prefix'] . 'sample/transfer_samples') }}">Transfer</a>
                                </li>
                                <li>
                                    <a href="{{ url('user') }}">Users</a>
                                </li>
                            @endif
                        @endif
                    @else
                        <li class="">
                            <a href="{{ url('pending') }}">Pending Tasks</a>
                        </li>
                    @endif
                @endif
            
            @endif
                <li class="dropdown">
                        
                    <a href="{{ url('/logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"
                         style="font-size: 25px;"
                    >
                        <i class="pe-7s-upload pe-rotate-90"></i>
                        <!-- <i class="fa fa-sign-out"></i> Log out -->

                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

                </li>
            </ul>
        </div>
        <!-- Full PC View -->
    </nav>
</div>