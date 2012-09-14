<?php
Router::add('videos-pagina-<id:(d+)>.html', array('controller' => 'videos', 'action' => 'index', ':id'));
