<?php

namespace luckywp\wikiLinking\admin\controllers;

use luckywp\wikiLinking\core\admin\Controller;

class SettingsController extends Controller
{

    public function actionIndex()
    {
        $this->render('index');
    }
}
