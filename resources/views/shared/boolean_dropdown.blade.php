
                                <select class="form-control requirable" name="{{ $field }}" id="{{ $field }}" required>
                                    <option value=""> Select One </option>
                                    <option value=1
                                        @if(isset($obj) && $obj->$field == 1)
                                            selected
                                        @endif
                                        > Yes </option>
                                    <option value=0
                                        @if(isset($obj) && !$obj->$field)
                                            selected
                                        @endif
                                        > No </option>    
                                    <option value='-1'
                                        @if(isset($obj) && !$obj->$field == -1)
                                            selected
                                        @endif
                                        > No Data </option>                                     
                                </select> 

                                {{ $slot ?? '' }}       