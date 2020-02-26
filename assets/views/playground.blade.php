@extends('laravel-pdf-tinker::layouts.master', [
    'pageTitle' => 'PDF Viewer',
    'pageDescription' => 'Test your HTML PDF template using PHP PDF libraries',
    'pageWidth' => 'full'
])

@section('content')

    {{-- Show error toast when PDF couldn't be generated --}}
    <div class="position-absolute w-100 h-100 p-4">
        <div class="d-flex w-100 align-items-start">
            <div aria-live="polite" aria-atomic="true" class="d-flex justify-content-end align-items-start" style="z-index: 5">
                <div id="error-alert" role="alert" aria-live="assertive" aria-atomic="true" class="toast" data-autohide="false">
                    <div class="toast-header">
                        <svg class="bd-placeholder-img rounded mr-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img"><rect fill="#ff0000" width="100%" height="100%"></rect></svg>
                        <strong class="mr-auto re" style="color: red;">Error</strong>
                        <small>just now</small>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="toast-body"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row playground">
        <div class="col-md-6 col-sm-12 p-0">
            <div class="card">
                <div class="card-body">
                    <form>
                        @csrf
                        <nav>
                            <div class="nav nav-tabs nav-justified" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-html-tab" data-toggle="tab" href="#nav-html"
                                   role="tab" aria-controls="nav-home" aria-selected="true">
                                    HTML
                                </a>
                                <a class="nav-item nav-link" id="nav-config-tab" data-toggle="tab" href="#nav-config"
                                   role="tab" aria-controls="nav-profile" aria-selected="false">
                                    Configuration
                                </a>
                            </div>
                        </nav>

                        <div class="tab-content mt-3" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-html" role="tabpanel"
                                 aria-labelledby="nav-html-tab">
                                <div class="form-group">
                                    <textarea
                                        class="form-control shadow"
                                        name="html"
                                        id="code"
                                    >
                                        @include('laravel-pdf-tinker::example-pdf-template')
                                    </textarea>
                                </div>

                                <div class="progress" style="visibility: hidden">
                                    <div
                                            id="progress-bar"
                                            class="progress-bar bg-success"
                                            role="progressbar"
                                            aria-valuenow="0"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                            style="width: 0;"
                                    ></div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-config" role="tabpanel" aria-labelledby="nav-config-tab">
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <div class="form-group">
                                            <label for="driver">Driver</label>
                                            <select class="form-control" name="driver" id="driver">
                                                @forelse($drivers as $driver)
                                                    <option value="{{ $driver }}">{{ $driver }}</option>
                                                @empty
                                                    <option value="" disabled selected>No drivers</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="options">Options</label>
                                            <textarea class="form-control" name="options" rows="15" id="options"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-12 p-0">
            <object id="preview" type="application/pdf"></object>
        </div>
    </div>
@endsection

@push('navbar_actions')
    {{-- The Portrait/landscape mode inside the menu bar--}}
    <form>
        <ul class="radio-tiles">
            <li>
                <input type="radio" name="mode" id="mode-portrait" value="portrait" checked />
                <label for="mode-portrait">
                    <span style="color: white;">
                        <i class="fas fa-tablet-alt"></i>
                    </span>
                </label>
            </li>
            <li>
                <input type="radio" name="mode" id="mode-landscape" value="landscape"/>
                <label for="mode-landscape">
                    <span style="color: white;">
                        <i class="fas fa-tablet-alt fa-rotate-90"></i>
                    </span>
                </label>
            </li>
        </ul>
    </form>
@endpush

@push('js')
    <script>
        let progressBarElement;
        let previewElement;
        let updateTimeout = null;
        let defaultOptions = {!! json_encode(config('laravel-pdf-tinker.default_driver_options')) !!};
        let codeEditor = null;

        $(document).ready(() => {
            initCodeEditor();
            progressBarElement = $('#progress-bar');
            previewElement = $('#preview');

            $('form').on('change', () => {
                updateView();
            });

            $('#driver').on('change', function() {
                let options = defaultOptions[$(this).val()];

                // Set driver options to empty object when there are no options in config
                if (!options) {
                    options = {}
                }

                $('#options').val(JSON.stringify(options, null, 2));
            }).change();

            previewElement[0].addEventListener('load', () =>  {
                setProgressBarValue(100);
                hideProgressBar();
            });
        });

        function initCodeEditor() {
            codeEditor = CodeMirror.fromTextArea($('#code')[0], {
                mode: 'htmlmixed',
                lineNumbers: true,
            });

            codeEditor.on('keyup', () => {
                $('#code').val(codeEditor.getValue());
                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(updateView, 750);
            });
        }

        function updateView() {
            showProgressBar();
            setProgressBarValue(0);
            setProgressBarValue(25);
            setProgressBarValue(50);

            formatCode();

            $.ajax({
                url: "{{ route('vendor.laravel-pdf-tinker.preview') }}",
                method: 'POST',
                data: $('form').serialize(),
                success: (data) => {
                    setProgressBarValue(75);
                    previewElement.attr('data', data.url);
                    $('#error-alert').toast('hide')
                },
                error: ({ responseJSON }) => {
                    $('#error-alert').find('.toast-body').text(responseJSON.message)
                    $('#error-alert').toast('show')
                }
            });
        }

        function formatCode() {
            let previousLocation = codeEditor.getCursor();

            // Select all text
            codeEditor.setSelection({
                'line': codeEditor.firstLine(),
                'ch':0,
                'sticky':null
            }, {
                'line': codeEditor.lastLine(),
                'ch':0,
                'sticky':null
            }, {
                scroll: false
            });

            // Auto indent the selection
            codeEditor.execCommand('indentAuto');

            // Select previous location
            codeEditor.setSelection(previousLocation);
        }

        function showProgressBar() {
            progressBarElement.closest('.progress').css({visibility: 'visible'});
        }

        function hideProgressBar() {
            setTimeout(() => {
                progressBarElement.closest('.progress').css({visibility: 'hidden'});
                setProgressBarValue(0);
            }, 500);
        }

        function setProgressBarValue(percentage) {
            progressBarElement.css('width', percentage + '%').attr('aria-valuenow', percentage);
        }
    </script>
@endpush
