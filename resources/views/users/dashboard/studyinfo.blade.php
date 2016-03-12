<div class="panel panel-default">
    <div class="panel-heading">
        <strong>Your study history</strong>

        <div class="btn-group pull-right" role="group">
            <a type="button" class="btn btn-primary btn-xs"
               href="{{ route('user::study::add', ['id' => $user->id]) }}">Add another study</a>
        </div>
    </div>
    <div class="panel-body">

        @if(count($user->studies) == 0)
            <p style="text-align: center;">
                <strong>
                    No studies registered for this user.
                </strong>
            </p>
        @else
            <div class="row">
                @foreach($user->studies as $study)
                    <div class="col-md-6 col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-body">

                                <p style="text-align: center">

                                    <strong>{{ $study->name }}</strong><br>
                                    {{ $study->faculty }}

                                    <br>

                                    @if($study->pivot->till == null)
                                        Since {{ date('d-m-Y',strtotime($study->pivot->created_at)) }}
                                    @else
                                        Between {{ date('d-m-Y',strtotime($study->pivot->created_at)) }}
                                        and {{ date('d-m-Y',strtotime($study->pivot->till)) }}
                                    @endif

                                </p>

                            </div>

                            <div class="panel-footer">

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="btn-group-justified btn-group" role="group">
                                            <a type="button" class="btn btn-default"
                                               href="{{ route("user::study::edit", ["id" => $user->id, "study_id" => $study->id]) }}"><i
                                                        class="fa fa-pencil"></i></a>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <form method="POST"
                                              action="{{ route('user::study::delete', ['study_id' => $study->id, 'id' => $user->id]) }}">
                                            {!! csrf_field() !!}
                                            <div class="btn-group btn-group-justified" role="group">
                                                <div class="btn-group" role="group">
                                                    <button type="submit" class="btn btn-danger"><i
                                                                class="fa fa-trash-o"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <hr>
        <p style="text-align: center;">
            Members are able to see your study history.
        </p>
    </div>
</div>