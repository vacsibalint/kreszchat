@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
     <!-- <h1>@KreszBOT - Statisztika</h1> -->
@stop

@section('content')
<section class="content">

      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo $user->profilePic ?>" alt="User profile picture">

              <h3 class="profile-username text-center"><?php echo $user->lastName . ' ' . $user->firstName ?></h3>

              <p class="text-muted text-center">
                #<?php echo $userId ?>
                <br>
                (<?php echo $user->email ?>)
              </p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Regisztráció dátuma</b> <a class="pull-right"><?php echo $user->created_at ?></a>
                </li>
                <li class="list-group-item">
                  <b>Befejezett vizsgák</b> <a class="pull-right"><?php echo count($tests);?> db</a>
                </li>
              </ul>

              <a href="#" class="btn btn-danger btn-block"><b>Profilom törlése</b></a>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Rólam</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <strong><i class="fa fa-book margin-r-5"></i> Iskola</strong>

              <p class="text-muted">
                Class Autósiskola
              </p>

              <hr>

              <strong><i class="fa fa-map-marker margin-r-5"></i> Vizsga helyszíne</strong>

              <p class="text-muted">Miskolc</p>

              <hr>

              <strong><i class="fa fa-car-crash margin-r-5"></i> Kategóriák</strong>

              <p>
                <span class="label label-danger">A</span>
                <span class="label label-success">B</span>
                <span class="label label-info">C</span>
                <span class="label label-warning">D</span>
                <span class="label label-primary">E</span>
              </p>

              <hr>

              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#timeline" data-toggle="tab">Teszteredmények</a></li>
              <li><a href="#settings" data-toggle="tab">Adataim</a></li>
            </ul>
            <div class="tab-content">
              <!-- /.tab-pane -->
              <div class="active tab-pane" id="timeline">
                <!-- The timeline -->
                <ul class="timeline timeline-inverse">
                  <?php foreach($tests as $test){ ?>

                  <li>
                    <i class="<?php echo ($test->result == 1) ? 'fa fa-check bg-green' : 'fa fa-times bg-red'; ?>"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i>  <?php echo $test->createdAt; ?></span>

                      <h3 class="timeline-header"><?php echo ($test->result == 1) ? 'Sikeres' : 'Sikertelen'; ?> <?php echo $test->category ?> vizsga</h3>

                      <div class="timeline-body">
                        Elért pontszám: 55/75
                      </div>
                      <div class="timeline-footer">
                        <a class="btn btn-primary btn-xs">Vizsga részletei</a>
                      </div>
                    </div>
                  </li>
                  <?php } ?>

                  <li class="time-label">
                      <span class="bg-green">
                        <?php echo $user->created_at ?> 
                      </span>
                  </li>

                  <li>
                    <i class="fa fa-user-plus bg-blue"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> <?php echo $user->created_at ?></span>

                      <h3 class="timeline-header">Regisztráció</h3>

                      <div class="timeline-body">
                        Regisztráltál a KreszBOT alkalmazásba
                      </div>
                    </div>
                  </li>

                  <!-- END timeline item -->
                  <li>
                    <i class="fa fa-clock-o bg-gray"></i>
                  </li>
                </ul>
              </div>
              <!-- /.tab-pane -->

              <div class="tab-pane" id="settings">
                <form class="form-horizontal">
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Név</label>

                    <div class="col-sm-10">
                      <input type="name" class="form-control" id="inputName" readonly value="<?php echo $user->firstName . ' ' . $user->lastName ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="inputName" placeholder="Email" value="<?php echo $user->email ?>">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="inputExperience" class="col-sm-2 control-label">Rólam</label>

                    <div class="col-sm-10">
                      <textarea class="form-control" id="inputExperience" placeholder="Írj magadról néhány dolgot.."></textarea>
                    </div>
                  </div>

                  <!-- <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                        </label>
                      </div>
                    </div>
                  </div> -->
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-success">Mentés</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
@stop