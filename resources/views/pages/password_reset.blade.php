<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
        <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
        <title>
            Reset Gymble Account Password
        </title>
    </head>
    <body>
        <div class="container">
            <div id="passwordreset" style="max-width: 450px; margin: 50px auto 20px;" class="mainbox">
                <div class="panel {{$data['error'] ? 'panel-danger' : 'panel-default'}}">
                    <div class="panel-heading " style="background: #fff;text-align: center;justify-content: space-between;align-items: center;">
                        <div class="panel-title">
                            <!-- <img alt="YBLA Logo" style="width: 150px;" src="{{ env('API_ROOT_URL') . '/images/logo-white.png' }}"/> -->
                        </div>
                    </div>   
                    <h3 style="padding: 0px 15px;" class="text-center">Reset Gymble Account Password</h3> 
                    @if ($data['isValid'])                 
                    <div class="panel-body">
                        <form id="passwordChangeform" class="" role="form" method="post">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="token" value="{{$data['token']}}">
                            <input type="hidden" name="isValid" value="{{$data['isValid']}}">
                            <input type="hidden" name="user_type" value="{{$data['user_type']}}">

                            <div class="form-group">
                                <label style="white-space: nowrap;text-align: start !important;" >New password</label>
                                <input type="password" class="form-control" name="password" placeholder="Enter your new password">
                                
                            </div>
                            <div class="form-group">
                                <label style="white-space: nowrap;text-align: start !important;" >Confirm password</label>
                                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm your new password">
                                
                            </div>
                            <!-- <div style="display: flex; align-items: center; justify-content: space-between;"> -->
                                <div style="margin-right:5px;">
                                    <small>Password must contain at least 8 characters, including alphabets, numbers and special characters.</small>
                                </div>
                                <button id="btn-password-change" type="submit" class="btn btn-success" style="text-transform: uppercase;background-color: #004c84;padding: 12px 16px;border-color: #fff;font-weight: bold;">
                                    Update
                                </button>
                            <!-- </div>           -->
                        </form>
                        
                        @if ($data['error'])
                            <div class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                <span class="sr-only">Error:</span>
                                {{ $data['error'] }}
                            </div>
                        @endif

                    </div>
                    @endif
                    @if (!$data['isValid'])
                        <div class="alert alert-danger" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <span class="sr-only">Error:</span>
                            Invalid token.
                        </div>
                    @endif
                </div>
            </div>             
        </div>
    </body>
</html>