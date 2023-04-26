		<?php 
			$prop2 = $prop2 ?? null;
			$is_required = false;
			if((isset($required) && $required) || (isset($facility_required) && !auth()->user()->is_covid_lab_user()) ) $is_required = true;
		 ?>
		<div class="form-group {{ $form_class ?? '' }} ">
			<label class="col-sm-4 control-label">{{ $label }}
                @if($is_required)
                    <strong><span style='color: #ff0000;'>*</span></strong>
                @endif
			</label>
			<div class="col-sm-8">
				<input class="form-control {{ $is_required ? 'requirable' : '' }}" type="{{ $input_type ?? 'text' }}" 
					name="{{ $prop }}" 
					id="{{ $prop }}" 
					value="{{ $default_val ?? $model->$prop ?? '' }}" 
                    @if($is_required)
                        required                                 
                    @endif
					{!! $attributes ?? null !!} 
					@isset($is_number) number='number' @endisset 
					@isset($placeholder) placeholder="{{ $placeholder }}" @endisset 
				>
			</div>
		</div>