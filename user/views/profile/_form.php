<?php
		foreach($profile->loadProfileFields() as $field) {
			echo CHtml::openTag('div',array('class'=>'row'));
			$attribute = !empty($tabularIdx) ? "[{$tabularIdx}]{$field->varname}" : $field->varname;
			echo CHtml::activeLabelEx($profile, $attribute);
			if ($field->field_type=="TEXT")
				echo CHtml::activeTextArea($profile, $attribute, array(
							'rows'=>6,
							'cols'=>50)
						);
			else
				echo CHtml::activeTextField($profile, $attribute, array(
							'size'=>60,
							'maxlength'=>(($field->field_size)?$field->field_size:255)));
			echo CHtml::error($profile, $attribute);
			if($field->hint)
				echo CHtml::tag('div',array('class'=>'hint'),$field->hint,true);
			echo CHtml::closeTag('div');
		}
