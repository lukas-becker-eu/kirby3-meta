<?php

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('lukasbecker/kirby3-meta', [
  'blueprints' => [
    'fields/meta' => __DIR__ . '/blueprints/fields/meta.yml',
    'tabs/meta' => __DIR__ . '/blueprints/tabs/meta.yml'
  ],
  'snippets' => [
    'head-meta' => __DIR__ . '/snippets/meta.php'
  ],
  'pageMethods' => [
    'getMetaTitle' => function () {
      if($this->metaTitle()->isNotEmpty() || $this->title()->isNotEmpty()) {
        $title = '';
        $separator = ' | ';
        if ($this->metaTitle()->isNotEmpty()) {
          $title = Str::unhtml($this->metaTitle()->kt());
        }
        else {
          if (!$this->isHomePage()) {
            $title .= r($this->title()->isNotEmpty(), $this->title() . $separator);

            foreach ($this->parents() as $p) {
              $title .= r($p->title()->isNotEmpty(), $p->title() . $separator) ;
            }
          }

          $title .= preg_replace('#^[^:/.]*[:/]+#i', '', preg_replace('{/$}', '', urldecode($this->site()->url())));
        }
        return Html::tag('title', $title);
      }
    },
    'getMetaDescription' => function () {
      $description = $this->metaDescription();
      if($description->isNotEmpty()) {
        $description = Str::unhtml($description->kt());
        return Html::tag('meta', null, ['name' => 'description', 'content' => $description]);
      }
    },
    'getMetaRobots' => function () {
      $index = 'index';
      $noindex = 'noindex';
      $follow = 'follow';
      $nofollow = 'nofollow';
      $robots = [];
      if ($this->metaRobotsIndex()->isNotEmpty()) {
        if ($this->metaRobotsIndex()->value() === $index) {
          array_push($robots, $index);
        } else {
          array_push($robots, $noindex);
        }
      } else {
        array_push($robots, $noindex);
      }
      if ($this->metaRobotsFollow()->isNotEmpty()) {
        if ($this->metaRobotsFollow()->value() === $follow) {
          array_push($robots, $follow);
        } else {
          array_push($robots, $nofollow);
        }
      } else {
        array_push($robots, $nofollow);
      }
      $robots = implode(", ", $robots);
      return Html::tag('meta', null, ['name' => 'robots', 'content' => $robots]);
    },
  ]
]);
