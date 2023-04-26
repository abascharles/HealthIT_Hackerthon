

                                    @php

                                        if(in_array(env('APP_LAB'), $double_approval) && $worksheet->status_id == 2){

                                            if($sample->has_rerun){
                                                $class = 'noneditable';
                                            }
                                            else{
                                                $class = 'editable';
                                            }


                                            
                                            /*if($sample->repeatt == 1){
                                                // $class = 'noneditable';
                                                $class = 'editable';
                                            }
                                            else{
                                                $class = 'editable';
                                            }*/
                                        }

                                    @endphp

                                    <tr>
                                        <td> 
                                            {{ $sample->patient->patient }}  
                                            <input type="hidden" name="samples[]" value="{{ $sample->id }}" class="{{ $class }}">
                                            <input type="hidden" name="batches[]" value="{{ $sample->batch_id }}" class="{{ $class }}">
                                            <input type="hidden" name="results[]" value="{{ $sample->result }}" class="{{ $class }}">
                                            <input type="hidden" name="interpretations[]" value="{{ $sample->interpretation }}" class="{{ $class }}">
                                        </td>
                                        <td> {{ $sample->id }}  </td>
                                        <td> {{ $sample->run }} </td>
                                        <td> {{ $sample->interpretation }} </td>


                                        <td> 
                                            <select class="dilutionfactor {{ $class }} dilution-{{ $sample->dilutionfactor }}" name="dilutionfactors[]">
                                                @foreach($dilutions as $dilution)
                                                    <option value="{{$dilution->dilutionfactor }}"
                                                        @if($sample->dilutionfactor == $dilution->dilutionfactor    )
                                                            selected="selected"
                                                        @endif
                                                        > {{ $dilution->dilutiontype }} </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td> 
                                            {!! $sample->coloured_result !!}
                                        </td>

                                        <td>
                                            @if(!$sample->approvedby)
                                                <div><label> <input type="checkbox" class="i-checks {{ $class }}"  name="redraws[]" value="{{ $sample->id }}"> Collect New Sample </label></div>
                                            @endif                                            
                                        </td>

                                        <td> 
                                            @if($sample->approvedby)
                                                @foreach($actions as $action)
                                                    @if($sample->repeatt == $action->id)
                                                        {!! $action->name_colour !!}
                                                    @endif
                                                @endforeach

                                            @else
                                                <select name="actions[]" class="{{ $class }}">
                                                    <option>Choose Action</option>
                                                    @foreach($actions as $action)
                                                        <option value="{{$action->id}}"
                                                            @if($sample->repeatt == $action->id)
                                                                selected
                                                            @endif
                                                            > {{ $action->name }} </option>
                                                    @endforeach
                                                </select>

                                            @endif
                                        </td>
                                        
                                        @if(in_array(env('APP_LAB'), $double_approval))
                                            <td> {{ $sample->dateapproved2 }} </td>
                                            <td> {{ $sample->final_approver->full_name ?? '' }} </td>
                                        @else
                                            <td> {{ $sample->dateapproved }} </td>
                                            <td> {{ $sample->approver->full_name ?? '' }} </td>
                                        @endif
                                        
                                        <td> 
                                            <a href="{{ url('viralsample/' . $sample->id) }}" title='Click to view Details' target='_blank'> Details</a> | 
                                            <a href="{{ url('viralsample/runs/' . $sample->id) }}" title='Click to View Runs' target='_blank'>Runs </a>  
                                        </td>
                                    </tr>