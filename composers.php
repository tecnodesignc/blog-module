<?php

view()->composer(['post::admin.create', 'post::admin.edit','category::admin.create', 'category::admin.edit'], \Modules\Blog\Composers\TemplateViewComposer::class);
