 <div class="modal-body">
     @if ($currentWorkspace && $milestone)
         <div class="p-2">
             <div class="row mb-4">
                 <div class="col-md-6">
                     <div>
                         <div class="form-control-label"><b> {{ __('Milestone Title') }}</b></div>
                         <p class="mt-1">{{ $milestone->title }}</p>
                     </div>

                     <div class="form-control-label"><b>{{ __('Milestone Summary') }} </b></div>
                     <p class="mt-1">{{ $milestone->summary }}</p>

                     <div class="form-control-label"><b>{{ __('Sales manager') }} </b></div>
                     <p class="mt-1">{{ $milestone->salesManager() }}</p>
                 </div>
                 <hr class="col-md-1" style="border: none; border-left: 1px solid #33333328;">
                 <div class="col-md-5">
                     <div class="form-control-label"><b>{{ __('Tasks') }} </b></div>
                     @foreach ($milestone->tasks() as $task)
                         <p class="mt-1 pl-6"><i class="fa-solid fa-circle fa-2xs m-1"
                                 style="color: rgba(0, 0, 0, 0.384)"></i>{{ $task }}</p>
                     @endforeach
                 </div>
             </div>
         </div>
     @else
         <div class="container mt-5">
             <div class="card">
                 <div class="card-body p-4">
                     <div class="page-error">
                         <div class="page-inner">
                             <h1>404</h1>
                             <div class="page-description">
                                 {{ __('Page Not Found') }}
                             </div>
                             <div class="page-search">
                                 <p class="text-muted mt-3">
                                     {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.") }}
                                 </p>
                                 <div class="mt-3">
                                     <a class="btn-return-home badge-blue" href="{{ route('home') }}"><i
                                             class="fas fa-reply"></i> {{ __('Return Home') }}</a>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     @endif
 </div>
