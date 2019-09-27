@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
     <!-- <h1>@KreszBOT - Statisztika</h1> -->
@stop

@section('content')
<section class="invoice">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <i class="fa fa-file-invoice"></i> KreszBOT - Statisztika igazolás
            <small class="pull-right"><?php echo date('Y-m-d H:i') ?> </small>
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
          <address>
            <img style="max-width: 100px;" src="<?php echo $user->profilePic ?>"/><br>

          </address>
        </div>

        <div class="col-sm-4 invoice-col"><!--
          To
          <address>
            <strong>John Doe</strong><br>
            795 Folsom Ave, Suite 600<br>
            San Francisco, CA 94107<br>
            Phone: (555) 539-1037<br>
            Email: john.doe@example.com
          </address>-->
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          <b>Igazolás #<?php echo rand(1,1000) ?></b><br>
          <br>
          <b>Felhasználó:</b> <?php echo $user->lastName . ' ' . $user->firstName ?><br>
          <b>ID:</b> <?php echo $userId ?><br>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-striped">
            <thead>
            <tr>
              <th>Teszt/kategória</th>
              <th>Pontszám</th>
              <th>Eredmény</th>
              <th>Dátum</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach($tests as $test){ ?>
                    <tr>
                        <td>"<?php echo $test->category ?>" kategória</td>
                        <td> <?php echo rand(1,55) ?> / 55</td>
                        <td>Megfelelt</td>
                        <td><?php echo $test->createdAt ?></td>
                    </tr>
                <?php } ?>
            </tbody>
          </table>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-6">
          <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
            Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem plugg
            dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
          </p>
        </div>
        <!-- /.col -->
        <div class="col-xs-6">
          <p class="lead">Összesítés <?php echo date('Y-m-d') ?> óta</p>

          <div class="table-responsive">
            <table class="table">
              <tbody>
              <tr>
                <th>Sikeresség aránya</th>
                <td>44%</td>
              </tr>
              <tr>
                <th style="color: green;">Megfelelt</th>
                <td style="color: green;">1</td>
              </tr>
              <tr>
                <th style="color: red;">Megbukott:</th>
                <td style="color: red;">4</td>
              </tr>
              <tr>
                <th style="width:50%">Összesen:</th>
                <td><?php echo count($tests) ?> teszt</td>
              </tr>
            </tbody></table>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- this row will not appear when printing -->
      <div class="row no-print">
        <div class="col-xs-12">
          <button type="button" class="btn btn-primary pull-right" style="margin-right: 5px;">
            <i class="fa fa-download"></i> Igazolás letöltése
          </button>
        </div>
      </div>
    </section>
@stop