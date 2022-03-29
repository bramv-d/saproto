@extends('website.layouts.redesign.generic')

@section('page-title')
    Edit {{ $photos->album_title }} ({{ date('M j, Y', $photos->album_date) }})
@endsection

@section('container')

    @isset($photos->event)
        <a class="btn btn-info btn-block mb-3"
           href="{{ route('event::show', ['id'=>$photos->event->getPublicId()]) }}">
            This album is linked to the event {{ $photos->event->title }}, click here to go to the event.
        </a>
    @endisset

    <div class="row">
        <div class="col-lg-3">
            @if($photos->published)
                @can('publishalbums')
                    <a class="btn btn-warning text-white btn-block mb-3"
                       href="{{ route('photo::admin::unpublish', ['id'=>$photos->album_id]) }}">
                        This album is published so editing is limited, click here to unpublish the album.
                    </a>
                @else
                    <span class="btn btn-warning text-white btn-block mb-3 cursor-default">
                        This album is published so editing is limited, ask a Protography admin to unpublish it if you wish to make changed.
                    </span>
                @endcan
            @else
                @can('publishalbums')
                    <a class="btn btn-danger text-white btn-block mb-3" href="{{ route('photo::admin::publish', ['id'=>$photos->album_id]) }}">
                        This album is not yet published, click here to publish the album.
                    </a>
                @else
                    <span class="btn btn-warning text-white btn-block mb-3 cursor-default">
                This album is not yet published, ask a Protography admin to publish it.
            </span>
                @endcan
            @endif

            <div class="card mb-3">

                @if(Auth::user()->can('publishalbums') || (Auth::user()->can('protography') && !$photos->published))
                    <div class="card-header bg-dark text-white text-center">
                        Edit album
                    </div>

                    <form method="post">
                        {{ csrf_field() }}
                        <div class="card-body">
                            <div class="form-group">
                                <label for="album">Album name:</label>
                                <input required type="text" id="album" name="album" class="form-control"
                                       value="{{ $photos->album_title }}">
                            </div>
                            @include('website.layouts.macros.datetimepicker', [
                                'name' => 'date',
                                'label' => 'Album date:',
                                'placeholder' => date($photos->album_date),
                                'format' => 'date'
                            ])
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="private"
                                       name="private" {{ $photos->private ? "checked" : "" }}>
                                <label class="form-check-label" for="private">Private album</label>
                            </div>
                        </div>

                        <div class="card-footer">
                            <input type="submit" class="btn btn-success btn-block" value="Save">
                            <button type="button" class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#delete-modal">
                                Delete Album
                            </button>
                        </div>

                    </form>
                @else
                    <div class="card-header bg-dark text-white text-center">
                        Edit album
                    </div>

                    <div class="card-body">
                        <b>Album name:</b> {{ $photos->album_title }}<br>
                        <b>Album date:</b> {{ date('d-m-Y', $photos->album_date) }}<br>
                        <b>Private album:</b> <i class="fa fa-{{ $photos->private ? "check" : "times"}}"></i>
                    </div>
                @endif
            </div>

            <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Delete Album</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            This will delete the album and all the photos inside.<br>
                            Are you sure you want to delete the album?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <a class="btn btn-danger" href="{{ route('photo::admin::delete', ['id' => $photos->album_id]) }}">
                                Delete Album
                            </a>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card mb-3">

                <div class="card-header bg-dark text-white text-center">
                    Thumbnail
                </div>

                @isset($photos->thumb)
                    <div class="card-body" style="height: 300px; background: url({{ $photos->thumb }}) no-repeat center; background-size: cover;"></div>
                @else
                    <div class="card-body d-flex opacity-25" style="height: 300px;">
                        <div class="text-center m-auto">
                            <i class="fa fa-image fa-5x"></i>
                            <p>No thumbnail set</p>
                        </div>
                    </div>
                @endisset

            </div>
        </div>

        <div class="col-lg-9">
            <div class="card mb-3">

                <div class="card-header bg-dark text-white text-center">
                    Add photos
                </div>
                @if(!$photos->published)
                    <div class="card-body">
                        <div id="error-bar" class="alert alert-danger d-none" role="alert">
                            <p>The following files failed to upload:</p>
                            <ul></ul>
                        </div>
                        <div id="upload-view" class="row position-relative"></div>
                        <div id="droparea" class="d-flex opacity-25 border border-2 border-light rounded-3" style="height: 200px">
                            <div id="droparea-content" class="text-center m-auto pointer-events-none">
                                <i class="fa fa-images fa-5x mt-2"></i>
                                <p>
                                    <span>Drop photos to upload</span>
                                    <span id="droparea-loader" class="spinner-border spinner-border-sm ms-1 d-none" role="status"></span>
                                </p>
                            </div>
                        </div>

                    </div>
                @else
                    <div class="card-footer text-center">
                        Uploading is disabled for published albums, unpublish the album to upload extra photos.
                    </div>
                @endif
            </div>


            <div class="card mb-3">
                <form method="POST" action="{{ route('photo::admin::action', ['id' => $photos->album_id]) }}">
                    {{ csrf_field() }}

                    <div class="card-header bg-dark text-white text-center">
                        {{ $photos->album_title }} ({{ date('M j, Y', $photos->album_date) }})
                    </div>

                    <div class="card-body">
                        @if(!$photos->published || Auth::user()->can('publishalbums'))
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="btn-group" role="group" aria-label="Toolbar">
                                        @php
                                            $attr = $photos->published ?
                                            'type=button data-bs-toggle=modal data-bs-target=#published-modal' :
                                            'type=submit'
                                        @endphp
                                        <button {{ $attr }} name="action" value="remove" class="btn btn-danger">
                                            <i class="fa fa-trash"></i> Remove
                                        </button>
                                        <button {{ $attr }} name="action" value="thumbnail" class="btn btn-success">
                                            <i class="fa fa-image"></i> Set thumbnail
                                        </button>
                                        <button {{ $attr }} name="action" value="private" class="btn btn-warning">
                                            <i class="fa fa-eye"></i> Toggle private
                                        </button>

                                    </div>

                                    <div class="modal fade" id="published-modal" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Perform action</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    This album has already been published. Are you sure you want perform this action?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <span id="confirm-button"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div id="photo-view" class="row">

                            @foreach($photos->photos as $key => $photo)

                                @include('website.layouts.macros.selectablephoto', ['photo' => $photo])

                            @endforeach

                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection

