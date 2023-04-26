
                                <div>
                                    <label> 
                                        <input name="{{ $field }}" type="radio" class="i-checks" required
                                            value="1"

                                            @if(isset($obj) && $obj->$field)
                                                checked="checked"
                                            @endif 
                                        /> 
                                        Yes
                                    </label>
                                </div>

                                <div>
                                    <label> 
                                        <input name="{{ $field }}" type="radio" class="i-checks" required
                                            value="0"

                                            @if(isset($obj) && !$obj->$field)
                                                checked="checked"
                                            @endif 
                                        /> 
                                        No
                                    </label>
                                </div>  

                                {{ $slot ?? '' }}       