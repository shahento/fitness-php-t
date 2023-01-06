<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
        <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
        <title>
            Password Reset
        </title>
    </head>
    <body>
        <div class="container">
            <div id="passwordreset" style="margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                <div class="panel panel-success">
                    <div class="panel-heading" style="background: #fff;text-align: center;justify-content: space-between;align-items: center;">
                    <!-- <div class="" style="text-align:center; border-bottom:1px solid #f0f5f8; padding-bottom:15px; margin-bottom: 40px;"> -->
                        <img src="{{ asset('images/gymble_logo.png') }}" width="250">
                    <!-- </div> -->
                    </div>                     
                    <div class="panel-body">
                        @if($data['success'])
                            <h3>You have successfully changed password.</h3>
                            <br>
                        @else
                            <h3>Sorry! This token is no more valid.</h3>
                            <br>
                        @endif
                    </div>
                </div>
            </div>             
        </div>
    </body>
</html>