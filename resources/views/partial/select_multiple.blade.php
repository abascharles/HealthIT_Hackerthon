				<?php
					if(!isset($prop2)) $prop2 = 'name';
				?>

				<div class="form-group {{ $form_class ?? '' }} ">
                    <label class="col-sm-4 control-label">  {{ $label }} (Multiple)
                        @if(isset($required) && $required)
                            <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                        @endif
                    </label>
					<div class="col-sm-8">
						<select class="form-control" id="{{ $prop }}" name="{{ $prop }}[]" multiple @isset($required) required @endisset> 
							<option></option>
							@foreach($items as $item)
								<option value="{{ $item->id }}" @if((isset($model) && $model->$prop && in_array($item->id, $model->$prop)) || (isset($default_val) && $default_val == $item->id)) selected @endif> {{ $item->$prop2 }} </option>
							@endforeach							
						</select>
					</div>			
				</div>