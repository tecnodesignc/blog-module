<?php

view()->composer(['blog::admin.posts.create', 'post::admin.posts.edit','blog::admin.categories.create', 'category::admin.categories.edit'], \Modules\Blog\Composers\TemplateViewComposer::class);
