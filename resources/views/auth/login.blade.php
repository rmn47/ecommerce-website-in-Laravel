@extends('auth.layouts.authentication')

@section('content')

    @include('auth.'.get_setting('authentication_layout_select').'.admin_login')

@endsection

@section('script')
    <script type="text/javascript">
        function autoFillAdmin(){
            $('#email').val('admin@example.com');
            $('#password').val('123456');
        }
    </script>
@endsection
