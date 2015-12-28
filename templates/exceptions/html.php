<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Error dumper</title>
    <link href="data:image/x-icon;base64,AAABAAEAEBAQAAEABAAoAQAAFgAAACgAAAAQAAAAIAAAAAEABAAAAAAAgAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAVFFOAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABEAABEAAAABEAAAARAAAAEQAAABEAAAARAAAAEQAAABEAAAARAAABEAAAAAEQABEAAAAAABEAEQAAAAAAEQABEAAAAAEQAAARAAAAEQAAABEAAAARAAAAEQAAABEAAAARAAAAEQAAAAEQAAEQAAAAAAAAAAAAD//wAA888AAOfnAADn5wAA5+cAAOfnAADP8wAAn/kAAJ/5AADP8wAA5+cAAOfnAADn5wAA5+cAAPPPAAD//wAA" rel="icon" type="image/x-icon" />
    <?php foreach ($data['__static']['css'] as $file) { ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $file ?>" />
    <?php } ?>
    <style type="text/css">
        .error-line
        {
            font-weight: bold;
            color: red;
            font-style: italic;
        }
        .code-box
        {
            overflow: auto;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
        pre, pre *
        {
            word-wrap: normal !important;
        }
        pre
        {
            margin-bottom: 0;
        }
        pre:last-child
        {
            margin-bottom: 0;
        }
        .panel-group .panel-heading a:after
        {
            font-family:'Glyphicons Halflings';
            content:"\e114";
            float: right;
            color: grey;
        }
        .panel-group .panel-heading a.collapsed:after
        {
            content:"\e080";
        }
        a
        {
            cursor: pointer;;
        }
        .nav-tabs
        {
            border-bottom: 0;
        }
        .nav-tabs > li:hover > a
        {
            border-bottom: 0;
        }
        .panel-collapse .panel-body
        {
            padding: 0;
        }
        .panel-collapse pre
        {
            border: 0;
            background: #fff;
        }
        .max-height
        {
            max-height: 400px;
            overflow: auto;
        }
        .panel-group:last-child
        {
            margin-bottom: 0;
        }
        pre.sf-dump
        {
            color: inherit !important;
            background: #f8f8f8 !important;
        }
        pre.sf-dump .sf-dump-private
        {
            color: #333 !important;
        }
        pre.sf-dump .sf-dump-str
        {
            color: #808080 !important;;
        }
        pre.sf-dump .sf-dump-protected
        {
            color: #999 !important;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Error!</h1>

    <h2>Message</h2>
    <pre><strong><?php echo $data['exceptionClass']; ?></strong><?php echo isset($data['message']) ? ' ' . $data['message'] : '' ?></pre>

    <h2>Backtrace</h2>
    <?php foreach ($data['trace'] as $step) { ?>
    <div class="panel panel-default">
        <div class="panel-heading"><?php echo $step['title'] ?></div>

        <div class="panel-body">
            <?php if (!empty($step['arguments'])) { ?>
            <div class="padding-menu">
                <ul class="nav nav-tabs" data-tabs="tabs-<?php echo $step['key'] ?>">
                    <li class="active">
                        <a href="#source-<?php echo $step['key'] ?>" data-toggle="tab">Source</a>
                    </li>
                    <li>
                        <a href="#colorized-key-<?php echo $step['key'] ?>" data-toggle="tab">Arguments</a>
                    </li>
                </ul>
            </div>
            <?php } ?>

            <div>
                <div class="tab-content">
                    <div class="tab-pane active in fade" id="<?php if (!empty($step['key'])) { ?>source-<?php echo $step['key']; } ?>">
                        <pre class="<?php echo !empty($step['key']) ? 'code-box' : '' ?> max-height"><?php echo $step['source'] ?></pre>
                    </div>

                    <?php if (!empty($step['arguments'])) { ?>
                    <div class="tab-pane fade" id="colorized-key-<?php echo $step['key'] ?>">
                        <div class="panel-group">
                            <?php foreach ($step['arguments'] as $keyParam => $param) { ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <a data-toggle="collapse" data-target="#parameters-<?php echo $step['key'] ?>-<?php echo $keyParam ?>" class="<?php echo $param['full'] ?: 'collapsed' ?>"><?php echo $param['name'] ?></a>
                                </div>
                                <div id="parameters-<?php echo $step['key'] ?>-<?php echo $keyParam ?>" class="panel-collapse <?php echo $param['full'] ? 'collapse in' : 'collapse' ?>">
                                    <div class="panel-body">
                                        <?php echo $param['dump']; ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<?php foreach ($data['__static']['js'] as $file) { ?>
    <script type="text/javascript" src="<?php echo $file ?>"></script>
<?php } ?>
<?php echo \ErrorDumper\Dumpers\Html::TAG_HTML ?>
</body>
</html>