@push('javascript')

    <script async type="text/javascript" nonce="{{ csp_nonce() }}">
        let fileId = 1
        let uploadRunning = false
        let dropArea = document.getElementById('droparea')

        document.getElementById('published-modal').addEventListener('show.bs.modal', e => {
            const footer = document.querySelector('#published-modal .modal-footer')
            const btn = e.relatedTarget.cloneNode(true)
            btn.type = 'submit'
            footer.replaceChild(btn, footer.lastChild)
        })

        if (dropArea && window.File && window.FileReader && window.FileList && window.Blob) {
            dropArea.addEventListener('dragover', e => {
                e.stopPropagation()
                e.preventDefault()
                dropArea.classList.remove('opacity-25')
                e.dataTransfer.dropEffect = 'move'
            })
            dropArea.addEventListener('dragleave', e => {
                e.stopPropagation()
                e.preventDefault()
                dropArea.classList.add('opacity-25')
            })
            window.addEventListener('drop', dropFiles)
        }

        function dropFiles(e) {
            e.stopPropagation()
            e.preventDefault()
            dropArea.classList.add('opacity-25')

            let files = e.dataTransfer.files
            if (files.length) {
                let fileQueue = []
                for (const file of files) {
                    if (['image/png', 'image/jpg', 'image/jpeg'].includes(file.type)) {
                        let fr = new FileReader()
                        fr.onload = async () => {
                            file.id = fileId++
                            fileQueue.push(file)
                            await uploadFiles(fileQueue)
                        }
                        fr.readAsDataURL(file)
                    }
                }
            }
        }

        async function uploadFiles(fileQueue) {
            while (fileQueue.length) {
                let file = fileQueue.shift()
                let formData = new FormData()
                formData.append('file', file)
                toggleRunning()
                await post('{{ route('photo::admin::upload', ['id' => $photos->album_id]) }}', formData)
                .then(data => {
                    if (data === 'ERROR') return uploadError(file)
                    document.getElementById('photo-view').innerHTML += res.data
                    document.getElementById('error-bar').classList.add('d-none')
                    document.querySelector('#error-bar ul').innerHTML = ''
                    toggleRunning()
                })
                .catch(err => {
                    console.error(err)
                    uploadError(file)
                    toggleRunning()
                })
            }
        }

        function toggleRunning() {
            uploadRunning = !uploadRunning
            const loader = document.getElementById('droparea-loader')
            loader.classList.toggle('d-none')
        }

        function uploadError(file) {
            document.getElementById('error-bar').classList.remove('d-none')
            document.querySelector('#error-bar ul').innerHTML += `<li> ${file.name} </li>`
        }
    </script>

@endpush
