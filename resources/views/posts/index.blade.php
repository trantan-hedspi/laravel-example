@extends('app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Home</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 form-inline">
                                <img src="{{asset('img/profile_img_default.jpg')}}" alt="picture" width="50" height="50">
                                <label>Tran Tan</label>
                            </div>
                            @foreach($posts as $post)
                                <div class="col-md-12 form-inline">
                                    <label>{{$post->title}}</label>
                                    <p>{{$post->content}}</p>
                                </div>
                                @endforeach
                            <div class="col-md-12 post-content">
                                <h4>My Post</h4>
                                <p> My content</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection