<!DOCTYPE html>
<html language="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="<?= htmlspecialchars($this->keywords)?>" />
    <meta name="description" content="<?=htmlspecialchars($this->description)?>" />
    <?= $assets->getAssets(); ?>
    <title><?=$this->pageTitle?></title>
</head>
<body>
<div id="page">
    <?= $flashMessenger->get('error'); ?>
    <?= $this->getContent() ?>
</div>
</body>
</html>
