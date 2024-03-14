<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    {{-- <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div> --}}

    <a href="{{ route('login.google') }}" class="btn btn-primary">Login with Google</a>


    <div class="container">
        <div class="card ">
            <div class="card-header">
              Upload Files
            </div>
            <div class="card-body">
                <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Name</label>
                        <input name="file_name" type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" required>
                      </div>
                      <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">File Upload</label>
                        <input name="file" type="file" class="form-control" id="exampleInputPassword1" required>
                      </div>
                      <button name="file" type="submit" class="btn btn-success">Submit</button>
                </form>


                 <h1>File</h1>
                 <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                      <tr>
                        <th scope="row">{{$loop->index+1}}</th>
                        <td>{{$file->file_name}}</td>
                        <td>
                            <a href="/files/{{$file->id}}" class="btn btn-primary btn-sm">Download</a>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
          </div>
        </div>
</x-app-layout>
