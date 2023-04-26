
                        <div class="form-group" @if(isset($form_div)) id="{{ $form_div }}"  @endif >
                            <label class="col-sm-3 control-label"> {{ $label }}
                                @if($required)
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                @endif
                            </label>
                            <div class="col-sm-9">
                                <select class="form-control @if($required) requirable @endif " 
                                    @if($required) required @endif

                                    @if(isset($disabled)) disabled @endif

                                        name="{{ $attr }}" id="{{ $attr }}">
                                    <option></option>
                                    @foreach ($drops as $row)
                                        <option value="{{ $row->id }}"

                                        @if (isset($model) && $model && $model->$attr == $row->id)
                                            selected
                                        @endif

                                        > {{ $row->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>