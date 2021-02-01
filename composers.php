<?php

view()->composer(['blog::admin.posts.create', 'blog::admin.posts.edit','blog::admin.categories.create', 'blog::admin.categories.edit'], \Modules\Blog\Composers\TemplateViewComposer::class);
