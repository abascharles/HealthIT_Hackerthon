                <?php 
                    $prop2 = $prop2 ?? null;
                    $is_required = false;
                    if((isset($required) && $required) || (isset($facility_required) && !auth()->user()->is_covid_lab_user()) ) $is_required = true;
                 ?>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">  {{ $label }}
                                @if($is_required)
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                @endif
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group date {{ $class ?? 'date-normal' }}">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" value="{{ $default_val ?? $model->$prop ?? '' }}" id="{{ $prop }}" name="{{ $prop }}" 
                                    @if($is_required)
                                        required  class="form-control requirable"
                                    @else
                                        class="form-control"
                                    @endif
                                    >
                                </div>
                            </div>                            
                        </div>