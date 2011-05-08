<?php $data = YumUser::model()->findByPk($data->user_id); ?>
<?php $this->renderPartial('application.modules.user.views.user._view', array('data' => $data)); ?>

