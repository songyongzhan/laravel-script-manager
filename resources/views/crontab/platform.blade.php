<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>脚本管理器</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
</head>
<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Login</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
            @endauth
        </div>
    @endif

    <div class="content" style="min-height: auto;">
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-4" style="cursor:pointer;" id="start">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-play"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Start</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-4 col-sm-4 col-xs-4" style="cursor:pointer;" id="stop">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-pause"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Stop</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-4 col-sm-4 col-xs-4" style="cursor:pointer;" id="restart">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-repeat"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">ReStart</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->

        </div>
    </div>

    <p>
        服务器运行状态：<span id="ws_server">.</span> 内存占用率：<span id="memory">0</span>%
    </p>


</div>

<script>

    $.get('/admin/crontabs/platformInfo', {}, function (data) {

        if (data.data.wsRun == 1) {
            $('#ws_server').addClass('bg-green').html('运行中');
        } else {
            $('#ws_server').addClass('bg-danger').html('已停止');
        }

        $('#memory').html(data.data.memory);

    });


    $("#stop").on('click', function () {

        $.get('/admin/crontabs/stop', {}, function (data) {
            console.log(data);
        });
    });

    $("#start").on('click', function () {

        $.get('/admin/crontabs/start', {}, function (data) {
            console.log(data);


        });
    });

    $("#restart").on('click', function () {

        $.get('/admin/crontabs/restart', {}, function (data) {
            if (data.code == 0) {
                alert("系统重新启动");
            } else {
                alert("系统重启失败");
            }
        });
    });


</script>


</body>
</html>
