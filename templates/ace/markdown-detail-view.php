<!DOCTYPE html>
<?php
$route = Bootstrap::route2file(urldecode($_SERVER['REQUEST_URI']));
$baseUrl = preg_replace('#markdown#', '', Bootstrap::getProjectByRoute($route));
?>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?= $title ?> - <?= Bootstrap::DOC_NAME ?></title>

    <link rel="stylesheet" type="text/css" href="/static/bootstrap/css/bootstrap.min.css" />
<!--    <link href="/static/ace/css/bootstrap.min.css" rel="stylesheet" />-->
    <link rel="stylesheet" href="/static/ace/css/font-awesome.min.css" />

    <!-- page specific plugin styles -->

    <!-- ace styles -->

    <link rel="stylesheet" href="/static/ace/css/ace.min.css" />
    <!-- inline styles related to this page -->
    <link rel="stylesheet" type="text/css" href="/static/base.css" />


    <!-- ace settings handler -->

<!--    <script src="/static/ace/js/ace-extra.min.js"></script>-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

    <!--[if lt IE 9]>
    <script src="/static/ace/js/html5shiv.js"></script>
    <script src="/static/ace/js/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<nav class="navbar navbar-inverse navbar-static-top top-navbar header-color-black" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="/"><?= Bootstrap::DOC_NAME ?></a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapses navbar-collapses">
            <ul class="nav navbar-nav">
                <?php foreach (DirectoryIndex::getProjects() as $project) { ?>
                    <li><a href="<?= $project['link'] ?>"><?= $project['name'] ?></a></li>
                <?php } ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if (isset($editUrl)) { ?><li><a href="<?= $editUrl?>">编辑</a></li><?php } ?>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div>
</nav>
<header class="jumbotron subhead">
    <div class="container">
        <h1><small>Demo</small></h1>
    </div>
</header>

<div class="container content">
    <div class="row">
        <div class="col-sm-3">
            <div class="widget-box">
                <div class="widget-header header-color-green2 header-color-sblue">
                    <h4 class="lighter smaller">目录</h4>
                </div>

                <div class="widget-body">
                    <div class="widget-main padding-8">
                        <div id="tree2" class="tree"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-9">
            <!--文档中文内容 start-->
            <?= $content ?>
            <!--文档中文内容 end-->
        </div>
    </div>
</div>
<footer class="footer">
    <div class="container">
        <p class="pull-right">
            <a href="#">Back to top</a>
        </p>
        <ul class="footer-links">
            <li><a href="http://walden.huamanshu.com" target="_blank">walden主页</a></li>
            <li><a href="https://github.com/meolu/walden" target="_blank">walden源码</a></li>
            <li><a href="https://github.com/meolu/walden/issues?state=open" target="_blank">提交bug</a></li>
        </ul>
    </div>
</footer>
<!-- basic scripts -->

<!--[if !IE]> -->

<script type="text/javascript">
    window.jQuery || document.write("<script src='/static/ace/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
</script>

<!-- <![endif]-->

<!--[if IE]>
<script type="text/javascript">
    window.jQuery || document.write("<script src='/static/ace/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

<script type="text/javascript">
    if("ontouchend" in document) document.write("<script src='/static/ace/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
</script>
<script src="/static/ace/js/bootstrap.min.js"></script>
<!--<script src="/static/ace/js/typeahead-bs2.min.js"></script>-->

<!-- page specific plugin scripts -->

<script src="/static/ace/js/fuelux/fuelux.tree.min.js"></script>

<!-- ace scripts -->

<script src="/static/ace/js/ace-elements.min.js"></script>
<script src="/static/ace/js/ace.min.js"></script>

<!-- inline scripts related to this page -->

<script type="text/javascript">
    jQuery(function($){
        var format = function (o) {
            var list = [];
            $.each(o, function(k, v) {
                var item = v;
                if (item.type == 'folder') {
                    item.additionalParameters = {'children': format(item.children)};
                } else {
                    item.name = '<i class="icon-file-text"></i><a href="' + item.link + '">' + item.name + '</a>'
                }
               list.push(item)
            })
            return list;
        }


        $.get('<?= $baseUrl ?>?recourse=1', function(o) {
            var treeData = format(o.data);
            console.log(treeData);
            var DataSourceTree = function(options) {
                this._data 	= options.data;
                this._delay = options.delay;
            }

            DataSourceTree.prototype.data = function(options, callback) {
                var self = this;
                var $data = null;

                if(!("name" in options) && !("type" in options)){
                    $data = this._data;//the root tree
                    callback({ data: $data });
                    return;
                }
                else if("type" in options && options.type == "folder") {
                    if("additionalParameters" in options && "children" in options.additionalParameters)
                        $data = options.additionalParameters.children;
                    else $data = {}//no data
                }

                if($data != null)//this setTimeout is only for mimicking some random delay
                    setTimeout(function(){callback({ data: $data });} , parseInt(Math.random() * 500) + 200);

            };
            var treeDataSource = new DataSourceTree({data: treeData});
            $('#tree2').ace_tree({
                dataSource: treeDataSource ,
                loadingHTML:'<div class="tree-loading"><i class="icon-refresh icon-spin blue"></i></div>',
                'open-icon' : 'icon-folder-open',
                'close-icon' : 'icon-folder-close',
                'selectable' : false,
                'selected-icon' : null,
                'unselected-icon' : null
            });
        })

        <?php if (isset($_GET['action']) && urldecode($_GET['action']) == Bootstrap::PUSH_GIT_URL) { ?>
        // 是否为编辑后的第一次文档预览，需要推送到git
        $.get('<?= Bootstrap::PUSH_GIT_URL ?>', function (o) {
            console.log(o);
        })
        <?php } ?>
    });

    // 统计
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?5980089b1455e9e015256741d0ab0b2e";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>

</body>
</html>
