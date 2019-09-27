@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1>@KreszBOT - Adminisztráció és statisztika</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fa fa-user-friends"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">Regisztrált felhasználók</span>
            <span class="info-box-number"><?= $stats['registeredUsers'] ?><small> db</small></span>
        </div>
        <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
        <span class="info-box-icon bg-blue"><i class="fa fa-question-circle"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">Megválaszolt kérdések</span>
            <span class="info-box-number"><?= $stats['answeredQuestions'] ?><small> db</small></span>
        </div>
        <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix visible-sm-block"></div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
        <span class="info-box-icon bg-red"><i class="fa fa-question-circle"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">Helytelenül megválaszolt</span>
            <span class="info-box-number"><?= $stats['incorrectQuestions'] ?><small> db</small></span>
        </div>
        <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
        <span class="info-box-icon bg-green"><i class="fa fa-question-circle"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">Helyesen megválaszolt</span>
            <span class="info-box-number"><?= $stats['correctQuestions'] ?><small> db</small></span>
        </div>
        <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
</div>
@stop