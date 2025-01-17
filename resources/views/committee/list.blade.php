@extends('website.layouts.redesign.generic')

@section('page-title')
    Committees
@endsection

@section('container')

    <div class="card mb-3">

        <div class="card-body">

            <div class="row" id="committee__list">

                @foreach($data as $key => $committee)

                    <div class="col-lg-2 col-lg-3 col-md-4 col-sm-6">

                        @include('committee.include.committee_block', ['committee' => $committee, 'photo_pop'=>false])

                    </div>

                @endforeach

            </div>

        </div>

    </div>

@endsection